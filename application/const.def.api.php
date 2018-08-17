<?php
/**
 * Created by IntelliJ IDEA.
 * User: User
 * Date: 22/12/2017
 * Time: 8:17 PM
 */
// 菜单类型管理
define('MENU_USER',1); // 业主管理
define("MENU_SYNTHE",2); // 报表
define('MENU_ACCOUNT',3); // 账号管理
define('MENU_OPERATE',4); // 操盘管理
define('MENU_LOG',5); // 日志管理


// 总后台 接口类型定义
define("API_USER",1); // 业主类别
define("API_USER_THRID",10001); // 业主第三方类别
define("API_USER_ENOM",10002); // 业主域名
define("API_USER_DEL",10003); // 业主删除
define("API_USER_EDIT",10004); // 业主编辑
define("API_USER_ADD",10005); // 添加
define("API_USER_WEBSTATUS",10006); // 挂维护
define("API_USER_SETTING",10007); // 网站设置

define("API_ACCOUNT",2); // 账号管理 类
define("API_ACCOUNT_ADD",20001); // 角色添加
define("API_ACCOUNT_SETSTATUS",20002); // 关闭角色
define("API_ACCOUNT_EDITMENU",20003); // 编辑菜单
define("API_ACCOUNT_EDITAPI",20004); // 编辑接口.

define("API_MENBER",3); // 会员类

define("API_OPERATE",4); // 操盘类  开奖之类的


define('API_LOG',5); // 日志管理
define("API_LOG_LOGLIST",50001);


