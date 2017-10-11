<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/10
 * Time: 下午1:46
 */


class Request
{
    /**
     * 获取路由通配符的信息 {id} {name} `正则表达式`
     * @return array
     */
    public static function getArgs(){
        return Router::getArgs();
    }
}