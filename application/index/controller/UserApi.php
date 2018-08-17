<?php
namespace app\index\controller;

use app\index\model\Gamelist;
use Aws\Health\HealthClient;
use Think\Model;

class UserApi extends PublicController
{
    private $userCache = 'ownerCache';
    private $expire = 60; // 60秒
    // 业主的所有api接口地址
    public function request($name){
        if(!isAjax()){
            JCode(ILLEGAL,'非法访问');
        }
        $func = "Ajax_".$name;
        if(!method_exists($this,$func)){
            JCode(ERROR,'模块'.$name.'不存在');
        }
        $this->checkRole(API_USER,$name,false);
        $this->checkUserInfo();
        $this->$func();
    }

    // 获取业主基本信息
    private function Ajax_getUserInfo(){
        $data = [];
        $data['reg'] = empty($res['reg'])?0:$res['reg'];
        $data['online'] = empty($res['online'])?0:$res['online'];
//        JCode(SUCCESS,'msgok',$data);
        $cache = getCache('getUserInfo:'.$this->user['id'],$this->userCache);
        if($cache){
            JCode(SUCCESS,'msgok1',$cache);
        }
        // 远程访问业主接口
        $res = $this->getUserDataByApi($this->user,'getUserInfo',[]);
        if($res['msg'] > 0){
            JCode(ERROR,$res['param']);
        }
        $res = $res['data'];
        $data = [];
        $data['reg'] = empty($res['reg'])?0:$res['reg'];
        $data['online'] = empty($res['online'])?0:$res['online'];
        setCache('getUserInfo:'.$this->user['id'],$this->userCache,$data,$this->expire);
        JCode(SUCCESS,'msgok',$data);
    }


    private function Ajax_spSetResult(){
        $data["gid"]=input("gid");
        $data["result_data"]=input("result_data",'');


        $idlist = input('idlist','');
        if(empty($idlist)){
            JCode(ERROR,'请选择业主');
        }
        if(!$user = self::$userModel->getUserInfoByIdlist($idlist)){
            JCode(ERROR,'获取业主信息失败');
        }

        $title = "userAPi";
        $name = 'spSetResult';
        $content = "写入体彩赛果 ";
        addLog($title,$name,$content);
        /***************************************/
        $result = []; // 记录信息， 成功或则失败
        $suc = false; // 默认失败
        foreach ($user as $k=>$v){
            // 远程访问业主接口
            $res = $this->getUserDataByApi($v,'spSetResult',$data);
            $result[$v['id']] = [];
            $result[$v['id']]['username'] = $v['username']; // 失败的业主名字
            if($res['msg'] > 0){
                $result[$v['id']]['param'] = "<b style='color: red'>".$res['param']."</b>";
                $result[$v['id']]['status']="<b style='color: red'>设置失败</b>" ;
            }else{
                $result[$v['id']]['param'] = "<b style='color: green'>".$res['param']."</b>";
                $result[$v['id']]['status']="<b style='color: green'>设置成功</b>" ;
            }
            continue;
            $suc = true;
        }
        if(false === $suc){
//            JCode(ERROR,'彩种设置失败');
        }
        JCode(SUCCESS,'msgok',$result);

        /************************************/
        JCode(0,'msgok',$user);
    }

    /***
     * 对于冠军赛事，需要先获取该赛事的盘口（盘口中包含最终冠军）,然后设置冠军赛果
     * 两步走
     * @ type : get 获取
     * @ type : set
     */
    private function Ajax_spSetChampionResult(){
        $data["gid"]=input("gid");
        $data["result_data"]=input("result_data",'');
        $idlist = input('idlist','');
        $type=input("type","get");
        if(empty($idlist)){
            JCode(ERROR,'请选择业主');
        }
        if(!$user = self::$userModel->getUserInfoByIdlist($idlist)){
            JCode(ERROR,'获取业主信息失败');
        }
        if($type=="get"){
            //第一步
            foreach ($user as $k=>$v){
                // 远程访问业主接口
                $res = $this->getUserDataByApi($v,'spSetChampionResult',$data);
                if( $res && $res["data"] &&  $res["data"]["gid"] ){
                    $res["data"]["begintime"]=date("Y-m-d  H:i:s",$res["data"]["begin_time"]/1000);
                    JCode(0,'msgok', $res["data"]);
                }
            }
            JCode(1,'获取赛事失败');
        }else if($type=="set"){
            //第二步
            $title = "userAPi";
            $name = 'spSetResult';
            $content = "写入体彩赛果 ";
            addLog($title,$name,$content);
            $data['league_name'] =input("league_name",'');
            $data['home_name'] = input("home_name",'');
            $data['visitor_name'] =input("visitor_name",'');
            $data['begin_time'] =input("begin_time",'');
            $data['end_time'] = input("end_time",'');
            addSportLog($data);
//            die;
            /***************************************/
            $result = []; // 记录信息， 成功或则失败
            $suc = false; // 默认失败
            foreach ($user as $k=>$v){
                // 远程访问业主接口
                $res = $this->getUserDataByApi($v,'spSetResult',$data);
                $result[$v['id']] = [];
                $result[$v['id']]['username'] = $v['username']; // 失败的业主名字
                if($res['msg'] > 0){
                    if(strpos( $res['param'],"gid_UINQUE")){
                        $result[$v['id']]['param'] = "<b style='color: red'>重复设置".""."</b>";
                    }else{
                        $result[$v['id']]['param'] = "<b style='color: red'>".$res['param']."</b>";
                    }

                    $result[$v['id']]['status']="<b style='color: red'>设置失败</b>" ;
                }else{
                    $result[$v['id']]['param'] = "<b style='color: green'>".$res['param']."</b>";
                    $result[$v['id']]['status']="<b style='color: green'>设置成功</b>" ;
                }
                continue;
                $suc = true;
            }
            if(false === $suc){
//            JCode(ERROR,'彩种设置失败');
            }
            JCode(SUCCESS,'msgok',$result);
        }else{
            JCode(ERROR,'参数错误');
        }

    }

    /**
     * 获取待开将赛事
     * INSERT INTO `api_menu`(`id`, `pid`, `appname`, `status`, `router`, `flag`, `icon`) VALUES (70002, 7, '补开列表', 1, 'splist', 0, '');
     */
    private function Ajax_GetListOfMatchToBeOpened(){
        $gid=input("gid","");
        $data=[];
        $data['gid']=$gid;
        $user=model("user")->select();
        foreach ($user as $k=>$v){
            // 远程访问业主接口
            $res = $this->getUserDataByApi($v,'GetListOfMatchToBeOpened',$data);
            if( $res && $res["data"]  ){
                   foreach($res["data"] as $k1=>$v1){
                       $res["data"][$k1]["begin_time"]=date("Y-m-d H-i",$v1["begin_time"]/1000);
                       $res["data"][$k1]["end_time"]=date("Y-m-d H-i",$v1["end_time"]/1000);
                   }
                JCode(0,'msgok', ["list"=>$res["data"],"pageinfo"=>100] );
            }
        }
    }


}