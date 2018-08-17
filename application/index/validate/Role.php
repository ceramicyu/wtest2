<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/12/2017
 * Time: 9:49 PM
 */
namespace app\index\validate;
use think\Validate;
class Role extends Validate
{
    protected $rule = [
        'name' => 'require|checkUser',
    ];
    protected $message = [
        'name.require' => '请输入业主名字',
        'name.checkUser' => '该角色已经存在',
    ];
    protected $scene = [
        'add' => ['username'],
    ];

    // 检测业主名字是否被暂用
    protected function checkUser($v,$r,$data){
        $db = db('Role');
        $res = $db->field('id')->where(['name'=>$v])->find();
        if($res){
            // 被暂用
            return false;
        }
        // 可用
        return true;
    }
}
