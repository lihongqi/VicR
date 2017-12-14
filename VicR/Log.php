<?php

/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/14
 * Time: 上午10:57
 */
class Log
{

    private static $levels = [
        'ERROR', 'WARN', 'NOTICE', 'DEBUG'
    ];

    /**
     * @param $data
     * @param int $k
     * @param $prefix
     */
    public static function debug($data, $k = 0, $prefix = 'debug')
    {
        self::_log($data, $k + 1, 3, $prefix);
    }

    /**
     * @param $data
     * @param int $k
     */
    public static function notice($data, $k = 0)
    {
        self::_log($data, $k + 1, 2, 'notice');
    }

    /**
     * @param $data
     * @param int $k
     */
    public static function warn($data, $k = 0)
    {
        self::_log($data, $k + 1, 1, 'warn');
    }

    /**
     * @param $data
     * @param int $k
     */
    public static function error($data, $k = 0)
    {
        self::_log($data, $k + 1, 0, 'error');
    }


    private static function _log($data, $k = 0, $code = 3, $prefix = 'vic')
    {

        $dir = App::config('Log.path') . '/' . date('Y-m-d') . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $path = $dir . $prefix . date('H') . '.log';
        if (is_string($data)) {
            $data = str_replace("\n", ' ', $data);
        } else {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }


        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 12);
        $name = $trace[$k]['file'];
        $line = $trace[$k]['line'];

        $code = self::$levels[$code];

        $str = $code . '|' . date('Y-m-d H:i:s') . '|' . Request::id() . '|' . $name . ':' . $line . '|' . $data . "\n";
        error_log($str, 3, $path);

    }
}