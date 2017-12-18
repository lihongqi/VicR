<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/14
 * Time: 下午3:01
 */
$Db = [
    'default' => [
        'dns' => 'mysql:host=127.0.0.1;dbname=vicblog',
        'username' => 'root',
        'password' => '123456',
        'ops' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
        ]
    ]
];

$Db['read'] = $Db['default'];

return $Db;