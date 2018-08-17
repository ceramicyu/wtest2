<?php
namespace app\index\controller;
use \think\cache\driver\Redis;
class RedisCache extends Redis
{
    private static $_instance = null; // redis的静态

    public static function _instance($option){
        if(!self::$_instance){
            self::$_instance = new RedisCache($option);
        }
        return self::$_instance; // redis的实例
    }

    public function hget($key,$field){
        $value = $this->handler->hGet($this->getCacheKey($key),$field);
        if(empty($value)){
            return false;
        }
        $data = json_decode($value,true);
        if(empty($data)){
            return false;
        }
        if(!isset($data['data']) || !isset($data['expire']) || !isset($data['time'])){
            $this->hdel($this->getCacheKey($key),$field);
            return false;
        }
        if($data['expire'] + $data['time'] < time() && $data['expire'] > 0){
            $this->hdel($this->getCacheKey($key),$field);
            return false;
        }
        return $data['data'];
    }

    public function hset($key,$field,$value,$expire = 0){
        if(!is_numeric($expire) || $expire < 0){
            $expire = 0;
        }
        $data = [];
        $data['data'] = $value;
        $data['expire'] = $expire;
        $data['time'] = time();
        $this->handler->hSet($this->getCacheKey($key),$field,json_encode($data));
    }

    public function hdel($key,$field = '*',$prex = false){
        $key = $this->getCacheKey($key);
        if($field === '*'){
            // 删除key下面的所有
            $this->handler->delete($key);
        }else{
            if($prex === true){
                $keys = $this->handler->hKeys($key);
                foreach($keys as $k=>$v){
                    if(preg_match('/^{$key}/',$v)){
                        $this->handle->hDel($key,$v);
                    }
                }
            }else{
                $this->handler->hDel($key,$field);
            }
        }
    }

}