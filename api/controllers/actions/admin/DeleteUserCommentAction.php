<?php

declare(strict_types=1);

namespace api\controllers\actions\admin;

use api\controllers\actions\Action;
use api\controllers\params\admin\DeleteUserCommentParams;
use api\models\exception\ApiParamsBadRequestHttpException;
use api\models\user\User;
use api\models\user\UserComment;

class DeleteUserCommentAction extends Action
{
    public function run(): bool
    {
        $params = new DeleteUserCommentParams();

        $params->load($this->request->post(), '');

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        (new UserComment())->delete($params->commentId, User::getCurrentUserId());

        return true;
    }
}