<?php

declare(strict_types=1);

namespace api\controllers\actions\pub;

use api\controllers\actions\Action;
use api\controllers\params\pub\ExchangeParams;
use api\models\payment\Exchange;
use api\models\exception\ApiParamsBadRequestHttpException;

class ExchangeAction extends Action
{
    private Exchange $exchange;

    public function __construct($id, $controller, Exchange $exchange, $config = [])
    {
        $this->exchange = $exchange;
        parent::__construct($id, $controller, $config);
    }

    public function run(): object
    {
        $params = new ExchangeParams();
        $params->load($this->request->get(), '');

        if (!$params->validate()) {
            throw new ApiParamsBadRequestHttpException($params);
        }

        return $this->exchange->getExchange('usd', [$params->currency]);
    }
}