<?php
use think\Route;                        //引入Route
Route::domain('www.opop33.com:8080','api');
//Route::rule('test1','index/index/demo');
//Route::rule('test2','api/v1/index');
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    //别名配置,别名只能是映射到控制器且访问时必须加上请求的方法
    '__alias__'   => [
    ],
    //变量规则
    '__pattern__' => [
    ],
//        域名绑定到模块
//    '__domain__'  => [
//        'admin' => 'admin',
//        'www.opop33.com'   => 'api',
//    ],
//    'api/nihao' =>'api/v1/index',
];
//Route::rule('api/nihao','api/v1/index');


//Route::rule(':version/user/:id','api/:version.User/read');
// 路由到默认模块或者绑定模块
//Route::get('version/index/:id','api/:version.Index/index');
// 路由到index模块
//Route::get('blog/:id','index/blog/read');
