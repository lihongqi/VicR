<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/8
 * Time: 下午5:45
 */
$time = microtime(true);
require_once __DIR__ . '/TestApp/index.php';
print_r(App::Config('cache.file.path'));