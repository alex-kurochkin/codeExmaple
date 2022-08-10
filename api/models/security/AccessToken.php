<?php

declare(strict_types=1);

namespace api\models\security;

use api\models\Config;
use api\models\exception\UnauthorizedException;
use api\models\Model;
use api\models\security\ars\AccessTokenAr;
use DateTime;
use RuntimeException;
use stdClass;

class AccessToken extends Model
{
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 2;

    public int $id;
    public int $userId;
    public string $token;
    public ?string $code2fa;
    public int $status;
    public ?stdClass $clientInfo;
    public string $createdAt;
    public string $activeAt;

    public static function resetCode2fa(string $accessToken): void
    {
        AccessTokenAr::resetCode2Fa($accessToken);
    }

    public static function resetAll(int $userId): void
    {
        AccessTokenAr::updateStatusByUser($userId, self::STATUS_INACTIVE);
    }

    public function add(int $userId, string $token, ?string $code2fa, stdClass $clientInfo): void
    {
        $this->userId = $userId;
        $this->token = $token;
        $this->code2fa = $code2fa;
        $this->status = self::STATUS_ACTIVE;
        $this->clientInfo = $clientInfo;
        $this->save();
    }

    public function delete(string $token): void
    {
        $t = $this->getActiveByToken($token);
        $t->status = self::STATUS_INACTIVE;
        $t->update();
    }

    public function getActiveByToken(string $tokenString): self
    {
        $token = AccessTokenAr::findByTokenAndStatus($tokenString, self::STATUS_ACTIVE);

        if (!$token) {
            throw new UnauthorizedException('User not found');
        }

        $activeAtDT = new DateTime($token->activeAt);

        if (Config::getUserLoginIdleLimit() <= time() - $activeAtDT->format('U')) {
            throw new UnauthorizedException('User login idle expired');
        }

        $token->activeAt = date('Y-m-d H:i:s');
        $token->update();

        return $this->importOne($token);
    }

    public function getActiveByCode2fa(string $code2fa): self
    {
        $token = AccessTokenAr::findByCode2faAndStatus($code2fa, self::STATUS_ACTIVE);

        if (!$token) {
            throw new UnauthorizedException('User not found by code2fa');
        }

        $createdAtDT = new DateTime($token->createdAt);

        if (Config::getUser2faIdleLimit() <= time() - $createdAtDT->format('U')) {
            throw new UnauthorizedException('User 2fa code idle expired');
        }

        return $this->importOne($token);
    }

    public function getLastByUserId(int $userId): self
    {
        $tokens = $this->getAllByUserId($userId);

        if (!$tokens) {
            throw new RuntimeException('AccessToken not found for user id: ' . $userId);
        }

        return array_pop($tokens);
    }

    private function getAllByUserId(int $userId): array
    {
        $tokens = AccessTokenAr::findAllTokensByUserId($userId);

        if (!$tokens) {
            return [];
        }

        return $this->importMany($tokens);
    }

    public function getUsersActivity(int $timeIndent = null, int $limit = 20, ?int $userId = null): array
    {
        return $this->importMany(
            AccessTokenAr::findUsersActivity($timeIndent, $limit, $userId, self::STATUS_ACTIVE)
        );
    }
}