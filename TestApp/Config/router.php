<?php
/**
 * Created by PhpStorm.
 * User: tanszhe
 * Date: 17/10/9
 * Time: 下午4:20
 */

Router::get('/name','\Controller\\Test@name');
Router::get('/name/{id}','\Controller\\Test@hello');
Router::get('/articles/{id}/{title}','\Controller\\Test@topic');

for ($i=0; $i < 1000 ; $i++) {
    Router::get('/abc'.$i,'\Controller\\Test@name');
}
