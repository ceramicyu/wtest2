<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
include('const.def.php');
// 应用公共文件

function JCode($msg = 0, $param=null, $data = null) {
	$arr = array(
        'msg' => $msg ?$msg: 0,
        'param' => $param ?$param: '',
        'data' => $data ? $data:[],
	);
	exit(json_encode($arr));
}

function JCodeArray($msg = 0, $param=null, $data = null) {
    $arr = array(
        'msg' => $msg ?$msg: 0,
        'param' => $param ?$param: '',
        'data' => $data ? $data:[],
    );
    return $arr;
}

/**
 * 请求
 * @param  [type] $url     请求地址
 * @param  [type] $data    请求数据结构
 * @param  string $timeout 超时时间(秒)
 * @return [type]          [description]
 */
function https_post($url,$data=array(), $timeout=0) { 
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url); 
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_VERBOSE, 1);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.104 Safari/537.36 Core/1.53.3103.400 QQBrowser/9.6.11372.400');
    if ($timeout>0) {
    	curl_setopt($curl, CURLOPT_TIMEOUT,$timeout);   //只需要设置一个秒的数量就可以
    }
    $result = curl_exec($curl);
    if (curl_errno($curl)) {
        return array('Errno:'.curl_error($curl), 400);
    }
    $pp =array($result, curl_getinfo($curl, CURLINFO_HTTP_CODE));
    curl_close($curl);
    return $pp;
}

// 判断是否是ajax跟put请求
function isAjax(){
    if(request()->isAjax() && request()->isPut()){
        return true;
    }
    return false;
}

// 检测用户名跟密码
function checkUser($user,$len = 6){
    if(preg_match('/^[a-z0-9]{'.$len.',20}$/i',$user)){
        return true;
    }
    return false;
}

// 用户名跟加密方式
function getUserPass($user,$pass){
    return md5($user.":".$pass);
}

function getRandStr($len = 20,$num = false){
    if ($num === false) {
        $str = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890';
    }else{
        $str = '1234567980';
    }
    if (!is_numeric($len) || empty($len)) {
        $len = 4;
    }
    $key = strlen($str) -1;
    $res = '';
    for($i = 0; $i < $len; $i++){
        $res .= $str[rand(0,$key)];
    }
    return $res;
}

// 获取指定前缀的put变量
function getDataBy($prex,$method = 'put') {
    if($method == 'put'){
        $arr = input('put.');
    }else if($method == 'post'){
        $arr = input('post.');
    }else{
        $arr = input('get.');
    }
    $len = strlen($prex);
    $data = array();
    foreach($arr as $key=>$val) {
        if (substr($key,0,$len) == $prex) {
            $data[substr($key, $len)] = trim($val);
        }
    }
    return $data;
}

function initCache(){
    $option = config('redis');
    if(empty($option['host'])){
        return ''; // 不使用redis缓存
    }
    $cache = \app\index\controller\RedisCache::_instance($option);
    return $cache;
}
function getConfig($name){
    $cache = initCache();
    if($cache){
        // redis缓存
        return $cache->get($name);
    }else{
        // 文件缓存
    }
}

function setConfig($name,$value,$expire = 600){
    $cache = initCache();
    if($cache){
        // redis缓存
        return $cache->set($name,$value,$expire);
    }else{
        // 文件缓存
    }
}

function delConfig($name){
    $cache = initCache();
    if($cache){
        // redis缓存
        return $cache->rm($name);
    }else{
        // 文件缓存
    }
}

function delCache($name,$type,$prex = false){
    $cache = initCache();
    if($cache){
        // redis缓存
        $cache->hdel($type,$name,$prex);
    }else{
        // 文件缓存
    }
}

function getCache($name,$type){
    $cache = initCache();
    if($cache){
        // redis缓存
        return $cache->hget($type,$name);
    }else{
        // 文件缓存
    }
}

function setCache($name,$type,$arr,$expire = 600){
    $cache = initCache();
    if($cache){
        // redis缓存
        $cache->hset($type,$name,$arr,$expire);
    }else{
        // 文件缓存
    }
}

// 检测数据是否是id
function checkId($id){
    if(!preg_match('/^[1-9][0-9]*$/',$id)){
        return false;
    }
    return true;
}

