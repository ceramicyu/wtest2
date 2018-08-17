<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/12/2017
 * Time: 9:49 PM
 */
namespace app\index\validate;
use think\Validate;
class User extends Validate
{
    protected $rule = [
        'username' => 'require|checkUser',
        'admin_url' => 'require|url',
        'index_url' => 'require|url',
        'website'   => 'require|alphaNum',
    ];
    protected $message = [
        'username.require' => '请输入业主名字',
        'admin_url.require' => '请输入业主后台域名',
        'admin_url.url'=>'域名不合法',
        'index_url.require' => '请输入业主后台域名',
        'index_url.url'=>'域名不合法',

        'website.require'=>'website不能为空',
        'website.alphaNum'=>'website只能是字母跟数字',
    ];
    protected $scene = [
        'add' => ['username','admin_url','index_url','website'],
        'edit' => ['admin_url','index_url','website'],
    ];

    // 检测业主名字是否被暂用
    protected function checkUser($v,$r,$data){
        $db = db('User');
        $res = $db->field('id')->where(['username'=>$v])->find();
        if($res){
            // 被暂用
            return false;
        }
        // 可用
        return true;
    }
}
