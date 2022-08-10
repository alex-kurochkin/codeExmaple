<?php

declare(strict_types=1);

namespace api\controllers\params\message;

use api\controllers\params\Params;
use api\models\Config;

class MessageParams extends Params
{
    public string $channel = '';
    public string $format = '';
    public string $server = '';

    public function rules(): array
    {
        return [
            [['channel', 'format'], 'required'],
            [['channel', 'format'], 'string'],
        ];
    }

    public static function getParamsInstance(string $formatName): self
    {
        $c = __NAMESPACE__ . '\\' . ucfirst($formatName) . 'MessageParams';

        if (!class_exists($c)) {
            $c = DefaultMessageParams::class;
        }

        return new $c;
    }

    public function load($data, $formName = ''): bool
    {
        $this->channel = $data['channel'];
        $this->format = $data['format'];
        $this->server = Config::getCorsOrigin();
        return parent::load($data, $formName);
    }
}