function encrty_sign($string,$operation,$key=''){
    $key=md5($key);
    $key_length=strlen($key);
    $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
    $string_length=strlen($string);
    $rndkey=$box=array();
    $result='';
    for($i=0;$i<=255;$i++){
        $rndkey[$i]=ord($key[$i%$key_length]);
        $box[$i]=$i;
    }
    for($j=$i=0;$i<256;$i++){
        $j=($j+$box[$i]+$rndkey[$i])%256;
        $tmp=$box[$i];
        $box[$i]=$box[$j];
        $box[$j]=$tmp;
    }
    for($a=$j=$i=0;$i<$string_length;$i++){
        $a=($a+1)%256;
        $j=($j+$box[$a])%256;
        $tmp=$box[$a];
        $box[$a]=$box[$j];
        $box[$j]=$tmp;
        $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
    }
    if($operation=='D'){
        if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8)){
            return substr($result,8);
        }else{
            return'';
        }
    }else{
        return str_replace('=','',base64_encode($result));
    }
}
// 解密 $key是密钥
function Dncrty($string,$key = ''){
    if (!is_string($string)) {
        return '';
    }
    if (empty($key) || !is_string($key)) {
        return '';
    }
    $res = encrty_sign($string,"D",$key);
    return json_decode($res,true);
}

// 加密 $key是密钥
function Encrty($data = '',$key = ''){
    if (empty($key) || !is_string($key)) {
        return '';
    }
    $string = json_encode($data);
    $res = encrty_sign($string,"E",$key);
    return $res;
}

// 权限检测函数  也可以用于前端界面
function checkRole($type,$id,$menu = true){
    $user = session('_admin');
    if(!$user){
        return false;
    }
    if($user['accesstype'] == 1){
        // 超级管理员
        return true;
    }
    if($menu){
        // 菜单检测
        if($user['menulist'] == 'all'){
            return true;
        }
        if(empty($user['menulist'])){
            return false;
        }
        $data = explode(';',$user['menulist']);
        foreach($data as $k=>$v){
            $tmp = explode(':',$v);
            if(empty($tmp[0]) || empty($tmp[1]) || $tmp[0] != $type){
                continue;
            }
            if( ($tmp[1] & pow(2,(int)$id % 100)) > 0){
                return true;
            }
        }
        return false;
    }else{
        // 菜单检测
        if($user['portlist'] == 'all'){
            return true;
        }
        if(empty($user['portlist'])){
            return false;
        }
        $data = explode(';',$user['portlist']);
        foreach($data as $k=>$v){
            $tmp = explode(':',$v);
            if(empty($tmp[0]) || empty($tmp[1]) || $tmp[0] != $type){
                continue;
            }
            if( ($tmp[1] & pow(2,(int)$id % 100)) > 0){
                return true;
            }
        }
        return false;
    }
}

// 写入日志
function addLog($title,$name,$data,$keyword = ''){
    $model = model('AdminLog');
    $model->addLog($title,$name,$data,$keyword);
}
// 写入日志
function addSportLog($data){
    $model = model('AdminLog');
    $model->addSportLog($data);
}

function trimall($str)//删除空格 换行 以及tab
{
    $qian=array("\t","\n","\r");
    $hou=array("","","");
    return str_replace($qian,$hou,$str);
}

// 检测域名  没有http  https 开头
function checkUrl($url = ''){
    if (!preg_match("/^(http|https)\:\/\/[a-zA-Z0-9\_\?\=\-]+(\.[a-zA-Z0-9\_\/\/\:\?\=\-]+)+$/i",$url)) {
        return false;
    }
    return true;
}

// 检测域名  没有http  https 开头
function checkUrl2($url = ''){
    if (!preg_match("/^[a-zA-Z0-9\_\?\=\-]+(\.[a-zA-Z0-9\_\/\/\:\?\=\-]+)+$/i",$url)) {
        return false;
    }
    return true;
}


// 上传彩票站资源
function getUploadPath($type=0, $website='') {
    $root = "{$website}";
    switch($type) {
        case UPLOAD_TYPE_BANNER:	// 幻灯片
            return "{$root}/Uploads/banner";
        case UPLOAD_TYPE_EVENT: 	// 活动图片
            return "{$root}/Uploads/event";
        case UPLOAD_TYPE_CACHE: 	// 缓存图片
            return "{$root}/Uploads/cache";
        case UPLOAD_TYPE_QRCODE: 	// 二维码
            return "{$root}/Uploads/qrcode";
        case UPLOAD_TYPE_USER:		// 用户头像
            return "{$root}/Uploads/user";
        case UPLOAD_TYPE_NOTICE: 	// 公告图片
            return "{$root}/Uploads/notice";
        case UPLOAD_TYPE_CPICON: 	// 彩种图标
            return "{$root}/Uploads/cpicon";
        case UPLOAD_TYPE_LOGO:		// LOGO
            return "{$root}/Uploads/logo";
        case UPLOAD_TYPE_PACK:		// APP 打包图片
            return "{$root}/Uploads/apppack";
        default:
            return '';
    }
}

// 获取当前用户的IP
function getCurIP() {
    $iplist = explode(",",trim(getFullIP()));
    if (empty($iplist)){
        return "";
    }
    return $iplist[0];
}

// 获取当前用户的IP路径
function getFullIP() {
    return isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
}