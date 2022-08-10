<?php

declare(strict_types=1);

namespace api\models\user\ars;

use api\models\Ar;
use api\models\user\UserComment;

/**
 * Class UserCommentAr
 * @package api\models\user\ars
 * @property int $id [int]
 * @property int $authorId [int]  Admin's userId
 * @property string $comment [varchar(500)]
 * @property int $datetime [timestamp]
 * @property int $userId [int]
 */
class UserCommentAr extends Ar
{
    protected static array $map = [
        'id' => 'id',
        'userId' => 'userId',
        'authorId' => 'authorId',
        'comment' => 'comment',
        'datetime' => 'datetime',
    ];

    public static function getModelName(): string
    {
        return UserComment::class;
    }

    public static function findByUserId(int $userId): array
    {
        $ascDesc = self::getOrderDirectionConst('desc');
        return self::find()->where(['userId' => $userId])->orderBy(['id' => $ascDesc])->all();
    }

    public static function tableName(): string
    {
        return '{{%UserComment}}';
    }
}