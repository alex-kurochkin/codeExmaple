<?php

declare(strict_types=1);

namespace api\models\user\ars;

use api\models\Ar;
use api\models\security\AccessToken;
use api\models\user\User;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class UserAr
 * @package api\models\admin\ars
 * @property int $id [int(11)]
 * @property string $username [varchar(255)]
 * @property string $email [varchar(255)]
 * @property int $status [smallint(6)]
 * @property int $createdAt [int(11)]
 * @property int $updatedAt [int(11)]
 * @property string $phone [decimal(11)]
 * @property int $balance [int(11)]
 * @property string $verifyPhoneCode [char(6)]
 * @property bool $phoneVerified [tinyint(4)]
 * @property bool $testCount [tinyint(4)]
 * @property string $language [char(2)]
 * @property bool $forceEnableProxyBuy [tinyint(3)]
 * @property string $datetime [datetime] represent DATETIME format of createdAt
 * @property int $spentMoney [int(11)]
 * @property bool $use2fa [char(36)]
 * @property bool $phoneCodeSentCount [tinyint]
 * @property int $phoneCodeSentAt [int]
 * @property string $passwordHash [varchar(255)]
 * @property string $passwordResetToken [varchar(255)]
 * @property string $verificationToken [varchar(255)]
 * @property string $bitcoinPaymentAddress [varchar(42)]
 */
class UserAr extends Ar
{
    public static array $map = [
        'id' => 'id',
        'username' => 'username',
        'passwordHash' => 'passwordHash',
        'passwordResetToken' => 'passwordResetToken',
        'verificationToken' => 'verificationToken',
        'email' => 'email',
        'status' => 'status',
        'createdAt' => 'createdAt',
        'updatedAt' => 'updatedAt',
        'phone' => 'phone',
        'balance' => 'balance',
        'verifyPhoneCode' => 'verifyPhoneCode',
        'phoneVerified' => 'phoneVerified',
        'phoneCodeSentAt' => 'phoneCodeSentAt',
        'phoneCodeSentCount' => 'phoneCodeSentCount',
        'language' => 'language',
        'testCount' => 'testCount',
        'forceEnableProxyBuy' => 'forceEnableProxyBuy',
        'datetime' => 'datetime',
        'spentMoney' => 'spentMoney',
        'use2fa' => 'use2fa',
        'bitcoinPaymentAddress' => 'bitcoinPaymentAddress',
    ];
    /** @var ?AccessToken */
    public $accessToken = null;
    /** @var ?string */
    public $code2fa = null;

    public static function findByEmail(string $email): self
    {
        return self::find()->where(['email' => $email])->one();
    }

    public static function findByEmailAndStatus(string $email, int $status): self
    {
        return self::find()->where(['email' => $email, 'status' => $status])->one();
    }

    public static function findByIdAndStatus(int $userId, int $status): self
    {
        return self::findOne(['id' => $userId, 'status' => $status]);
    }

    public static function findByVerificationToken(string $token): self
    {
        return self::find()->where(['verificationToken' => $token])->one();
    }

    public static function findByPasswordResetToken(string $token): self
    {
        return self::find()->where(['passwordResetToken' => $token])->one();
    }

    public static function findByBitcoinPaymentAddressAndStatus(string $bitcoinPaymentAddress, int $status): ?self
    {
        return self::findOne(['bitcoinPaymentAddress' => $bitcoinPaymentAddress, 'status' => $status]);
    }

    public static function countAllRecords(): int
    {
        return (int)self::find()->count();
    }

    public static function getModelName(): string
    {
        return User::class;
    }

    public static function search(
        string $searchType,
        string $searchUserString,
        string $role,
        int $offset,
        int $limit,
        string $orderBy,
        string $orderDirection
    ): array {
        $orderBy = self::escapeColumnName($orderBy);
        $ascDesc = self::getOrderDirectionConst($orderDirection);

        $list = self::find()->offset($offset)->limit($limit)->orderBy([$orderBy => $ascDesc]);

        if ('username' === $searchType) {
            $list->andWhere(['like', 'username', $searchUserString . '%', false]);
        }

        if ('email' === $searchType) {
            $searchString = self::prepareEmailSearchString($searchUserString);

            $list->andWhere(['like', 'email', $searchString, false]);
        }

        if ($role) {
            $list->join('JOIN', 'auth_assignment', 'auth_assignment.user_id = ' . self::tableName() . '.id');
            $list->andWhere(['item_name' => $role]);
        }

        return $list->all();
    }

    private static function prepareEmailSearchString(string $email): string
    {
        [$username, $domain] = explode('@', $email);

        if (strpos($domain, '.')) {
            [$d2, $d1] = explode('.', $domain);
            $domain = $d2 . '%.' . $d1;
        }

        return $username . '%@' . $domain . '%';
    }

    public static function tableName(): string
    {
        return '{{%user}}';
    }

    /**
     * Number of all records as it would have been found without taking offset and limit by self::search().
     * @param string $searchType
     * @param string $searchUserString
     * @return int
     */
    public static function searchFullCount(
        string $searchType,
        string $searchUserString
    ): int {
        $list = self::find();

        if ('username' === $searchType) {
            $list
                ->where(['like', 'username', $searchUserString . '%', false])
                ->all();
        }

        if ('email' === $searchType) {
            $searchString = self::prepareEmailSearchString($searchUserString);

            $list
                ->where(['like', 'email', $searchString, false])
                ->all();
        }

        return $list->count();
    }

    public static function calculateSummaryUsersBalance(string $role): int
    {
        $find = self::find();

        if ($role) {
            $find->join('JOIN', 'auth_assignment', 'auth_assignment.user_id = ' . self::tableName() . '.id');
            $find->andwhere(['item_name' => $role]);
        }

        return (int)$find->sum('balance');
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['createdAt', 'updatedAt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updatedAt'],
                ],
                'value' => function () {
                    return time();
                },
            ],
        ];
    }

    public function beforeSave($insert): bool
    {
        $this->phoneVerified = (int)$this->phoneVerified;
        $this->forceEnableProxyBuy = (int)$this->forceEnableProxyBuy;
        $this->use2fa = (int)$this->use2fa;
        return parent::beforeSave($insert);
    }

    public function afterFind(): void
    {
        $this->phoneVerified = (bool)$this->phoneVerified;
        $this->forceEnableProxyBuy = (bool)$this->forceEnableProxyBuy;
        $this->use2fa = (bool)$this->use2fa;
        parent::afterFind();
    }
}