<?php

declare(strict_types=1);

namespace api\controllers\dtos\common;

use api\controllers\dtos\SingleDto;
use api\models\security\AuthManager;
use api\models\user\User;

class UserDto extends SingleDto
{
    public $id;
    public $username;
    public $email;
    public $status;
    public $phone;
    public $balance;
    public $phoneVerified;
    public $language;
    public $isAdmin;
    public $isSu;
    public $createdAt;
    public $updatedAt;
    public ?int $proxiesCount = null;
    public ?int $proxiesCountAll = null;

    public ?int $spentMoney = null;


    /**
     * @param User $user
     * @return $this
     */
    public function oneToResponse($user): self
    {
        $statuses = User::getStatuses();

        /** @var UserDto $this */
        parent::oneToResponse($user);
        $this->status = $statuses[$this->status];

        $this->isAdmin = AuthManager::isAdmin($this->id);
        $this->isSu = AuthManager::isSu($this->id);

        return $this;
    }
}