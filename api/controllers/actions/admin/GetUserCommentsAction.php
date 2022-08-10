<?php

declare(strict_types=1);

namespace api\controllers\actions\admin;

use api\controllers\actions\Action;
use api\controllers\dtos\admin\UserCommentsDto;
use api\controllers\params\admin\GetUserCommentParams;
use api\models\exception\ApiParamsBadRequestHttpException;
use api\models\user\UserComment;

class GetUserCommentsAction extends Action
{
    public function run(): UserCommentsDto
    {
        $params = new GetUserCommentParams();

        $params->load($this->request->get(), '');

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        $comments = (new UserComment())->getByUserId((int)$params->userId);

        return (new UserCommentsDto($comments));
    }
}