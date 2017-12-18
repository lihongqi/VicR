<?php

namespace Model;


use Drm\Base;

class Blog extends Base
{
    CONST TABLE = 'blogs';
    CONST ORDER = 'add_time desc,id desc';

    protected static $with_info = [
        'author' => [
            'table' => Author::TABLE,
            'self_link_key' => 'author_id',
            'link_key' => 'id',
            'ops' => [
                'field' => 'id,name'
            ]
        ]
    ];

 }