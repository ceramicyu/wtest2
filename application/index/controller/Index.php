<?php
namespace app\index\controller;
use app\index\model\AdminLog;

class Index extends Common
{	
	// 登录页面
    public function index()
    {
        if(!session('_admin.username')){
            return view('login');
        }
        // 载入菜单
        $data = [];
        $arr = model("Menu")->getMenuByUser(session('_admin'));
//        $data['list'] = json_encode($arr);
        $data['list'] = $arr;
        $data['num'] = db('User')->count();
        //读取接口
        $arr2 = model("Port")->getPortByUser(session('_admin'));
        $data['port'] = json_encode($arr2);
        $data['admin'] = session("_admin");
        return view('index',$data);
    }

    public function login(){
        if(isAjax()){
            $captcha = input('verifycode');
            if(!captcha_check($captcha)){
                //验证失败
                JCode(ERROR_VERIFY,'验证码错误');
            };
            $username =  input('username','');
            $password =  input('password','');
            if(!checkUser($username)){
                JCode(ERROR,'用户名不合法');
            }
            if(!checkUser($password)){
                JCode(ERROR,'密码不合法');
            }
            $model = model('Admin');// 管理员表
            $where = [];
            $where['username'] = $username;
            $user = $model->where($where)->find();
            if(!$user){
                JCode(ERROR,'账号不存在');
            }
            $pass = getUserPass($username,$password);

            if($user['lock_status'] >= 3 && $user['lock_ip']==ip2long(request()->ip())){
               $rest_time=ceil(($user['lock_time']+3600-time())/60);
                if($rest_time <= 0){
                    $user['lock_status']=0;
                }else{
                    JCode(ERROR,'您的账号已经被锁定,请您'.$rest_time.'分钟后再登录');
                }
            }

            if($user['password'] != $pass){
                $detail="<b style='color: red;'>密码错误第".($user['lock_status']+1)."次</b>";
                db('Admin')->where(['username'=>$username])->setField(['lock_status'=>($user['lock_status']+1),'lock_ip'=>ip2long(request()->ip()),'lock_time'=>time()]);
                model('AdminLog')->addERRLog($username,$detail);

                JCode(ERROR,'密码不正确，还剩余'.(3-$user['lock_status']).'次机会');
            }
            if($user['locked'] != 1){
                JCode(ERROR,'您的账号已经被锁定');
            }
            $res = $model->initAdminData($user);
            if(!$res){
                JCode(ERROR,'登录失败');
            }
            // 登录写入日志
            model('AdminLog')->addLoginLog();
            JCode(SUCCESS,'登录成功');
        }else{
            JCode(ILLEGAL,'非法访问');
        }
    }

    public function logout(){
        session('_admin',null);
        if(isAjax()){
            JCode(0,"msgok");
        }
        $this->redirect('/index');return;
    }


}