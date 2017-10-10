<?php

/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/8
 * Time: 下午5:48
 */
class Register
{
    public static function autoLoad($path)
    {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        if (file_exists(VIC_APP_ATH . '/' . $path . '.php')) {
            return require_once(VIC_APP_ATH . '/' . $path . '.php');
        } else if (file_exists(VIC_CORE_PATH . '/' . $path . '.php')) {
            return require_once(VIC_CORE_PATH . '/' . $path . '.php');
        } else {
            exit('没有找到文件:' . $path);
        }
    }
}