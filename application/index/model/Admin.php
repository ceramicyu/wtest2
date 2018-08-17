<?php
namespace app\index\model;
use	think\Model;

/**
DROP TABLE IF EXISTS api_admin;
CREATE TABLE api_admin (
`id` int(11) unsigned not null AUTO_INCREMENT comment 'id',
`username` char(20) not null default '' comment '账号',
`password` char(32) not null default '' comment '密码',
`create_time` int(11) not null default '0',
`update_time` int(11) not null default '0',
`locked` tinyint(1) not null default '1' comment '是否锁定',
`accesstype` int(11) not null default '1' comment '管理员权限',
`phone` varchar(20) not null default '' ,
`login_ip` int(11) not null default '0' comment '当前登录的ip',
`last_ip` int(11) not null default '0' comment '上次登录的ip',
`login_time` int(11) not null default '0' comment '登录时间',
`last_time` int(11) not null default '0' comment '上次登录时间',
 `token` char(32) not null default '' comment '登录的令牌',
PRIMARY KEY (`id`),
KEY `username` (`username`)
) engine=InnoDB charset=utf8;
 */

class Admin extends Model {
    protected $table = 'api_admin';
    protected $insert = ['create_time'];
    protected $update = ['update_time'];
    protected $readonly = ['username'];
    protected $cacheName = 'api_admin';
    protected $expire = 1800;// 默认30分钟重新登录


    // 自动完成  create time 跟update time已经可以自动写入
    protected function setCreateTimeAttr($value){
        return time();
    }
    protected function setUpdateTimeAttr($value){
        return time();
    }
    protected function getUpdateTimeAttr($value){
        return date('Y-m-d H:i:s',$value);
    }
    protected function getCreateTimeAttr($value){
        return date('Y-m-d H:i:s',$value);
    }
    protected function getLoginIpAttr($ip){
        return long2ip($ip); // 获取ip  字符串
    }
    protected function setLoginIpAttr(){
        return ip2long(request()->ip()); // 自动获取ip
    }
//    protected function setLastIpAttr($ip){
//        return ip2long($ip); // 整形保存
//    }
    protected function getLastIpAttr($ip){
        return long2ip($ip); // 整形保存
    }
    protected function setLoginTimeAttr(){
        return time(); // 自动设置当前登录时间
    }
    protected function getLoginTimeAttr($time){
        return date('Y/m/d H:i:s',$time);
    }

//    protected function setLastTimeAttr($time){
//        if(is_numeric($time)){
//            return $time;
//        }
//        return strtotime($time); // 自动设置当前登录时间
//    }
    protected function getLastTimeAttr($time){
        return date('Y/m/d H:i:s',$time);
    }
    // 登录初始化用户数据   之后增加权限数据管控  等其他
    public function initAdminData($user){
        $user = $user->toArray();
        // 载入角色信息
        $role = db('Role')->where(['id'=>$user['accesstype'],'status'=>1])->find();
        if(!$role){
            return false; // 载入角色失败
        }
        $user['last_ip'] = ip2long($user['login_ip']);
        $user['last_time'] = strtotime($user['login_time']);
        $user['token'] = md5($user['username'].time().$user['password']);
        $user['lock_status']=0;
        $res = $this->save($user,['id'=>$user['id']]);
        if(!$res){
            return false;
        }
        $user['menulist'] = $role['menulist'];
        $user['portlist'] = $role['portlist'];
        $user['role'] = $role['name'];
        setCache($user['token'],$this->cacheName,$user,$this->expire);
        unset($user['password']);
        session('_admin',$user);
        return true;
    }

    // 更新管理员缓存
    private function updateCache($user){
        // 载入角色信息
        $role = db('Role')->where(['id'=>$user['accesstype'],'status'=>1])->find();
        if(!$role){
            return false; // 载入角色失败
        }
        $user['menulist'] = $role['menulist'];
        $user['portlist'] = $role['portlist'];
        $user['role'] = $role['name'];
        setCache($user['token'],$this->cacheName,$user,$this->expire);
        return $user;
    }

    public function getDataById($id){
        if(!checkId($id)){
            return false;
        }
        $res = $this->find($id);
        if(!$res){
            return false;
        }
        return $res;
    }

    private function getDataByToken($token,$id){
        if(empty($token)){
            return false;
        }
        $res = getCache($token,$this->cacheName);
        if(!$res){
            // 补救
            $res = $this->where(['token'=>$token,'id'=>$id])->find();
            if(!$res){
                return false;
            }
            unset($res['password']);
            return $this->updateCache($res->toArray());
        }
        return $res;
    }

    public function saveData($data,$where){
        return $this->save($data,$where);
    }

    public function checkToken($user){
        $res = $this->getDataByToken($user['token'],$user['id']);
        if(!$res){
            return false;
        }
        return true;
    }

}