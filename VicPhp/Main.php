<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/8
 * Time: 下午5:54
 */
define('VIC_CORE_PATH',__DIR__);
require_once __DIR__.'/Register.php';
spl_autoload_register('\VicPhp\Register::autoLoad');