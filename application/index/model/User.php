<?php
namespace app\index\model;
use	think\Model;

/**
DROP TABLE IF EXISTS api_user;
CREATE TABLE api_user (
`id` int(11) unsigned not null AUTO_INCREMENT comment 'id',
`username` char(20) not null default '' comment '业主名字',
`create_time` int(11) not null default '0' comment '写入数据表的时间',
`update_time` int(11) not null default '0',
`locked` tinyint(1) not null default '1' comment '是否维护',
`admin_url` varchar(200) not null default '' comment '后台接口域名',
`index_url` varchar(200) not null default '' comment '主域名接口域名',
`token` char(32) not null default '' comment '接口token',
`website` char(20) not null default '' comment 'website',
`remark` varchar(200) not null default '' comment '备注',
`view` varchar(20) not null default 'haocai' comment '使用模板',
 PRIMARY KEY (`id`)
) engine=InnoDB charset=utf8;
 */

class User extends Model {
    protected $table = 'api_user';
    protected $readonly = ['token','username']; // 只读字段
    private $cacheName = 'api_user'; // 缓存名字 方便清除缓存
    private $expire = 60 * 60 * 24; // 默认缓存一天
    protected $insert = ['token','create_time']; // 自动完成字段
    protected $update = ['update_time'];

    // 自动完成  添加自动写入token
    protected function setTokenAttr(){
        return md5(getRandStr());
    }
    protected function setUpdateTimeAttr($value){
        return time();
    }
    protected function setCreateTimeAttr($value){
        return time();
    }
    protected function getCreateTimeAttr($value){
        return date('Y/m/d H:i:s',$value);
    }
    protected function getUpdateTimeAttr($value){
        return date('Y/m/d H:i:s',$value);
    }

    // 获取业主的数据
    public function getDataList(){
        $list = getCache('list',$this->cacheName);
        if(empty($list)){
            $list = $this->setCache(); // 设置缓存
        }
        return $list;
    }

    // 设置缓存
    public function setCache(){
        $list = $this->select();// 这里是对象  转换成熟组
        if(!empty($list)){
            $temp = [];
            foreach ($list as $k=>$v){
                $temp[] = $v->toArray();
            }
            $list = $temp;
            setCache('list',$this->cacheName,$list,$this->expire);
        }
        return $list;
    }

    // 根据id获取业主的信息
    public function getDataById($id){
        if(!checkId($id)){
            return false;
        }
        $list = $this->getDataList();
        if(empty($list)){
            return null;
        }
        foreach($list as $k=>$v){
            if($v['id'] == $id){
                return $v;
            }
        }
        return null;
    }

    public function saveData($data,$where = []){
        $res = $this->save($data,$where);
        if($res){
            $this->setCache();
            return true;
        }
        return false;
    }

    // 根据id列表获取业主信息
    public function getUserInfoByIdlist($idlist){
        if(!preg_match('/^([0-9]*\|)*[0-9]+$/',$idlist)){
            return false;
        }
        $data = array_unique(array_filter(explode("|",$idlist))); // id数组
        $temp = [];
        foreach($data as $k=>$v){
            if($user = $this->getDataById($v)){
                $temp[] = $user;
            }
        }
        return $temp;
    }


}