<?php

declare(strict_types=1);

namespace api\models\security;

use api\models\ErrorCode;
use api\models\exception\ForbiddenException;
use api\models\exception\UnauthorizedException;
use yii\web\IdentityInterface;

class HttpBearerAdminAuth extends HttpBearerAuth
{
    public function authenticate($user, $request, $response): ?IdentityInterface
    {
        $identity = parent::authenticate($user, $request, $response);

        if (!$identity) {
            $response->statusCode = ErrorCode::UNAUTHORIZED;
            throw new UnauthorizedException('Unauthorized');
        }

        if (!AuthManager::isAdmin($identity->id)) {
            $response->statusCode = ErrorCode::FORBIDDEN;
            throw new ForbiddenException(
                'Admin access only. User id: ' . $identity->id . ' (' . $identity->email . ')'
            );
        }

        return $identity;
    }
}