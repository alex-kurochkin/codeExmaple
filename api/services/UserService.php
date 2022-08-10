<?php

declare(strict_types=1);

namespace api\services;

use api\models\Config;
use api\models\Container;
use api\models\exception\UnauthorizedException;
use api\models\Security;
use api\models\security\AccessToken;
use api\models\security\AuthManager;
use api\models\sms\SmsSender;
use api\models\user\User;
use api\services\notify\user\NotifyUserEmailService;
use DateTimeImmutable;
use DateTimeInterface;
use LogicException;

class UserService
{
    public function getList(
        PackageUserService $packageService,
        string $role,
        string $searchUserString,
        int $start,
        int $count,
        string $orderBy,
        string $orderDirection
    ): array {
        $user = (new User());

        $users = $user->search(
            $searchUserString,
            $role,
            $start,
            $count,
            $orderBy,
            $orderDirection
        );

        foreach ($users as $k => $user) {
            $proxiesList = Container::invoke(
                [$packageService, 'getInProcessingList'],
                ['userId' => $user->id]
            );

            $user->proxies = $proxiesList;
        }

        $countAll = $user->searchFullCount($searchUserString);

        return ['users' => $users, 'countAll' => $countAll];
    }

    public function getUserByIdProxiesList(
        User $userModel,
        PackageUserService $proxyUserService,
        int $userId,
        int $proxyStart,
        int $proxyCount
    ): User {
        User::hasAccess($userId);

        $user = $userModel->getById($userId);
        $proxiesList = Container::invoke(
            [$proxyUserService, 'getList'],
            ['userId' => $user->id, 'proxyStart' => $proxyStart, 'proxyCount' => $proxyCount]
        );

        $user->proxies = $proxiesList;

        return $user;
    }

    public function getUserProxiesCount(PackageUserService $proxyUserService, int $userId): int
    {
        return Container::invoke(
            [$proxyUserService, 'getCountByUserId'],
            ['userId' => $userId]
        );
    }

    public function getUserById(User $userModel, PackageUserService $packageService, int $userId): User
    {
        $user = $userModel->getById($userId);
        $proxiesList = Container::invoke(
            [$packageService, 'getInProcessingList'],
            ['userId' => $user->id]
        );

        $user->proxies = $proxiesList;

        return $user;
    }

    public function setLanguage(User $userModel, int $userId, string $language): User
    {
        User::hasAccess($userId);

        $user = $userModel->getById($userId);
        $user->setLanguage($language);
        $user->update();

        return $user;
    }

    public function setUse2Fa(User $userModel, int $userId, bool $use2fa): User
    {
        User::hasAccess($userId);

        if (false === $use2fa && AuthManager::isAdmin($userId)) {
            throw new LogicException('Unable to switch off 2fa for admin');
        }

        $user = $userModel->getById($userId);
        $user->setUse2fa($use2fa);
        $user->update();

        return $user;
    }

    public function changeRole(int $targetUserId, string $roleName): bool
    {
        if (AuthManager::isSu($targetUserId)) {
            return false;
        }

        SetRoleLog::info('Set role ' . $roleName . ' to ' . $targetUserId);

        if ('admin' === $roleName) {
            if (AuthManager::isAdmin($targetUserId)) {
                throw new LogicException('User already is admin');
            }

            AuthManager::assignAdminRole($targetUserId);

            $user = User::get($targetUserId);
            $user->use2fa = true;
            $user->update();

            return true;
        }

        AuthManager::revokeAdminRole($targetUserId);

        if (!AuthManager::isUser($targetUserId)) {
            AuthManager::assignUserRole($targetUserId);
        }

        AccessToken::resetAll($targetUserId);

        return true;
    }

    /**
     * @param DateTimeInterface|null $startDate
     * @param DateTimeInterface|null $endDate
     * @param int|null $userId
     * @param string $role
     * @param int|null $start
     * @param int|null $count
     * @param string $orderBy
     * @param string $orderDirection
     * @return UserBalanceLog[]
     */
    public function getUsersBalanceLog(
        ?DateTimeInterface $startDate,
        ?DateTimeInterface $endDate,
        ?int $userId,
        string $role,
        int $start,
        int $count,
        string $orderBy,
        string $orderDirection
    ): array {
        return (new UserBalanceLog())->getLogByDates(
            $startDate,
            $endDate,
            $userId,
            $role,
            $start,
            $count,
            $orderBy,
            $orderDirection
        );
    }

