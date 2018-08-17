<?php
namespace app\index\model;
use	think\Model;

/**
DROP TABLE IF EXISTS api_play_menu;
CREATE TABLE api_play_menu (

) engine=InnoDB charset=utf8;
 */

class Playmenu extends Model {
    protected $table = 'api_play_menu';
    private $cacheName = 'api_play_menu'; // 缓存名字 方便清除缓存
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
        $list = $this->select();
        if(!empty($list)){
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
     * 获得游戏类型列表
     */
    public function getGameTypeList(){
        $list=$this->query('select js_tag from api_play_menu group by js_tag');

        return json_encode($list);
    }
    public function getGamePlayList(){
        $list=$this->query('select * from api_game_list ');

        return json_encode($list);
    }

    public function getPlayMenus($do_cache=true, $flag = 3) {
        // 使用缓存
        $time = time();
        if ($do_cache) {
            $arr = getConfig("game_play_menu", true);
            if ($arr && $arr['expire'] > $time - 1200) {
                return $arr['data'];
            }
        }
        $res = $this->query("SELECT * FROM api_play_menu ORDER BY js_tag ASC, menuid ASC");
        $cache = array();
        $cache['expire'] = time();
        $cache['data'] = array();
        foreach($res as $key => $val) {
            $cache['data'][$val['js_tag']][] = $val;
        }
        setConfig("game_play_menu", $cache);

        return $cache['data'];
    }
    // js_tag获取游戏菜单
    public function getPlayMenusByJsTag($js_tag, $showtype=3) {
        $data = $this->getPlayMenus(true, $showtype);
        $newdata = array();
        foreach($data[$js_tag] as $key => $val) {
            if (((int)$val['showtype'] & $showtype)>0) {
                $newdata[] = $val;
            }
        }
        // print_r($data[$js_tag]);die;
        return $newdata;
    }
    // id获取游戏菜单   --->玩法开关专用
    public function getPlayConfigById2($id) {
        $configs = $this->getPlayConfigs2(true);
        if (!$configs) {
            return null;
        }
        return $configs[$id];
    }

    // 获取所有游戏的菜单 --->玩法开关专用
    public function getPlayConfigs2($do_cache=true) {
        // 使用缓存
        $time = time();
        if ($do_cache) {
            $arr = getConfig("game_play_config2", true);
            if ($arr && $arr['expire'] > $time -(86400*30) ) {
                // 判断设备
                return $arr['data'];
            }
        }
        $res = $this->query("SELECT * FROM api_play_config WHERE 1 ORDER BY gameid ASC, playid ASC");
        $cache = array();
        $cache['expire'] = time();
        $cache['data'] = array();
        foreach($res as $key => $val) {
            $cache['data'][$val['gameid']][$val['playid']] = $val;
        }
        setConfig("game_play_config2", $cache);
        // 判断设备
        return $cache['data'];
    }
    public function getGameByJsTag($jstag) {
        $gamelist = $this->getGameList(0,0,0,0);
        $list = array();
        foreach($gamelist as $key => $val) {
            if ($val['js_tag'] == $jstag) {
                $list[$val['game_id']] = $val;
            }
        }
        return $list;
    }
    public function getGameList($type=0, $speed=0, $status=0, $keys=0) {
        $config = getCache("game_{$type}_{$speed}_{$status}_{$keys}", "game_list", true);
        if ($config) {
            return $config;
        }

        $webConfigModel = new \Home\Model\GameWebConfigModel();
        $webconf = $webConfigModel->getData();
        $where = '';
        if ($type>0) {
            $where .= "type = '$type'";
        }
        if ($speed > 0) {
            $where .= empty($where) ? '' : ' AND ';
            $where .= "speed = '".($speed == 2 ? 0 : 1)."'";
        }
        if ($status > 0) {
            $where .= empty($where) ? '' : ' AND ';
            switch($status) {
                case 1:
                    $where .= "enable > '0'";
                    break;
                case 2:
                    $where .= "enable = '0'";
                    break;
            }
        }
        $res = $this->where($where)->order("hot ASC")->select();
        $data = array();
        foreach($res as $k => $v) {
            $v['game_name'] = preg_replace("/\{prefix\}/", (!empty($webconf['lottery_prefix']) ? $webconf['lottery_prefix'] : '三分'), $v['game_name']);
            if ($keys) {
                $data[$v['game_id']] = $v;
            }else{
                array_push($data, $v);
            }
        }
        setCache("game_{$type}_{$speed}_{$status}_{$keys}", "game_list", $data);
        return $data;
    }



}