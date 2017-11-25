<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/9
 * Time: 下午4:20
 */

for ($i=0;$i<10000;$i++) {
    Router::get('/name/{user}/'.$i, '\\Controller\\Test@name');
}
