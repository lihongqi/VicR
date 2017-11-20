<?php

/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/20
 * Time: 下午8:53
 */
class Response
{
    /**
     * 模板中的数据
     * @var array
     */
    public static $tpl_data = [];


    /**
     * @param string $m
     * @param array $args
     * @return mixed
     */
    public static function redirectMethod($m, $args = [])
    {
        return App::call($m, $args);
    }

    /**
     * @param string $url
     * @param array $args
     * @param array $info ['type':'','msg':'']
     */
    public static function redirectAndNotice($url,$args = [],$info = []){
        $_SESSION['notice'] = $info;
        self::redirect($url,$args);
    }

    /**
     * 页面跳转
     * @param $url
     * @param array $args
     */
    public static function redirect($url, $args = [])
    {
        if (isset($args['time'])) {
            header('Refresh:' . $args['time'] . ';url=' . $url);
        } else if (isset($args['httpCode'])) {
            header('Location:' . $url, true, $args['httpCode']);
        } else {
            header('Location:' . $url, true, 302);
        }
    }

    /**
     * @param $str
     * @param array $data
     * @param bool $csrf
     * @return string
     */
    public static function getUrl($str, $data = [], $csrf = false)
    {
        $url = \Funcs::array_get(Router::$as_info, $str);
        if ($data) {
            $key = array_map(function ($v) {
                return '{' . $v . '}';
            }, array_keys($data));
            $url = str_replace($key, array_values($data), $url);
        }
        if ($csrf) {
            $url = $url . '?' . \Logic\Safe::CSRF_KEY . '=' . \Logic\Safe::createSign();
        }
        return $url;
    }

    /**
     * @param string $tpl
     * @param array $data
     */
    public static function tpl($template, $data = [])
    {
        if (defined('VIC_VIEW_PATH') === false) {
            return '未定义模板路径:VIC_VIEW_PATH';
        }
        self::$tpl_data = $data + self::$tpl_data;
        ob_start();
        extract(self::$tpl_data);
        require VIC_VIEW_PATH . '/' . $template . '.php';
        return ob_get_clean();
    }

    /**
     *
     */
    public static function sessionStart($name = '')
    {
        if($name){
            session_name($name);
        }
        session_start();
    }

    /**
     *
     */
    public static function sessionDistory()
    {
        session_destroy();
    }

}