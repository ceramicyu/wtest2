<?php
namespace app\index\model;
use	think\Model;

/**
drop table if exists api_admin_log;
CREATE TABLE api_admin_log (
`id` int(11) unsigned not null AUTO_INCREMENT comment 'id',
`username` varchar(20) not null default '' comment '操作账号',
`create_time` int(11) not null default '0' comment '操作时间',
`update_time` int(11) not null default '0' comment '操作时间',
`content` text comment '操作内容',
`title` varchar(50) not null default '' comment '操作标题',
`keywords` varchar(255) not null default '' comment '操作关键字',
`ip` int(11) not null default '0' comment '操作ip',
primary key (id)
key (username)
) engine=InnoDB charset=utf8;


drop table if exists api_admin_login_log;
CREATE TABLE api_admin_login_log (
`id` int(11) unsigned not null AUTO_INCREMENT comment 'id',
`username` varchar(20) not null default '' comment '操作账号',
`create_time` int(11) not null default '0' comment '操作时间',
`update_time` int(11) not null default '0' comment '操作时间',
`ip` int(11) not null default '0' comment '操作ip',
primary key (id)
) engine=InnoDB charset=utf8;

 */

class AdminLog extends Model {
    protected $insert = ['create_time','ip','username']; // 自动完成
    static private $lang = [];
    protected function setCreateTimeAttr(){
        return time();
    }
    protected function getCreateTimeAttr($value){
        return date("Y-m-d H:i:s",$value);
    }

    protected function setIpAttr(){
        return ip2long(request()->ip());
    }
    protected function getIpAttr($value){
        return long2ip($value);
    }
    protected function setUsernameAttr(){
        return session('_admin.username');
    }

    /*
     * 获取操作语言包
     * */
    private function getLang(){
        if(empty(self::$lang)){
            self::$lang = config('cp_lang');
        }
        return self::$lang;
    }

    /*
     * $title : 控制器的名字
     * $name  : 操作的接口名字
     * $data  : 操作的内容  data是request参数数组  ---》根据下标进行解读   解读文件在配置的语言包里面   默认去除 post_前缀
     * */
    public function addLog($title,$name,$data,$extra_keyword = ''){
        $name = preg_replace('/^Ajax_/','',$name);
        if($name == 'list'){
            // 读取列表不加入日志
            return;
        }
        $title = strtolower($title);
        $name = strtolower($name);
        $logData = [];
        $lang = $this->getLang();
        $key = $title."_".$name;
        $logData['keywords'] = $key;
        if(!empty($lang[$key])){
            $logData['title'] = $lang[$key];
            $logData['keywords'] .= ','.$lang[$key];
        }else if(!empty($lang[$title])){
            $logData['title'] = $lang[$title];
            $logData['keywords'] .= ','.$lang[$title];
        }else{
            $logData['title'] = '未知语言 ： '.$title." -- {$name}";
        }
        if($extra_keyword && is_string($extra_keyword)){
            $logData['keywords'] .= ','.$extra_keyword;
        }
        $param_key = $title."_param";
        $content = '';
        /* data参数解析 */
        if($data){
            if(is_string($data)){
                $content .= $data;
            }else{
                $content .= json_encode($data);
//                if(empty($lang[$param_key])){
//                    $content .= "参数解析失败,已下是原数据:\n";
//                    foreach($data as $k=>$v){
//                        if(!is_string($v)){
//                            $v = json_encode($v);
//                        }
//                        $content .= "{$k} ==> {$v}\n";
//                    }
//                }else{
//
//                }
            }
        }
        $logData['content'] = $content;
        return $this->save($logData);
    }

    // 登录日志
    public function addLoginLog(){
        $data = [];
        $data['username'] = session('_admin.username');
        $data['create_time'] = time();
        $data['ip'] = ip2long(request()->ip());
        $data['update_time'] = 0;
        $this->table('api_admin_login_log')->insert($data);
    }
    // 登录日志
    public function addERRLog($user,$detail){
        $data = [];
        $data['username'] = $user;
        $data['create_time'] = time();
        $data['ip'] = ip2long(request()->ip());
        $data['update_time'] =1;
//        $data['detail']="<b style='color: red'>密码错误()</b>";
        $data['detail']=$detail;
        $this->table('api_admin_login_log')->insert($data);
    }

    /**
     * @param $user
     * @param $detail
     * `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
    `gid` int(11) unsigned zerofill NOT NULL COMMENT '皇冠赛事id',
    `league_name` varchar(255) NOT NULL COMMENT '联赛',
    `home_name` varchar(255) NOT NULL COMMENT '主队',
    `visitor_name` varchar(255) NOT NULL COMMENT '客队',
    `begin_time` bigint(20) NOT NULL COMMENT '开始时间',
    `end_time` bigint(20) NOT NULL COMMENT '结束时间',
    `result_data` json NOT NULL COMMENT '赛果数据',
    `user` varchar(255) NOT NULL COMMENT '操作人员',
    `time` bigint(20) NOT NULL COMMENT '操作时间',
     */
    public function addSportLog($logData){
        $data = [];
        $data['gid'] = $logData['gid'];
        $data['league_name'] = $logData['league_name'] ;
        $data['home_name'] = $logData['home_name'];
        $data['visitor_name'] =$logData['visitor_name'];
        $data['begin_time'] =$logData['begin_time'];
        $data['end_time'] = $logData['end_time'];
        $data['result_data'] =$logData['result_data'];
        $data['user']= session('_admin.username');
        $data['time']= time();
        $data['ip']= ip2long(request()->ip());;

        $this->table('api_log')->insert($data);

    }
}