<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/12/2017
 * Time: 9:49 PM
 */
namespace app\index\validate;
use think\Validate;
class Admin extends Validate
{
    protected $rule = [
        'username' => 'require|checkUser|checkPass',
        'password' => 'require|checkPass',
        'accesstype'    => 'require|checkType|checkOnly',
        'level'    => 'checkLevel',
    ];
    protected $message = [
        'username.require' => '请输入账号',
        'username.checkUser' => '该账号已经存在',
        'password.require' => '请输入密码',
        'password.checkPass' => '密码不符合规范',
        'accesstype.checkType'=>'请选择角色',
        'accesstype.require'=>'请选择角色',
        'accesstype.checkOnly'=>'超级管理员的角色只能有一个',
        'level.checkLevel'=>'权值只能是1-99',
    ];
    protected $scene = [
        'add' => ['username','password','accesstype','level'],
        'edit' => ['username','accesstype','level'],
    ];

    // 权值检测
    protected function checkLevel($v,$r,$data){
        if(!preg_match('/^[1-9][0-9]?$/',$v)){
            return false;
        }
        return true;
    }

    // 检测业主名字是否被暂用
    protected function checkUser($v,$r,$data){
        $db = db('Admin');
        $res = $db->field('id')->where(['username'=>$v])->find();
        if($res){
            // 被暂用
            return false;
        }
        // 可用
        return true;
    }

    // 检测账号 密码
    protected function checkPass($v,$r,$data,$a){
        if($a == 'username'){
            $len = 4;
        }else{
            $len = 6;
        }
        if(checkUser($v,$len)){
            return true;
        }
        return false;
    }

    //
    protected  function checkType($v,$r,$data,$a){
        if(checkId($v)){
            return true;
        }
        return false;
    }

    protected function checkOnly($v,$r,$data,$a){
        if($v == 1){
            return false;
        }
        return true;
    }

}
