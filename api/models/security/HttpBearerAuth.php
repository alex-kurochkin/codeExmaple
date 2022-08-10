<?php

declare(strict_types=1);

namespace api\models\security;

use yii\filters\auth\HttpBearerAuth as HttpBearerYiiAuth;

class HttpBearerAuth extends HttpBearerYiiAuth
{
    public static function getBearerToken(): ?string
    {
        $headers = self::getAuthorizationHeader();

        if (!empty($headers) && preg_match('/Bearer\s(\S+)/', $headers, $m)) {
            return $m[1];
        }

        return null;
    }

    private static function getAuthorizationHeader(): ?string
    {
        if (isset($_SERVER['Authorization'])) {
            return trim($_SERVER['Authorization']);
        }

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            return trim($_SERVER['HTTP_AUTHORIZATION']);
        }

        if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();

            $requestHeaders = array_combine(
                array_map('ucwords', array_keys($requestHeaders)),
                array_values($requestHeaders)
            );

            if (isset($requestHeaders['Authorization'])) {
                return trim($requestHeaders['Authorization']);
            }
        }

        return null;
    }
}