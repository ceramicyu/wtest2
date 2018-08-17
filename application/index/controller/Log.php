<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2017/12/27
 * Time: 16:06
 */

namespace app\index\controller;

class Log extends PublicController
{
    public function request($name){
        if(!isAjax()){
            JCode(ILLEGAL,'非法访问');
        }
        $func = "Ajax_".$name;
        if(!method_exists($this,$func)){
            JCode(ERROR,'模块'.$name.'不存在');
        }
        $this->checkRole(API_LOG,$name,false);
        $this->$func();
    }

    public function Ajax_loglist(){
        $username = input('username','');
        $begin_time = input('begin_time','');
        $end_time = input('end_time','');
        $where='';
        if($username){
            if($where!=''){
                $where.=" AND ";
            }
            $where.="username like '%$username%'";
        }
        if($begin_time){
            if($where!=''){
                $where.=" AND ";
            }
            $begin_time=strtotime($begin_time." 0:0:0");
            $where.=" create_time >= $begin_time ";
        }
        if($end_time){
            if($where!=''){
                $where.=" AND ";
            }
            $end_time=strtotime($end_time." 23:59:59");
            $where.=" create_time <= $end_time ";

        }

            $this->order= " create_time desc ";
        $data = $this->page("AdminLog",$where);

        JCode(SUCCESS,'msgok', $data);

    }

    public function Ajax_loginLog(){
        $username = input('username','');
        $begin_time = input('begin_time','');
        $end_time = input('end_time','');
        $where='';
        if($username){
            if($where!=''){
                $where.=" AND ";
            }
            $where.="username like '%$username%'";
        }
        if($begin_time){
            if($where!=''){
                $where.=" AND ";
            }
            $begin_time=strtotime($begin_time." 0:0:0");
            $where.=" create_time >= $begin_time ";
        }
        if($end_time){
            if($where!=''){
                $where.=" AND ";
            }
            $end_time=strtotime($end_time." 23:59:59");
            $where.=" create_time <= $end_time ";

        }
        $this->order= " create_time desc ";
        $data = $this->page("Admin_login_log",$where,"*",true);
        JCode(SUCCESS,'msgok', $data);
    }

    /**
     * 赛果记录
     */
    private function Ajax_getAdminLog(){
        $gid=input("gid","");
        $ip=input("ip","");
        $username=input("username","");
        $keyword=input("keyword","");
        $m=model("log");
        $where=" 1 ";
        if(is_numeric($gid)){
            $where.=" and gid={$gid} ";
        }
        if($ip){
            $ip=ip2long($ip);
            $where.=" and ip='{$ip}' ";
        }
        if($username){
            $where.=" and user='{$username}' ";
        }
        if($keyword){
            $where.=" and ( league_name like '%{$keyword}%' or home_name  like '%{$keyword}%' or visitor_name like '%{$keyword}%' )";
        }
        $res= $m->where($where)->order(" id desc")->select();
        JCode(0,'msgok',["list"=>$res,"pageinfo"=>count($res)]);
    }


}