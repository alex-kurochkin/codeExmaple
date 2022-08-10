<?php

namespace console\controllers;

use api\models\user\ars\UserAr;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class SeedController extends Controller
{
    public function actionUsers(): int
    {
        (new UserAr())->truncate();

        Yii::$app->db->createCommand()->batchInsert(
            '{{%user}}',
            [
                'id',
                'username',
                'auth_key',
                'password_hash',
                'password_reset_token',
                'email',
                'status',
                'created_at',
                'updated_at',
                'verification_token',
                'access_token',
            ],
            [
                [1, 'admin', 'afHopAqhSyPlSbl5y4nTgM_cPduteUP8', '$2y$13$OJcdiEIhP2BtYiBBgqxJie.DxFSOPdLNWQHjklau0AcE.hzoof7/K', null, 'admin@mp.local', 10, 1616049869, 1616049869, '6OkRD_1at0Mi07C-lyswGlG4H7mj9p8I_1616049869', 'nfjneafkjacrkjfd8gydf7df786fdg78fd67gfdsf'],
                [2, 'su', 'izZxl7lQqS_XtreZAk1fqlrK2ylhOXI3', '$2y$13$ro.1l/xvsqZXrq2xu5rV9.ho5xi3.xHe0FHeCT4TmqWUoUcqtKeQi', null, 'su@mp.local', 9, 1616055286, 1616055286, 'AeqsnB8lOZAPz7oIpG9iUv9hkfNwSZdj_1616055286', '3rssnfjneafkjacrkjfd8gydf7df786fdg78fd67g'],
            ]
        )->execute();

        return ExitCode::OK;
    }
}