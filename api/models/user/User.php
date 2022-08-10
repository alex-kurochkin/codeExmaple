<?php

declare(strict_types=1);

namespace api\models\user;

use api\models\common\UserOwnedInterface;
use api\models\ErrorCode;
use api\models\exception\ForbiddenException;
use api\models\exception\UnauthorizedException;
use api\models\Model;
use api\models\Security;
use api\models\security\AccessToken;
use api\models\security\AuthManager;
use api\models\security\HttpBearerAuth;
use api\models\user\ars\UserAr;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\IdentityInterface;

class User extends Model implements IdentityInterface, UserOwnedInterface
{
    public const STATUS_DELETED = 0;
    public const STATUS_INACTIVE = 9;
    public const STATUS_ACTIVE = 10;
    public int $id;
    public string $username;
    public string $email;
    public int $status;
    public ?string $phone;
    public int $balance;
    public bool $phoneVerified;
    public ?int $phoneCodeSentAt;
    public int $phoneCodeSentCount;
    public bool $forceEnableProxyBuy;
    public int $testCount;
    public ?string $language;
    public int $createdAt;
    public int $updatedAt;
    public string $datetime;
    public string $passwordHash;
    public ?string $passwordResetToken;
    public ?string $verificationToken;
    public ?string $accessToken;
    public ?string $verifyPhoneCode;
    public bool $use2fa = false;
    public ?string $code2fa = null;
    public ?string $bitcoinPaymentAddress = null;
    public int $spentMoney = 0;
    public array $proxies = [];
    /** @var string Used to send auto-generated password to user via email. */
    public string $generatedPassword;

    public static function getStatuses(): array
    {
        return [
            self::STATUS_DELETED => 'deleted',
            self::STATUS_INACTIVE => 'inactive',
            self::STATUS_ACTIVE => 'active',
        ];
    }

    public static function getByEmail(string $email): self
    {
        $user = UserAr::findByEmail($email);

        if (!$user) {
            throw new UnauthorizedException('User not found by email: ' . $email);
        }

        return (new self)->importOne($user);
    }

    public static function getActiveByEmail(string $email): ?self
    {
        $user = UserAr::findByEmailAndStatus($email, self::STATUS_ACTIVE);

        if (!$user) {
            return null;
        }

        return (new self)->importOne($user);
    }

    public static function getActiveByCode2fa(string $code2fa): ?self
    {
        $accessToken = (new AccessToken())->getActiveByCode2fa($code2fa);
        $user = UserAr::findByIdAndStatus($accessToken->userId, self::STATUS_ACTIVE);

        if (!$user) {
            return null;
        }

        $u = (new self)->importOne($user);
        $u->accessToken = $accessToken->token;

        return $u;
    }

    public static function getActiveByBitcoinPaymentAddress(string $bitcoinPaymentAddress): ?self
    {
        $user = UserAr::findByBitcoinPaymentAddressAndStatus($bitcoinPaymentAddress, self::STATUS_ACTIVE);

        if (!$user) {
            return null;
        }

        return (new self)->importOne($user);
    }

    public static function getSummaryUsersBalance(string $role): int
    {
        return UserAr::calculateSummaryUsersBalance($role);
    }

    public static function getByVerificationToken(string $token): self
    {
        $user = UserAr::findByVerificationToken($token);

        if (!$user) {
            throw new UnauthorizedException('User not found for email verification by token ' . $token);
        }

        return (new self)->importOne($user);
    }

    public static function getByPasswordResetToken(string $token): ?self
    {
        $user = UserAr::findByPasswordResetToken($token);

        if (!$user) {
            return null;
        }

        return (new self)->importOne($user);
    }

    public static function getAllCount(): int
    {
        return UserAr::countAllRecords();
    }

    /**
     * Hack for ResultQiwiAction
     * ResultQiwiAction calls directly by Qiwi but we need to accept or reject payment
     * @throws InvalidConfigException
     */
    public static function setAdminCurrentUser(): void
    {
        $user = self::get(1);
        Yii::$app->set('user', $user);
    }

    /**
     * Can current user access to view, modify and etc. with some object?
     * Can current user do some operation as other user?
     *  Call: User::hasAccess($someItem->userId);
     *  or User::hasAccess($userId);
     *
     *  Do not use if() construction unnecessarily, just call this method.
     *  In case of user does not have the right, this method throws UnauthorizedException by himself
     *
     * @param int $ownerId
     * @return bool
     * @throws ForbiddenException
     */
    public static function hasAccess(int $ownerId): bool
    {
        $userId = self::getCurrentUserId();

        if (AuthManager::isAdmin($userId) || AuthManager::isSu($userId)) {
            return true;
        }

        if ($userId === $ownerId) {
            return true;
        }

        throw new ForbiddenException('Permission Denied');
    }

    public static function getCurrentUserId(): ?int
    {
        return Yii::$app->user->id;
    }

    public static function hasAdminAccess(): bool
    {
        $user = Yii::$app->getUser();

        if (AuthManager::isAdmin($user->id) || AuthManager::isSu($user->id)) {
            return true;
        }

        throw new ForbiddenException('Access Denied: You must have administrator level access.');
    }

    public static function findIdentity($id): ?self
    {
        $user = UserAr::findByIdAndStatus($id, self::STATUS_ACTIVE);

        if (!$user) {
            throw new UnauthorizedException('User not found by id: ' . $id);
        }

        return (new self())->importOne($user);
    }

