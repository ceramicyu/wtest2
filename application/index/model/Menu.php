<?php
namespace app\index\model;
use	think\Model;

/**
DROP TABLE IF EXISTS api_menu;
CREATE TABLE api_menu (
`id` int(11) unsigned not null AUTO_INCREMENT comment 'id',
`pid` int(10) unsigned not null default '0' comment '父级id',
`appname` char(15) not null default '' comment '菜单名字',
`status` tinyint(1) not null default '1' comment '是否使用',
`router` char(20) not null default '' comment '接口名字',
`flag` tinyint(1) not null default '1' comment '菜单还是接口',
`icon` varchar(50) not null default '' comment 'icon',
PRIMARY KEY (`id`)
) engine=InnoDB charset=utf8;
 */

class Menu extends Model {
    protected $table = 'api_menu';
    private $cacheName = 'api_menu'; // 缓存名字 方便清除缓存
    private $expire = 60 * 60 * 24; // 默认缓存一天
    // 获取菜单数据
    public function getDataList(){
        $list = getCache('menulist',$this->cacheName);
        if(empty($list)){
            $list = $this->setCache()['menulist']; // 设置缓存
        }
        return $list;
    }

    // 设置缓存
    public function setCache(){
        $list = $this->where(['status'=>1])->select();
        if(!empty($list)){
            $menulist = [];
            $temp = [];
            foreach($list as $k=>$v){
                $v = $v->toArray();
                $temp[] = $v;
                if($v['pid'] == 0){
                    // 1级菜单
                    $menulist[$v['id']] = $v;
                    if(!isset($menulist[$v['id']]['submenu'])){
                        $menulist[$v['id']]['submenu'] = [];
                    }
                }else{
                    $menulist[$v['pid']]['submenu'][$v['id']] = $v;
                }
            }
            setCache('menulist',$this->cacheName,$menulist,$this->expire);
            setCache('list',$this->cacheName,$temp,$this->expire);
            return ['list'=>$temp,'menulist'=>$menulist];
        }
        return ['list'=>[],'menulist'=>[]];
    }

    // 获取菜单
    public function getMenuList(){
        $list = getCache('list',$this->cacheName);
        if(empty($list)){
            $data = $this->setCache();
            $list = $data['list'];
        }
        return $list;
    }

    // 根据字符串去检测port权限是否完整   返回存储进入数据库的port字符串
    public function checkMenuStr($str = ''){
        if(empty($str) || !is_string($str)){
            return null;
        }
        $data = array_filter(explode(' ',$str));
        $menu = $this->getDataList();
        if(empty($menu)){
            return null;
        }
        $port = '';
        foreach($data as $k=>$v){
            if(!preg_match('/^[1-9][0-9]*\|([1-9][0-9]*\,?)+$/',$v)){
                return null;
            }
            $tmp = array_filter(explode('|',$v));
            if(empty($menu[$tmp[0]])){
                return null; // 非法menu id
            }
            $submenu = array_filter(explode(',',$tmp[1]));
            $substr = 0;
            foreach($submenu as $val){
                $substr += pow(2,(int)$val % 100); // 移位得到的结果
            }
            if($port != ''){
                $port .= ';';
            }
            $port .= $tmp[0].":".$substr;
        }
        return $port;
    }

    public function getDataByRouter($menu,$type){
        $list = $this->getMenuList();
        if(empty($list)){
            return false;
        }
        foreach($list as $v){
            if($v['pid'] == 0){
                continue;
            }
            if($v['router'] == $menu || $v['pid'] == $type){
                return $v;
            }
        }
        return false;
    }

    // 获取当前用户菜单
    public function getMenuByUser($user){
        if(empty($user['menulist'])){
            return [];
        }
        $list = $this->getDataList();
        if($user['menulist'] == 'all'){
            return $list;
        }
        $data = array_filter(explode(';',$user['menulist']));
        $user_menu_list = [];
        $temp = [];
        $i = 0;
        foreach($data as $k=>$v){
            $tmp = array_filter(explode(':',$v));
            if(empty($tmp[0] || empty($tmp[1]))){
                continue;
            }
            $user_menu_list[$tmp[0]] = $tmp[1];
            if(empty($list[$tmp[0]])){
                continue;
            }
            $t = $list[$tmp[0]];
            $temp[$i] = $t;
            $temp[$i]['submenu'] = [];
            foreach($t['submenu'] as $key=>$val){
                if( ($tmp[1] & pow(2,$val['id'] % 100 )) > 0){
                    $temp[$i]['submenu'][] = $val;
                }
            }
            $i++;
        }
        return $temp;
    }

}