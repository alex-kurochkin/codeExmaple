<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit(): void
    {
        Yii::$app->db->createCommand()->delete('auth_assignment', [])->execute();
        Yii::$app->db->createCommand()->delete('auth_item_child', [])->execute();
        Yii::$app->db->createCommand()->delete('auth_item', [])->execute();
        Yii::$app->db->createCommand()->delete('auth_rule', [])->execute();

        $auth = Yii::$app->authManager;

        // Create roles
        $adminRole = $auth->createRole('admin');
        $auth->add($adminRole);

        $userRole = $auth->createRole('user');
        $auth->add($userRole);

        $suRole = $auth->createRole('su');
        $auth->add($suRole);

        // Set hierarchy
        $auth->addChild($userRole, $adminRole);
        $auth->addChild($adminRole, $suRole);

        // Set roles
        // Users are added by migration:
        // 1 - admin
        // 2 - su
        $auth->assign($adminRole, 1);
        $auth->assign($suRole, 2);
    }
}