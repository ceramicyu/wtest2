<?php
namespace app\index\controller;

class PageTpl extends PublicController
{
    public function request($name){
        $func = 'page_'.$name;
        if(!method_exists($this,$func)){
            $this->redirect('/index');return;
        }
        $this->checkRole('MENU',$name);
        $arr2 = model("Port")->getPortByUser(session('_admin'));
        $data['port_list'] = json_encode($arr2);
        return $this->$func($data);
    }
    // 用户列表
    private function page_user($data){
        $data['gamelist'] = model('Gamelist')->getDataList();
        $data['userlist'] = model('User')->getDataList();
        return view('user/list',$data);
    }
    // 会员列表
    private function page_menber($data){
        $list = model('User')->getDataList();
        $data['userlist'] = $list;
        // 组合交易类型
        $temp = config("trad_type");
        $trad_type = [];
        foreach ($temp as $key=>$val){
            $sub_type = [];
            foreach($val['sub_type'] as $k=>$v){
                $sub_type[$v['type']] = $v;
            }
            $val['sub_type'] = $sub_type;
            $trad_type[$val['type']] = $val;
        }

        $data['trad_type'] = json_encode($trad_type);
        return view('user/menberInfo',$data);
    }
    // 投注记录
    private function page_touzhuLog($data){
        $data['userlist'] = model('User')->getDataList();
        $data['gamelist'] = json_encode(model('Gamelist')->getDataList());
        return view('user/touzhuLog',$data);
    }
    // 投注现金明细
    private function page_price($data){
        $data['trad_type']=json_encode(Config('TRAD_TYPE'));
        $data['userlist'] = model('User')->getDataList();
        $data['gamelist'] = json_encode(model('Gamelist')->getDataList());
        return view('user/priceLog',$data);
    }
    // 业主网站设置
    private function page_webConfig($data){
        $data['userlist'] = model('User')->getDataList();
        $data['gamelist'] = json_encode(model('Gamelist')->getDataList());
        return view('system/webConfig',$data);
    }


    // 综合报表
    private function page_synthe($data){
        $list = model('User')->getDataList();
        $data['list'] = $list;
        return view('static/synthe_table',$data);
    }
    // 角色列表  权限控制
    private function page_role($data){
        $data['menu'] = model('Menu')->getDataList();
        $data['port'] = model('Port')->getDataList();
        return view('role/list',$data);
    }
    // 子账号管理
    private function page_account($data){
        $data['rolelist'] = model('Role')->getDataList();
        $data['role'] = json_encode(model('Role')->getDataInuse());
        return view('admin/account',$data);
    }
    // 个人中心
    private function page_center($data){

        $data['info'] = model('Admin')->getDataById(session('_admin')['id']);

        return view('admin/center',$data);
    }
    // 会员管理
    private function page_menberInfo($data){
        $data['userlist'] = model('User')->getDataList();

        // 组合交易类型
        $temp = config("trad_type");
        $trad_type = [];
        foreach ($temp as $key=>$val){
            $sub_type = [];
            foreach($val['sub_type'] as $k=>$v){
                $sub_type[$v['type']] = $v;
            }
            $val['sub_type'] = $sub_type;
            $trad_type[$val['type']] = $val;
        }

        $data['trad_type'] = json_encode($trad_type);
        return view('user/menberInfo',$data);
    }
    // 期数管理
    private function page_qishuMgr($data){
        $data['userlist'] = model('User')->getDataList();
        $data['gamelist'] = json_encode(model('Gamelist')->getDataList());
        return view('operate/reopenLottery',$data);
    }
    // 彩种管理
    private function page_gamelist($data){
        $data['userlist'] = model('User')->getDataList();
        $data['gamelist'] = json_encode(model('Gamelist')->getDataList());
        return view('operate/gameMgr',$data);
    }

    // 玩法开关
    private function page_playonoff($data){
        $data['userlist'] = model('User')->getDataList();
        $data['gamelist'] = model('Gamelist')->getDataList();
        return view('operate/playOnOff',$data);
    }
    // 遗漏补开
    private function page_reopenLottery($data){
        $data['userlist'] = model('User')->getDataList();
        $data['gamelist'] = json_encode(model('Gamelist')->getDataList());
        return view('operate/reopenLottery',$data);
    }
    // 日志管理
    private function page_loglist($data){
        return view('log/loglist');
    }
    // 登录日志
    private function page_loginLog($data){
        return view('log/login_log');
    }
    // 后台操作日志
    private function page_sportlog($data){
        $data['userlist'] = model('User')->getDataList();
        return view('log/admin_log',$data);
    }

    // 缓存管理
    private function page_cacheMgr($data){
        $data['userlist'] = model('User')->getDataList();
        return view('system/cacheMgr',$data);
    }
    //体彩补开奖
    private function page_spbkj($data){
        $data['userlist'] = model('User')->getDataList();
        return view('sport/spbkj',$data);
    }
    //体彩补开奖列表
    private function page_splist($data){
        $data['userlist'] = model('User')->getDataList();
        return view('sport/list',$data);
    }

}