<?php

/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/8
 * Time: 下午5:49
 */
class Router
{
    public static $info = [];

    public static $as_info = [];

    public static $args = [];

    /**
     * @return mixed|null
     */
    private static function getPath()
    {
        return urldecode(Funcs::array_get_not_null($_SERVER, ['REQUEST_URI', 'argv.1']));
    }

    /**
     * @return string
     */
    private static function getKey()
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        $path = self::getPath();
        $paths = explode('?', $path);
        $path = '/' . trim($paths[0], '/');
        $paths = explode('/', $path);
        foreach ($paths as $i => $v) {
            if (is_numeric($v)) {
                $paths[$i] = '#' . $v;
            }
        }
        $path = implode('.', $paths);
        if ($path === '') {
            $path = 0;
        }
        $path = trim($method . $path, '.');
        return $path;
    }

    private static function matchRouter($arr, $key)
    {
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            foreach ($keys as $v) {
                $arr = self::rules($arr, $v);
                if ($arr === null) {
                    return null;
                }
            }
            return $arr;
        } else {
            return self::rules($arr, $key);
        }
    }


    private static function rules($arr, $v)
    {
        if (isset($arr[$v])) {
            return $arr[$v];
        }
        $keys = array_keys($arr);
        foreach ($keys as $key) {
            $s = substr($key, 0, 1);
            if ($s == '{') {
                if (substr($key, 1, 2) == 'id') { //  match {id} only is numeric
                    if (substr($v, 0, 1) == '#') {
                        self::$args[] = substr($v, 1);
                        return $arr[$key];
                    }
                } else {
                    self::$args[] = $v;
                    return $arr[$key];
                }
            } else if ($s == '`') { //正则表达式 ` 正则表达式 `
                if (preg_match('/' . substr($key, 1, -1) . '/', $v)) {
                    self::$args[] = $v;
                    return $arr[$key];
                }
            }
        }
        return null;
    }

    public static function getExecInfo()
    {
        $info = self::matchRouter(self::$info, self::getKey());
        if (is_array($info)) {
            $info = Funcs::array_get_not_null($info, ['0', 'use']);
        }
        if (is_array($info)) {
            $info = Funcs::array_get($info, 'use');
        }
        return $info;
    }

    private static $group_info = [];
    private static $max_group_depth = 200;

    /**
     * @param array $rule
     * @param \Closure $route
     */
    public static function group($rule, $route)
    {
        $len = self::$max_group_depth - count(self::$group_info);
        self::$group_info[$len] = $rule;
        ksort(self::$group_info);
        $route();
        unset(self::$group_info[$len]);
    }

    private static function withGroupAction($group_info, $action)
    {
        if (is_array($action)) {
            if (isset($group_info['as']) && isset($action['as'])) {
                $action['as'] = trim($group_info['as'], '.') . '.' . $action['as'];
            }
            if (isset($group_info['namespace'])) {
                $action['use'] = '\\' . $group_info['namespace'] . '\\' . trim($action['use'], '\\');
            }
            if (isset($group_info['middle'])) {
                $action['middle'] = $group_info['middle'];
            }
            if (isset($group_info['cache'])) {
                $action['cache'] = $group_info['cache'];
            }
        } else {
            if (isset($group_info['namespace'])) {
                $action = '\\' . $group_info['namespace'] . '\\' . trim($action, '\\');
            }
            if (isset($group_info['middle'])) {
                $action = ['use' => $action];
                $action['middle'] = $group_info['middle'];
            }
            if (isset($group_info['cache'])) {
                $action['cache'] = $group_info['cache'];
            }
        }
        return $action;
    }

    private static function withGroupPath($group_info, $path)
    {
        $path = '/' . trim($path, '/');
        if (isset($group_info['prefix'])) {
            $prefix = trim($group_info['prefix'], '/');
            $path = '/' . trim($prefix, '/') . $path;
        }
        return $path;
    }


    private static function set($method, $path, $action)
    {
        foreach (self::$group_info as $value) {
            $action = self::withGroupAction($value, $action);
            $path = self::withGroupPath($value, $path);
        }
        if (is_array($action)) {
            self::setAsInfo($path, $action);
        }
        $arr = explode('/', $method . $path);
        self::$info = array_merge_recursive(self::$info, self::setPath($arr, $action));
    }

    /**
     * @param $path
     * @param array $action
     */
    private static function setAsInfo($path, $action)
    {
        if (isset($action['as'])) {
            self::$as_info[$action['as']] = $path;
        }
    }

    private static function setPath($arr, $v, $i = 0)
    {
        if (isset($arr[$i])) {
            if (is_numeric($arr[$i])) {
                $arr[$i] = '#' . $arr[$i];
            } else if ($arr[$i] == '') {
                $arr[$i] = 0;
            }
            return [$arr[$i] => self::setPath($arr, $v, $i + 1)];
        } else {
            return $v;
        }
    }

    /**
     * @param string $path
     * @param string $controller
     */
    public static function resource($path, $controller)
    {
        self::get($path, $controller . '@' . 'getAction');
        self::post($path, $controller . '@' . 'postAction');
        self::put($path, $controller . '@' . 'putAction');
        self::delete($path, $controller . '@' . 'deleteAction');
        self::patch($path, $controller . '@' . 'patchAction');
        self::head($path, $controller . '@' . 'headAction');
        self::options($path, $controller . '@' . 'optionsAction');
    }

    /**
     * @param string $path
     * @param string|array $action
     */
    public static function get($path, $action)
    {
        self::set('get', $path, $action);
    }

    /**
     * @param string $path
     * @param string|array $action
     */
    public static function post($path, $action)
    {
        self::set('post', $path, $action);
    }

    /**
     * @param string $path
     * @param string|array $action
     */
    public static function put($path, $action)
    {
        self::set('put', $path, $action);
    }

    /**
     * @param string $path
     * @param string|array $action
     */
    public static function delete($path, $action)
    {
        self::set('delete', $path, $action);
    }

    /**
     * @param string $path
     * @param string|array $action
     */
    public static function patch($path, $action)
    {
        self::set('patch', $path, $action);
    }

    /**
     * @param string $path
     * @param string|array $action
     */
    public static function head($path, $action)
    {
        self::set('head', $path, $action);
    }

    /**
     * @param string $path
     * @param string|array $action
     */
    public static function options($path, $action)
    {
        self::set('options', $path, $action);
    }


}

