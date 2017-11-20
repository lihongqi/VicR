<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/22
 * Time: 下午5:40
 */

namespace Cache;


class Redis
{
    /**
     * @var \Redis
     */
    private static $redis = null;

    private static $start_time = 0;

    private static $max_connect_time = 600;

    private function __construct()
    {

    }

    /**
     * @return \Redis
     */
    public static function connect(){
        if(self::$redis === null || self::$start_time + self::$max_connect_time < time()) {
            if(self::$redis){
                self::$redis->close();
                self::$redis = null;
            }
            self::$start_time = time();
            self::$redis = new \Redis();
            $conf = \App::config('Cache.redis');
            self::$redis->connect($conf['host'], $conf['port'],0);
        }
        return self::$redis;
    }

}