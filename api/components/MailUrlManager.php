<?php

declare(strict_types=1);

namespace api\components;

use api\models\Config;

class MailUrlManager
{
    public function createAbsoluteUrl(string $path, array $params = []): string
    {
        $url = Config::getCorsOrigin() . '/';

        $url .= $path ? $path . '/' : '';

        $url .= $params ? '?' . http_build_query($params) : '';

        return $url;
    }
}