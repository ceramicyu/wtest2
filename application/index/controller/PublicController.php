<?php
namespace app\index\controller;

class PublicController extends Common
{
    protected $pageNum = 100;// 默认100条
    protected $order = 'id asc';
    private $api_name = '/index.php/OwnerApi/request'; // 业主接口名字
    static protected $userModel = null;// 用户user模型
    protected $user = null; // 业主
//    private $plist = "http://upload.bxvip588.com/index.php/AppApi/request";
    private $plist = "http://apk.bxvip588.com/index.php/AppApi/request";
    private $app_token = 'd20a1bf73c288b4ad4ddc8eb3fc59274704a0495';
    static protected $adminModel = null;
    static protected $menuModel = null;
    static protected $portModel = null;
    protected $admin = []; // 管理员信息
    // 公共类  必须登录   继承基类
    public function __construct()
    {
        parent::__construct();
        if(self::$adminModel == null){
            self::$adminModel = model('Admin');
        }
        $this->checkAdminLogin();
    }

    // 检测管理员 是否已掉线
    private function checkAdminLogin(){
        $this->admin = session('_admin');
        if(!$this->admin || empty($this->admin['token']) || !self::$adminModel->checkToken($this->admin)){
            $this->notLogin();
        }
    }

    // 未登录界面控制
    private function notLogin(){
        session('_admin',null);
        if(isAjax()){
            JCode(LOGOUT,'请先登录');
        }else{
            $this->redirect('/index');return;
        }
    }

    // 权限界面控制
    private function noAccess(){
        if(isAjax()){
            JCode(NOALLOW,'您没有权限访问');
        }else{
            $this->redirect('/index');exit; // 可以考虑跳转到权限控制界面
        }
    }

    // 权限检测
    protected function checkRole($type,$path, $menu = true){
        if($path == 'list'){
            return true;
        }
        if($menu){
            // 菜单检测
            if(self::$menuModel == null){
                self::$menuModel = model('Menu');
            }
            $menu = self::$menuModel->getDataByRouter($path,$type);
            if(!$menu){
//                $this->noAccess();
                return true; // 找不到菜单的名字 那么就不需要权限检测
            }
            $res = checkRole($menu['pid'],$menu['id']);
            if(!$res){
                $this->noAccess();
            }
        }else{
            // 接口检测
            if(self::$portModel == null){
                self::$portModel = model('Port');
            }
            $menu = self::$portModel->getDataByRouter($path,$type);

            if(!$menu){
//                $this->noAccess();
                return true; // 找不到接口的名字 那么就不需要权限检测
            }
            $res = checkRole($menu['pid'],$menu['id'],false);
            if(!$res){
                $this->noAccess();
            }
        }
    }

    // 检测业主信息是否正确
    protected function checkUserInfo(){
        // 获取业主信息
        $id = input('id','');
        $ids = input('idlist',''); // 多个业主  1,2,3,4,
        if(self::$userModel == null){
            self::$userModel = model('User');
        }
        if($ids){
            $user = self::$userModel->getUserInfoByIdlist($ids);
        }else{
            $user = self::$userModel->getDataById($id);
        }
        if(!$user){
            JCode(ERROR,'业主信息错误');
        }
        $this->user = $user;
    }

    // 分页函数
    protected function page($table,$where = [],$field = '*',$m = false){
        $pageIndex = input('pageid',1); // 第一页
        $first = input('firstRequest',1);
        if($m){
            $model = db($table);
        }else{
            $model = model($table);
        }
        if (!preg_match('/^[0-9]+$/',$pageIndex) || $pageIndex <= 0) {
            $pageIndex = 1;
        }
        $limit = (($pageIndex-1) * $this->pageNum).",".$this->pageNum;
        $list = $model->field($field)->where($where)->order($this->order)->limit($limit)->select();
        $pageCount = count($list);
        if($first == 1){
            $pageCount = $model->where($where)->count();
        }
        return ['pageinfo'=>$pageCount,'list'=>$list];
    }

    // 加密函数
    private function encrty($data,$key){
        try{
            return Encrty($data,$key);
        }catch(\Exception $exception){
            return '';
        }
    }

    // 解密函数
    private function dncrty($string,$key){
        try{
            return Dncrty($string,$key);
        }catch(\Exception $exception){
            return '';
        }
    }

    protected function setEnomToPlist($user,$enom,$param_type,$comment = ''){
        $data = [];
        $data['ac'] = 'addUserEnom2';
        $data['enom'] = $enom;
        $data['website'] = $user["website"];
        $data['param_type'] = $param_type;
        $data['comment'] = $comment;
        $data['key'] = $this->app_token;
        return https_post($this->plist,$data);
    }

    // 获取业主接口函数
    protected function getUserDataByApi($user,$api,$extra = null){
        if(!empty($user['inner_url'])){
            $url = $user['inner_url'];
        }else if(!empty($user['admin_url'])){
            $url = $user['admin_url'];
        }else{
            return JCodeArray(ERROR,'业主的域名不能为空');
        }
        $data = [];
        $data['func'] = $api;
        $data['data'] = $extra;
        $postdata = $this->encrty($data,$user['token']); // 加密
        if(empty($postdata)){
            return JCodeArray(ERROR,'数据加密失败');
        }
        $res = https_post($url.$this->api_name,['data'=>$postdata]);
        if(empty($res[1])){
            return JCodeArray(ERROR,'curl超时:'.$res[0]);
        }
        if($res[1] === 400){
            // 错误
            return JCodeArray(ERROR,$res[0]);
        }
        $data = json_decode($res[0],true); // json转换

        if(!$data){
            return JCodeArray(ERROR,'curl访问错误:'.$res[0]);
        }

        if(!empty($data['data'])){
            // 解密data
            $returndata = $this->dncrty($data['data'],$user['token']);
            if(empty($returndata)){
                return JCodeArray(ERROR,'数据解密失败'); // 数据解密失败
            }
        }else{
            $returndata = [];
        }
        if($data['msg'] > 0){
            return JCodeArray(ERROR,$data['param'],$returndata);
        }
        return JCodeArray(SUCCESS,'msgok',$returndata);
    }

    // 获取开奖号码
    protected function getKjBalls($game,$date,$qishu = false){
        if(empty($game['tag'])){
            return false;
        }
        $date = date("Y-m-d",strtotime($date)); // 转换日期格式

        $res = https_post(config('get_kjballs_by_tag')."?tag=".$game['tag']."&date=".$date); // http://52.69.209.225/lottery-2.php?tag=cqssc&date=2017-12-27
        if(empty($res[1]) || $res[1] === 400){
            return false;
        }
        // 去除 0 \n \r 等
        $json = trimall($res[0]);
        if(preg_match('/}0$/',$json)){
            $json = substr($json,0,-1);
        }
        // 结果是json结构
        $datalist = json_decode($json,true);
        if(!$datalist || empty($datalist['data'])){
            return false;
        }
        $balls_data = $datalist['data'];

        if($qishu){
            // 如果单独一期  则返回期数跟号码
            foreach($balls_data as $k=>$v){
                if($v['expect'] == $qishu){
                    return $v; // 选中期数的数据
                }
            }
            return false;
        }
        return $balls_data; // 当日的所有数据
    }

}