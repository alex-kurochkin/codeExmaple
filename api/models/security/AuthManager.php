<?php

declare(strict_types=1);

namespace api\models\security;

use Yii;

class AuthManager
{
    public static function assignUserRole(int $userId): void
    {
        $auth = Yii::$app->authManager;
        $userRole = $auth->getRole('user');
        $auth->assign($userRole, $userId);
    }

    public static function assignAdminRole(int $userId): void
    {
        $auth = Yii::$app->authManager;
        $adminRole = $auth->getRole('admin');
        $auth->assign($adminRole, $userId);

        $userRole = $auth->getRole('user');
        $auth->revoke($userRole, $userId);
    }

    public static function revokeAdminRole($userId): void
    {
        $auth = Yii::$app->authManager;
        $adminRole = $auth->getRole('admin');
        $auth->revoke($adminRole, $userId);

        $userRole = $auth->getRole('user');
        $auth->assign($userRole, $userId);
    }

    public static function is(int $userId, string $role): bool
    {
        // because su is admin too
        if ('admin' === $role) {
            return self::isAdmin($userId);
        }

        $roles = Yii::$app->authManager->getRolesByUser($userId);

        return array_key_exists($role, $roles);
    }

    public static function isAdmin(int $userId): bool
    {
        $roles = Yii::$app->authManager->getRolesByUser($userId);

        return array_key_exists('admin', $roles) || array_key_exists('su', $roles);
    }

    /**
     * Detect for user role "user"
     * @param int $userId
     * @return bool
     */
    public static function isUser(int $userId): bool
    {
        $roles = Yii::$app->authManager->getRolesByUser($userId);

        return array_key_exists('user', $roles);
    }

    /**
     * Detect for user role is exactly "su"
     * @param int $userId
     * @return bool
     */
    public static function isSu(int $userId): bool
    {
        $roles = Yii::$app->authManager->getRolesByUser($userId);

        return array_key_exists('su', $roles);
    }
}