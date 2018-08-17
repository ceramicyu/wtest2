<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//use think\Route;

//Route::rule('getApiData/:id', 'Index/Index/getApiData/:id');
return [
    // 首页
    '/index'=>'index/Index/index',
    // 登陆
    '/login'=>['index/Index/login',['method'    =>  'post|put']],
    // 退出
    '/logout'=>'index/Index/logout',

    // 所有的界面加载  除了登录跟首页
    '/:name'            =>  ['index/PageTpl/request',['method'    =>  'get']],

    // 所有的业主类
    '/user/:name'             =>  ['index/User/request',['method'    =>  'put|ajax']],

    // 角色类
    '/role/:name'             =>  ['index/Role/request',['method'    =>  'put|ajax']],

    // 账号管理
    '/admin/:name'             =>  ['index/Admin/request',['method'    =>  'put|ajax']],

    // 接口类
    '/api/:name'             =>  ['index/UserApi/request',['method'    =>  'put|ajax']],

    // 日志类
    '/log/:name'             =>  ['index/Log/request',['method'    =>  'put|ajax']],

    // 操盘管理类
    '/operate/:name'             =>  ['index/Operate/request',['method'    =>  'put|ajax']],

    // 缓存管理类
    '/system/:name'             =>  ['index/System/request',['method'    =>  'put|ajax']],

    // 上传类
    '/upload/:name'             =>  ['index/Upload/request',['method'    =>  'put|ajax|post']],

    // 首页
    '/'=>'index/Index/index',
];