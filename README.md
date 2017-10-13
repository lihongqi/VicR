# VicR 文档


>用`laravel/lumen`感觉还挺好用的，但是发现有点慢。执行到开始执行controller里的代码前 本机测试大约需要`60ms`左右。本来只打算写一个路由的，后来还是打算做成了个简易框架。VicR路由支持分组、中间件、缓存，路由规则支持{id}，{xxx}，`正则表达式`，本机测试路由消耗时间`1ms`左右。路由第一次执行后会缓存。

空间名、类名大驼峰；方法名小驼峰

## 路由 Router

内置了restful 7个方法，可以支持任意方法，自己加一行代码就可以了。

### demo
#### get put post delete patch head options
```php
Router::get('/user','User@getSelf');
//{id}匹配纯数字 会在赋到调用方法的参数里 或者可以通过 \Request::getArgs()获取
Router::get('/user/{id}','User@getUserInfo');
//{xxx}通配符
Router::get('/user/{name}','User@getUserInfoByName);
//正则表达式匹配 ^\w{2,4}$
Router::get('/user/`^\w{2,4}$`','User@getUserInfoByVipName');
//第二个参数为数组情况 as 增加两个别名 home 调用 Funcs::url('home') 返回 /user/home ; middle 中间件
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
第二参数执行方法 执行方法里面可以继续调用 Router::group
);
```


