<?php

declare(strict_types=1);

namespace api\controllers\params;

use api\models\common\City;
use api\models\common\Country;
use api\models\Security;
use api\models\user\User;
use api\models\user\UserComment;

trait ParamsCustomValidator
{
    /**
     * Custom validator.
     * @param $attribute
     * @param $params
     * @param $validator
     * @param $current
     * @return bool
     */
    public function userExists($attribute, $params, $validator, $current): bool
    {
        if ($this->errorString) {
            return false;
        }

        if (!$current) {
            return true;
        }

        // some Params check user by email or what else
        if ('userId' === $attribute || 'authorId' === $attribute) {
            $attribute = 'id';
        }

        if (User::existsBy($attribute, $current)) {
            return true;
        }

        $this->addError($attribute, 'User not found: ' . $current);

        return false;
    }

    public function verifyPhoneCode($attribute, $params, $validator, $phoneCode): bool
    {
        if ($this->errorString) {
            return false;
        }

        if (!$phoneCode) {
            return true;
        }

        if (User::existsBy('verifyPhoneCode', $phoneCode)) {
            return true;
        }

        $this->addError($attribute, 'User not found by code verification.');

        return false;
    }

    public function checkVerificationCode($attribute, $params, $validator, $token): bool
    {
        if ($this->errorString) {
            return false;
        }

        if (!$token) {
            return true;
        }

        if (User::existsBy('verificationToken', $token)) {
            return true;
        }

        $this->addError($attribute, 'User not found by email verification.');

        return false;
    }

    public function checkPasswordResetToken($attribute, $params, $validator, $token): bool
    {
        if ($this->errorString) {
            return false;
        }

        if (!$token) {
            return true;
        }

        if (User::existsBy('passwordResetToken', $token)) {
            return true;
        }

        $this->addError($attribute, 'User not found by password reset token.');

        return false;
    }

    public function checkPassword($attribute, $params, $validator, $password): bool
    {
        if ($this->errorString) {
            return false;
        }

        if (!$password) {
            return true;
        }

        if (!isset($this->email)) {
            $this->addError($attribute, 'Email cannot be blank.');
            return false;
        }

        $user = User::getByEmail($this->email);

        if (!Security::validatePassword($password, $user->passwordHash)) {
            $this->addError($attribute, 'Wrong password.');
            return false;
        }

        return true;
    }

    public function emailIsFree($attribute, $params, $validator, $email): bool
    {
        if ($this->errorString) {
            return false;
        }

        if (!$email) {
            return true;
        }

        if (!User::existsBy('email', $email)) {
            return true;
        }

        $this->addError($attribute, 'This email address has already been taken.');

        return false;
    }

    public function userIsInactive($attribute, $params, $validator, $email): bool
    {
        if ($this->errorString) {
            return false;
        }

        if (!$email) {
            return true;
        }

        if (!$user = User::getByEmail($email)) {
            $this->addError($attribute, 'User not found: ' . $email);
            return false;
        }

        if (User::STATUS_INACTIVE !== $user->status) {
            $this->addError($attribute, 'User active or deleted: ' . $email);
            return false;
        }

        return true;
    }

    public function countryExists($attribute, $params, $validator, $countryId): bool
    {
        if ($this->errorString) {
            return false;
        }

        if (!$countryId) {
            return true;
        }

        if (Country::existsBy('id', $countryId)) {
            return true;
        }

        $this->addError($attribute, 'Country not found: ' . $countryId);

        return false;
    }

    public function cityExists($attribute, $params, $validator, $cityId): bool
    {
        if ($this->errorString) {
            return false;
        }

        if (!$cityId) {
            return true;
        }

        if (City::existsBy('id', $cityId)) {
            return true;
        }

        $this->addError($attribute, 'City not found: ' . $cityId);

        return false;
    }

    public function commentExists($attribute, $params, $validator, $userCommentId): bool
    {
        if ($this->errorString) {
            return false;
        }

        if (!$userCommentId) {
            return true;
        }

        if (UserComment::existsBy('id', $userCommentId)) {
            return true;
        }

        $this->addError($attribute, 'UserComment record not found: ' . $userCommentId);

        return false;
    }

    public function phoneFilter($current): string
    {
        return str_replace(['(', ')', '-', ' ', '+'], '', $current);
    }
}