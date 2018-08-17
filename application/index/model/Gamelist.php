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

class Gamelist extends Model {
    protected $table = 'api_game_list';
    private $cacheName = 'api_game_list'; // 缓存名字 方便清除缓存
    private $expire = 60 * 60 * 24 * 7; // 默认缓存一天

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
        $list = $this->select()->toArray();
        if(!empty($list)){
            $temp = [];
            foreach($list as $k=>$v){
                $temp[$v['game_id']] = $v;
            }
            $list = $temp;
            setCache('list',$this->cacheName,$list,$this->expire);
        }
        return $list;
    }

    // 根据游戏id获取
    public function getDataById($id){
        $list = $this->getDataList();
        if(empty($list)){
            return false;
        }
        foreach($list as $k=>$v){
            if($v['game_id'] == $id){
                return $v;
            }
        }
        return false;
    }
    /*
     * 根据js_tag获取游戏
     * @return: arr
     */
    public function getDataByTag($tag){
        $list = $this->where(['js_tag'=>$tag])->select()->toArray();
       if(!$list){
           return false;
       }
        return $list;
    }


}