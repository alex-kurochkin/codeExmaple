<?php

declare(strict_types=1);

namespace api\controllers\params\admin;

use api\controllers\params\Params;

class UsersListParams extends Params
{
    public string $searchUser = '';
    public int $start = 0;
    public int $count = 100;
    public string $orderBy = 'id';
    public string $orderDirection = 'asc';
    public string $role = 'user';

    public function rules(): array
    {
        return [
            ['searchUser', 'string'],
            ['start', 'integer', 'min' => 0],
            ['count', 'integer', 'min' => 1],
            [['orderBy', 'orderDirection'], 'string'],
            [
                'orderBy',
                'in',
                'range' => ['id', 'username', 'email', 'status', 'balance', 'createdAt', 'spentMoney']
            ],
            ['orderDirection', 'in', 'range' => ['asc', 'desc']],
            ['role', 'in', 'range' => ['user', 'admin']],
        ];
    }
}