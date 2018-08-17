<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/12/2017
 * Time: 4:20 PM
 */
return [
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__PUBLIC__'       => '/static/',

    ],
    // 缓存管理
    'redis'          =>[
        'host'       => '10.0.12.10', // 暂时关闭缓存
        'port'       => 6379,
        'password'   => '',
        'select'     => 0,
        'timeout'    => 0,
        'expire'     => 600, // 默认10分钟
        'persistent' => true,
        'prefix'     => '',
    ],

    // 补奖接口设定
    'get_kjballs' => 'http://52.69.209.225/lottery-2.php', // 所有彩种近5期
    'get_kjballs_by_tag' => 'http://52.69.209.225/lottery.php', // 可以根据日期进行获取某个彩种 某天

    // 所有操作的语言包
    'cp_lang' => require('cp_lang.php'),
    'trad_type'=> require('trad_type.php'),

];