    /**
     * @param int $userId
     * @return UserBalanceLog[]
     */
    public function getUserBalanceLogByUserId(int $userId): array
    {
        return (new UserBalanceLog())->getByUserId($userId);
    }

    public function getUserBalanceLogById(int $id): UserBalanceLog
    {
        return (new UserBalanceLog())->getById($id);
    }

    public function getUserBalanceLogByPid(string $pid): UserBalanceLog
    {
        return (new UserBalanceLog())->getByPid($pid);
    }

    public function updateBalanceByAdmin(
        User $userModel,
        UserBalanceLog $userBalanceLog,
        int $actorId,
        int $userId,
        int $diff,
        string $comment
    ): User {
        $user = $userModel->getById($userId);

        $balance = $user->getBalance();

        /** The diff can be negative value! */
        $balance += $diff;

        if (0 > $balance) {
            throw new LogicException('You are trying to reduce the user\'s balance to a negative value');
        }

        $user->setBalance($balance);
        $user->update();

        $userBalanceLog->add(
            $userId,
            $actorId,
            UserBalanceLog::OPERATION_ADMIN_UPDATE_USER_BALANCE,
            UserBalanceLog::RESULT_OK,
            $user->getBalance(),
            $diff,
            null,
            null,
            null,
            null,
            null,
            $comment
        );

        return $user;
    }

    public function refunds(
        User $userModel,
        UserBalanceLog $userBalanceLog,
        int $actorId,
        int $userId,
        int $diff,
        string $comment
    ): void {
        $user = $userModel->getById($userId);

        $balance = $user->getBalance();

        if (0 >= $diff) {
            throw new LogicException('The diff must be greater than zero');
        }

        $balance -= $diff;

        if (0 > $balance) {
            throw new LogicException('You are trying to reduce the user\'s balance to a negative value');
        }

        $user->setBalance($balance);
        $user->update();

        $userBalanceLog->add(
            $userId,
            $actorId,
            UserBalanceLog::OPERATION_REFUND,
            UserBalanceLog::RESULT_OK,
            $user->getBalance(),
            -$diff,
            null,
            null,
            null,
            null,
            null,
            $comment
        );
    }

    public function fastLogin(string $email, string $language): User
    {
        $password = Security::generateRandomString(8);

        $user = $this->signup($email, $password, $language);
        $user->setActive();
        $user->update();
        unset($user);

        $user = $this->login($email, $password);

        $user->generatedPassword = $password;

        NotifyUserEmailService::sendFastLogin($user);

        return $user;
    }

    public function signup(string $email, string $password, string $language): User
    {
        [$username] = explode('@', $email);

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->language = $language;
        $user->setInactive();
        $user->setPassword($password);
        $user->generateEmailVerificationToken();
        $user->datetime = (new DateTimeImmutable())->format('Y-m-d H:i:s'); // @todo rename it
        $user->save();

        $user->generateAccessToken();

        AuthManager::assignUserRole($user->id);

        if (!Config::getNeedVerifyEmail()) {
            $user->setActive();
            $user->update();

            $user->generatedPassword = '********';
            NotifyUserEmailService::sendFastLogin($user);

            return $user;
        }

        NotifyUserEmailService::sendEmailVerify($user);

        return $user;
    }

    public function login(string $email, string $password): User
    {
        $user = User::getActiveByEmail($email);

        if (!$user) {
            throw new UnauthorizedException('Active user not found');
        }

        if (!Security::validatePassword($password, $user->passwordHash)) {
            throw new LogicException('Wrong password');
        }

        $user->generateAccessToken();

        if ($user->use2fa) {
            NotifyUserEmailService::send2faLogin($user);
        }

        return $user;
    }

