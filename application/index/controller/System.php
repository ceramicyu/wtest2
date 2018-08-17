<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2018/2/6
 * Time: 10:59
 */

namespace app\index\controller;


class System extends PublicController
{
    // 操盘管理
    public function request($name){
        if(!isAjax()){
            JCode(ILLEGAL,'非法访问');
        }
        $func = "Ajax_".$name;
        if(!method_exists($this,$func)){
            JCode(ERROR,'模块'.$name.'不存在');
        }
        $this->checkRole(API_USER,$name,false);
        $this->$func();
    }


}