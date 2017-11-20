<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/8
 * Time: 下午5:54
 */
define('VIC_CORE_PATH', __DIR__);
if(defined('VIC_SHELL') === false){
    define('VIC_SHELL',true);
}
require_once VIC_CORE_PATH . '/App.php';
spl_autoload_register('App::autoLoad');
App::$start_time = microtime(true);

