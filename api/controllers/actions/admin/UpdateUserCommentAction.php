<?php

declare(strict_types=1);

namespace api\controllers\actions\admin;

use api\controllers\actions\Action;
use api\controllers\params\admin\UpdateUserCommentParams;
use api\models\exception\ApiParamsBadRequestHttpException;
use api\models\user\User;
use api\models\user\UserComment;

class UpdateUserCommentAction extends Action
{
    public function run(): bool
    {
        $params = new UpdateUserCommentParams();

        $params->load($this->request->post(), '');

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        (new UserComment())->updateComment($params->commentId, User::getCurrentUserId(), $params->comment);

        return true;
    }
}