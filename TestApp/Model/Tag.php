<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/14
 * Time: 下午6:00
 */

namespace Model;


use Drm\Base;

class Tag extends Base
{
    public static $table = 'd_tag';

    public static function getList($id)
    {
        self::where('id', '>', $id);
        return self::findAll([
            'where' => [
                ['id' => 12],
                ['id' => 5],
                ['id' => 16],
            ]
        ]);
    }
}