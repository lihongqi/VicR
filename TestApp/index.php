<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/8
 * Time: 下午5:45
 */
define('VIC_APP_PATH', __DIR__);
define('VIC_SHELL', false);
define('VIC_VIEW_PATH',VIC_APP_PATH.'/View');
require_once VIC_APP_PATH . '/../VicR/VicR.php';


App::loadRouter();
//Response::sessionStart('jzt');

try {
    Router::exec();
} catch (\Except\DbError $e) {
    echo 'DB error.';
} catch (Exception $e) {

}