    public static function findIdentityByAccessToken($token, $type = null): ?self
    {
        $accessToken = (new AccessToken())->getActiveByToken($token);

        try {
            $user = self::get($accessToken->userId);
        } catch (Exception $e) {
            if (ErrorCode::NOT_FOUND === $e->getCode()) {
                throw new UnauthorizedException('User not found by access token: ' . $token);
            }

            throw $e;
        }

        $user->accessToken = $accessToken->token;

        return $user;
    }

    public function resetCode2fa(): void
    {
        AccessToken::resetCode2fa($this->getAccessToken());
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getAll(): array
    {
        return $this->importMany(UserAr::find()->all());
    }

    /**
     * Search user by name or email
     * @param string $searchUserString
     * @param string $role
     * @param int $start
     * @param int $count
     * @param string $orderBy
     * @param string $orderDirection
     * @return array
     */
    public function search(
        string $searchUserString,
        string $role,
        int $start,
        int $count,
        string $orderBy,
        string $orderDirection
    ): array {
        $searchType = $this->getSearchType($searchUserString);

        return $this->importMany(
            UserAr::search(
                $searchType,
                $searchUserString,
                $role,
                $start,
                $count,
                $orderBy,
                $orderDirection
            )
        );
    }

    /**
     * @param string $searchUserString
     * @return string "username", "email" or empty string
     */
    private function getSearchType(string $searchUserString): string
    {
        $searchType = '';
        if ($searchUserString) {
            $searchType = 'username';
            if (filter_var($searchUserString, FILTER_VALIDATE_EMAIL)) {
                $searchType = 'email';
            }
        }

        return $searchType;
    }

    public function searchFullCount(string $searchUserString): int
    {
        $searchType = $this->getSearchType($searchUserString);
        return UserAr::searchFullCount($searchType, $searchUserString);
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;
        return $this;
    }

    public function setUse2fa(bool $use2fa): self
    {
        $this->use2fa = $use2fa;
        return $this;
    }

    public function increaseBalance(int $amount): self
    {
        $this->balance += $amount;
        return $this;
    }

    public function decreaseBalance(int $amount): self
    {
        $this->balance -= $amount;
        $this->spentMoney += $amount;
        $this->update();

        return $this;
    }

    public function isPhoneVerified(): bool
    {
        return $this->phoneVerified;
    }

    public function setPhoneVerified(): self
    {
        $this->verifyPhoneCode = null;
        $this->phoneVerified = true;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->id;
    }

    public function getTestCount(): int
    {
        return $this->testCount;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * @return int
     */
    public function getPhoneCodeSentAt(): int
    {
        return $this->phoneCodeSentAt;
    }

    /**
     * @return int
     */
    public function getPhoneCodeSentCount(): int
    {
        return $this->phoneCodeSentCount;
    }

    public function resetPhoneCodeSentCount(): void
    {
        $this->phoneCodeSentCount = 0;
    }

    public function increaseTestCount(): self
    {
        $this->testCount++;
        return $this;
    }

    public function setForceEnableProxyBuy(bool $enabled): self
    {
        $this->forceEnableProxyBuy = $enabled;
        return $this;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function setVerifyPhoneCode(string $code, string $phone): self
    {
        $this->phoneCodeSentAt = time();
        $this->phoneCodeSentCount++;
        $this->verifyPhoneCode = $code;
        $this->phone = $phone;
        return $this;
    }

    public function setPasswordResetToken($token): self
    {
        $this->passwordResetToken = $token;
        return $this;
    }

    public function logout(): self
    {
        $this->accessToken = null;
        $currentToken = HttpBearerAuth::getBearerToken();
        (new AccessToken())->delete($currentToken);

        return $this;
    }

    public function setActive(): self
    {
        $this->verificationToken = null;
        $this->status = self::STATUS_ACTIVE;
        return $this;
    }

    public function setInactive(): self
    {
        $this->status = self::STATUS_INACTIVE;
        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->passwordHash = Security::generatePasswordHash($password);
        return $this;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
        $this->update();
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
        $this->update();
    }

    public function removePasswordResetToken(): self
    {
        $this->passwordResetToken = null;
        return $this;
    }

    public function generateAccessToken(): self
    {
        $this->accessToken = Security::generateRandomString() . time();

        if ($this->use2fa) {
            $this->code2fa = Security::generateRandomString();
        }

        $accessToken = new AccessToken();
        $accessToken->add($this->id, $this->getAccessToken(), $this->getCode2fa(), Security::getClientInfo());

        return $this;
    }

    public function getCode2fa(): ?string
    {
        return $this->code2fa;
    }

    public function generateEmailVerificationToken(): self
    {
        $this->verificationToken = Security::generateRandomString() . '_' . time();
        return $this;
    }

    ////////// ----- ///////////

    public function getBitcoinPaymentAddress(): ?string
    {
        return $this->bitcoinPaymentAddress;
    }

    public function setBitcoinPaymentAddress(string $bitcoinPaymentAddress): self
    {
        $this->bitcoinPaymentAddress = $bitcoinPaymentAddress;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthKey(): string
    {
        return '';
    }

    public function validateAuthKey($authKey): bool
    {
        return false;
    }
}