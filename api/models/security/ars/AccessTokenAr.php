<?php

declare(strict_types=1);

namespace api\models\security\ars;

use api\models\Ar;
use api\models\security\AccessToken;
use yii\db\Expression;

/**
 * Class AccessTokenAr
 * @package api\models\security\ars
 * @property int $id [int]
 * @property int $userId [int]
 * @property string $token [varchar(42)]
 * @property bool $status [tinyint]
 * @property string $clientInfo [json]
 * @property int $createdAt [timestamp]
 * @property string $code2fa [varchar(32)]
 * @property int $activeAt [timestamp]
 */
class AccessTokenAr extends Ar
{
    protected static array $map = [
        'id' => 'id',
        'userId' => 'userId',
        'token' => 'token',
        'code2fa' => 'code2fa',
        'status' => 'status',
        'clientInfo' => 'clientInfo',
        'createdAt' => 'createdAt',
        'activeAt' => 'activeAt',
    ];

    protected static array $types = [
        'clientInfo' => 'json',
    ];

    public static function tableName(): string
    {
        return '{{%AccessToken}}';
    }

    public static function getModelName(): string
    {
        return AccessToken::class;
    }

    public static function findByTokenAndStatus(string $token, int $status): ?self
    {
        return self::find()
            ->where(['token' => $token])
            ->andWhere(['status' => $status])
            ->one();
    }

    public static function findByCode2faAndStatus(string $code2fa, int $status): ?self
    {
        return self::find()
            ->where(['code2fa' => $code2fa])
            ->andWhere(['status' => $status])
            ->one();
    }

    public static function resetCode2Fa(string $accessToken): void
    {
        self::updateAll(['code2fa' => null], ['token' => $accessToken]);
    }

    public static function findAllTokensByUserId(int $userId): array
    {
        return self::find()
            ->where(['userId' => $userId])
            ->orderBy('id')
            ->all();
    }

    public static function updateStatusByUser(int $userId, int $status): void
    {
        self::updateAll(['status' => $status], ['userId' => $userId]);
    }

    public static function findUsersActivity(int $timeIndent, ?int $limit, ?int $userId = null, int $status): array
    {
        $list = self::find()
            ->where(['status' => $status])
            ->andWhere(['<=', 'activeAt', new Expression('NOW() - INTERVAL ' . $timeIndent . ' SECOND')])
            ->orderBy(['activeAt' => SORT_DESC])
            ->limit($limit);

        if ($userId) {
            $list->andWhere(['userId' => $userId]);
        }

        return $list->all();
    }
}