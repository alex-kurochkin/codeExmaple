<?php

declare(strict_types=1);

namespace api\controllers\actions\admin;

use api\controllers\actions\Action;
use api\controllers\params\admin\SetUserCommentParams;
use api\models\exception\ApiParamsBadRequestHttpException;
use api\models\user\User;
use api\models\user\UserComment;

class AddUserCommentAction extends Action
{
    public function run(): bool
    {
        $params = new SetUserCommentParams();

        $params->load($this->request->post(), '');

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        (new UserComment())->add(User::getCurrentUserId(), $params->userId, $params->comment);

        return true;
    }
}