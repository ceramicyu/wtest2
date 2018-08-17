<?php
namespace app\index\model;
use	think\Model;

/**
DROP TABLE IF EXISTS api_role;
CREATE TABLE api_role (
`id` int(11) unsigned not null AUTO_INCREMENT comment 'id',
`name` char(20) not null default '' comment '角色名字',
`create_time` int(11) not null default '0' comment '写入数据表的时间',
`update_time` int(11) not null default '0',
`port` varchar(1000) not null default '' comment '角色权限',
`status` tinyint(1) not null default '1' comment '1是可用  0是不用',
`mark` varchar(100) not null default '' comment '角色备注',
PRIMARY KEY (`id`)
) engine=InnoDB charset=utf8;
 */

class Role extends Model {
    protected $table = 'api_role';
    private $cacheName = 'api_role'; // 缓存名字 方便清除缓存
    private $expire = 60 * 60 * 24; // 默认缓存一天
    protected $insert = ['create_time']; // 自动完成字段
    protected $update = ['update_time'];

    protected function setUpdateTimeAttr(){
        return time();
    }
    protected function setCreateTimeAttr(){
        return time();
    }
    protected function getCreateTimeAttr($value){
        return date('Y/m/d H:i:s',$value);
    }
    protected function getUpdateTimeAttr($value){
        return date('Y/m/d H:i:s',$value);
    }

    // 获取可用的角色
    public function getDataInuse(){
        $list = $this->getDataList();
        if(empty($list)){
            return [];
        }
        $tmp = [];
        foreach($list as $v){
            if($v['status'] == 1){
                $temp = [];
                $temp['id'] = $v['id'];
                $temp['name'] = $v['name'];
                $tmp[$v['id']] = $temp;
            }
        }
        return $tmp;
    }

    // 获取角色的数据
    public function getDataList(){
        $list = getCache('list',$this->cacheName);
        if(empty($list)){
            $list = $this->setCache(); // 设置缓存
        }
        return $list;
    }

    // 设置缓存
    public function setCache(){
        $list = $this->select();
        if(!empty($list)){
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
//            $this->setCache();
            return true;
        }
        return false;
    }
}