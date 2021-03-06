# VicR 文档!

![框架运行逻辑图](https://gitee.com/uploads/images/2017/1121/185219_63d48451_101862.png)


>用`laravel/lumen`感觉还挺好用的，但是发现有点慢。执行到开始执行controller里的代码前 本机测试大约需要`60ms`左右。本来只打算写一个路由的，后来还是打算做成了个简易框架。VicR的路由部分支持分组、中间件、缓存，路由规则支持{id}，{xxx}，`正则表达式`。

>测了一下路由消耗时间`1ms`左右(第一次执行会生成映射关系 Router::$info; Router::$as_info; )。对比了一下 fastroute ，fastroute需要`20ms`左右。

>PSR-4 规范

## 路由 Router

内置了restful 7个方法，可以支持任意方法，自己加一行代码就可以了。

### demo

```php

Router::group([
    'namespace' => 'Controller'
], function () {

    Router::get('/', 'Test@index');

    // {name} 通用匹配
    Router::get('/hello/{name}', 'Test@hello');

    //{id} 匹配数字
    Router::get('/blog/{id}', 'Test@blog');

    // 缓存 10秒
    Router::get('/cache', [
        'use' => 'Test@cache',
        'cache' => [
            'time' => 10 //10s
        ]
    ]);

    //中间件csrf 
    Router::group([
        'middle' => [\Middle\Safe::class . '@csrfSetSign'],
    ], function () {
        Router::get('/login', 'Test@login');
    });

    Router::post('/login', [
        'use' => 'Test@into',
        'as' => 'login.into',
        'middle' => [\Middle\Safe::class . '@csrfVerifySign']
    ]);


});

```

上面这个路由会生成如下缓存，下次请求如果文件没有修改会直接使用缓存的信息 
 
```json
[
    {
        "get": {
            "0": {
                "use": "\\Controller\\Test@index",
                "middle": []
            },
            "router": [
                {
                    "use": "\\Controller\\Test@router",
                    "middle": []
                }
            ],
            "hello": {
                "{name}": [
                    {
                        "use": "\\Controller\\Test@hello",
                        "middle": []
                    }
                ]
            },
            "blog": {
                "{id}": [
                    {
                        "use": "\\Controller\\Test@blog",
                        "middle": []
                    }
                ]
            },
            "cache": [
                {
                    "use": "\\Controller\\Test@cache",
                    "cache": {
                        "time": 10
                    }
                }
            ],
            "login": [
                {
                    "use": "\\Controller\\Test@login",
                    "middle": [
                        "Middle\\Safe@csrfSetSign"
                    ]
                }
            ]
        },
        "post": {
            "login": [
                {
                    "use": "\\Controller\\Test@into",
                    "as": "login.into",
                    "middle": [
                        "Middle\\Safe@csrfVerifySign"
                    ]
                }
            ]
        }
    },
    {
        "login.into": "/login"
    }
]

```

#### get put post delete patch head options


```php
Router::get('/user','User@xxx');

//{id}匹配纯数字 会在赋到调用方法的参数里 或者可以通过 Router::$args 获取
Router::get('/user/{id}','User@getUserInfo');

// 路径/user/123 会执行这里 貌似fastroute不支持这种路由
Router::get('/user/123','User@vip');

//{xxx}通配符
Router::get('/user/{name}','User@getUserInfoByName);

//正则表达式匹配 ^\w{2,4}$
Router::get('/user/`^\w{2,4}$`','User@getUserInfoByVipName');

//第二个参数为数组情况 。 
//  as：增加个别名home ，调用 Response::getUrl('home') 返回 /user/home ; 
//  middle：中间件 ； 中间件数组 从左到右 从外到里(group可以包在多个路由外面) 依次执行，任何一个中间件阻断了 后面的就都不会被执行了（常用来权限认证，数据加解密，接口合并…… )
//  cache：缓存 ，在缓存时间内不会执行User@getUserInfoByName直接返回上一次执行结果，会执行中间件。
Router::get('/user/home',[
    'use' => 'User@getUserInfoByName',
    'as' => 'home',
    'middle' => ['App@csrf']，
    'cache' => [
        'time' => 60 //过期时间 单位秒
    ],
]);
```
#### controller

自动加上了上面7个方法的路由 对应的方法规则 getAction, putAction ...

#### group 
```php
Router::group(
第一个参数数组 array(
	'as' => '别名', //string
	'middle' => ['App@csrf','App@filter'] //中间件回接接收到两个参数第一个 $next 匿名函数 ，第二个目标方法 目的是让中间件知道最终会由谁来执行这个请求 
	'cache' => [
		'time' => 60 //过期时间 单位秒
		'call' => Closure 函数方法 自定义缓存方法（非必须） 调用时会带上（方法名称+请求参数）
	],
	'namespace' => '命名空间',
	'prefix' => '请求url的前缀'
)
,
function(){
    Router::get('/name','User@name');
    
    Router::group(,)
}
);
```

## 数据操作 

``php

    Blog::find([
        'id' => 1,
        'star' => 0
    ]);
    // SELECT * FROM  `blogs` WHERE id = '1' AND star = '0' LIMIT 1

    $time = time();
    Blog::find([
        'id' => 1,
        ['add_time', '>=', $time],
        ['add_time', '<', $time + 86400],
        'star' => 0
    ]);
    // SELECT * FROM  `blogs` WHERE add_time >= '1513416708' AND add_time < '1513503108' AND id = '1' AND star = '0' LIMIT 1


    Blog::with('author');
    Blog::find([
        ['id','in',[1,2]],
        ['add_time', '>=', $time],
        ['add_time', '<', $time + 86400],
        'star' => 0
    ]);
    //SELECT * FROM  `blogs` WHERE id in ('1','2') AND add_time >= '1513416708' AND add_time < '1513503108' AND star = '0' LIMIT 1
    //SELECT id,name FROM  `author` WHERE id in ('3')

    Blog::find([
        ['id'=>1,'star' => 0],
        ['add_time', '>=', $time],
        ['add_time', '<', $time + 86400]
    ]);
    //SELECT * FROM  `blogs` WHERE (id = '1' OR star = '0') AND add_time >= '1513416708' AND add_time < '1513503108' LIMIT 1

```


