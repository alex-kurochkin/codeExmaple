<?php

declare(strict_types=1);

namespace api\controllers\actions;

use Yii;
use yii\base\Action as YiiAction;
use yii\web\Request;
use yii\web\Response;

class Action extends YiiAction
{
    protected Request $request;
    protected Response $response;

    public function __construct($id, $controller, $config = [])
    {
        $this->request = Yii::$app->request;
        $this->response = Yii::$app->response;
        parent::__construct($id, $controller, $config);
    }
}