    public function login2fa(string $code2fa): User
    {
        $user = User::getActiveByCode2fa($code2fa);

        if (!$user) {
            throw new UnauthorizedException('Active 2fa user not found: ' . $code2fa);
        }

        $user->resetCode2fa();

        return $user;
    }

    public function resetPasswordResetToken(string $email): void
    {
        $user = User::getActiveByEmail($email);

        if (!$user) {
            throw new UnauthorizedException('Active user not found');
        }

        $resetToken = $user->passwordResetToken;

        $valid = false;
        if ($resetToken) {
            $timestamp = (int)substr($resetToken, strrpos($resetToken, '_') + 1);
            $expire = Config::getUserPasswordResetTokenExpire();
            $valid = $timestamp + $expire >= time();
        }

        if (!$valid) {
            $newToken = Security::generateRandomString() . '_' . time();
            $user->setPasswordResetToken($newToken);
            $user->update();
        }

        NotifyUserEmailService::sendPasswordResetToken($user);
    }

    public function resetPassword(string $token, string $password): void
    {
        $user = User::getByPasswordResetToken($token);

        if (!$user) {
            throw new UnauthorizedException('User not found');
        }

        $user->setPassword($password);
        $user->removePasswordResetToken();
        $user->generateAccessToken();
        $user->update();

        $this->resetAllSessions($user->id);
    }

    public function verifyEmail(string $token): User
    {
        $user = User::getByVerificationToken($token);

        $user->setActive();
        $user->update();

        $accessToken = (new AccessToken())->getLastByUserId($user->id);
        $user->accessToken = $accessToken->token;

        return $user;
    }

    public function resendVerificationEmail(string $email): void
    {
        $user = User::getByEmail($email);
        NotifyUserEmailService::sendEmailVerify($user);
    }

    public function sendVerificationPhoneCode(int $userId, string $phone): void
    {
        $user = User::get($userId);

        if ($phone === $user->phone && $user->phoneVerified) {
            throw new LogicException('Phone verified already');
        }

        if (
            $user->getPhoneCodeSentCount() >= Config::getUserSentPhoneCodeNoDelayAttempt()
            && $user->getPhoneCodeSentAt() + Config::getUserSentPhoneCodeDelay() >= time()
        ) {
            throw new LogicException('Phone code send timeout');
        }

        if ($user->getPhoneCodeSentCount() >= Config::getUserSentPhoneCodeNoDelayAttempt()) {
            $user->resetPhoneCodeSentCount();
        }

        $code = (string)random_int(100000, 999999);

        $user->setVerifyPhoneCode($code, $phone);
        $user->update();

        SmsLog::info('Send code ' . $code . ' to ' . $phone);

        SmsSender::send('+' . $phone, 'Your verification code: ' . $code);
    }

    public function setPhoneVerified(int $userId): User
    {
        $user = User::get($userId);

        $user->setPhoneVerified();
        $user->update();

        return $user;
    }

    public function getBalanceLogRecordCount(
        ?DateTimeInterface $startDate,
        ?DateTimeInterface $endDate,
        ?int $userId
    ): int {
        return (new UserBalanceLog())->getLogRecordsCount($startDate, $endDate, $userId);
    }

    public function setPassword(int $userId, string $password): void
    {
        $user = User::get($userId);
        $user->setPassword($password);
        $user->update();

        AccessToken::resetAll($userId);
    }

    public function resetAllSessions(int $userId): void
    {
        AccessToken::resetAll($userId);
    }

    public function setStatus(int $userId, string $status): void
    {
        $statusCode = array_flip(User::getStatuses())[$status];

        if (User::STATUS_INACTIVE === $statusCode) {
            throw new LogicException('Unable to set user status "inactive"');
        }

        $user = User::get($userId);
        $user->status = $statusCode;
        $user->update();

        if (User::STATUS_DELETED === $statusCode) {
            AccessToken::resetAll($userId);
            (new PackageAdminService())->deleteAllUserPackages($userId);
        }
    }

    public function setUserPhone(int $userId, string $phone): void
    {
        (User::get($userId))
            ->setPhone($phone)
            ->setPhoneVerified()
            ->update();
    }
}