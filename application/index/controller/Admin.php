<?php
namespace app\index\controller;


class Admin extends PublicController
{
    public function request($name){
        if(!isAjax()){
            JCode(ILLEGAL,'非法访问');
        }
        $func = "Ajax_".$name;
        if(!method_exists($this,$func)){
            JCode(ERROR,'模块'.$name.'不存在');
        }
        $this->checkRole(3,$name,false);
        $this->$func();
    }

    // 权值检测
    private function checkLevel($level){
        if(!preg_match('/^[1-9][0-9]?$/',$level)){
            JCode(ERROR,'请输入合法的权值: 1-99');
        }
        if($this->admin['id'] != 1 || $this->admin['accesstype'] != 1){
            // 过滤超管
            $this_level = $this->admin['level'];
            if($this_level >= $level){
                JCode(ERROR,'只允许更改比自己权值大的账号');
            }
        }
    }

    // 列表
    private function Ajax_list(){
        $url = input('url','');
        $username = input('username','');
        $where = [];
        if($url){
            $where['url'] = $url;
        }
        if($username){
            $where['username'] = $username;
        }
        $list = $this->page('Admin',$where);
        JCode(SUCCESS,'msgok',$list);
    }

    // 设置账号状态
    private function Ajax_setStatus(){
        $id = input('id','');
        $status = input('status','');
        if($id == 1){
            JCode(ERROR,'总账号不允许修改');
        }
        if(!preg_match('/^[0-9]$/',$status)){
            JCode(ERROR,'非法的status');
        }
        $model = model('Admin');
        $role = $model->getDataById($id);
        if(!$role){
            JCode(ERROR,'账号不存在');
        }

        $this->checkLevel($role['level']);


        if($role['locked'] == $status && !($role['lock_status'] > 3)){
            JCode(ERROR,'status未做更改');
        }
        $data['locked'] = $status;
        $res = $model->saveData($data,['id'=>$id]);
        if(!$res){
            JCode(ERROR,'修改失败');
        }

        //日志

      if($status==0){
          $log['content']="设置账号<b style='color: red;'>".$role['username']."</b>状态<b>锁定</b>";
      }else{
          if($role['lock_status'] >3){
              $res = $model->saveData(['lock_status'=>0],['id'=>$id]);
          }
          $log['content']="设置账号<b  style='color: red;'>".$role['username']."</b>状态<b>解锁</b>";
      }

        addLog('Admin','setStatus' , $log['content']);

        JCode(SUCCESS,'修改成功');
    }

    // 更改权值  权值小的可以更改大的  权值大的不允许更改小的   超级管理员 无视
    private function Ajax_changeLevel(){
        $id = input('id','');
        $level = input('level','');
        $model = model('Admin');
        $user = $model->getDataById($id);
        if(!$user){
            JCode(ERROR,'账号不存在');
        }
        if($user['id'] == 1 || $user['accesstype'] == 1){
            JCode(ERROR,'超管账号不允许更改');
        }
        $this->checkLevel($level);
        $data = [];
        $data['level'] = $level;
        $res = $model->saveData($data,['id'=>$id]);
        if($res < 0){
            JCode(ERROR,'权值更改失败');
        }
        $content="账号ID：".$id."   权值：".$level;
        addLog('Admin','changeLevel' , $content);
        JCode(SUCCESS,'修改成功');
    }

    // 添加
    private function Ajax_add(){
        $data = getDataBy('post_'); //

        $result = $this->validate($data,'Admin.add');
        if(true !== $result){
            JCode(ERROR,$result);
        }
        $model = model('Admin');
        $data['password'] = getUserPass($data['username'],$data['password']);
        if(empty($data['level'])){
            $data['level'] = 99;
        }
        $res = $model->save($data);
        if(!$res){
            JCode(ERROR,'添加失败');
        }

        //日志

        $logdata=model("Role")->where('id','=',$data['accesstype'])->find();
        $log['content']="【账号】：".$data['username']."【角色】：".$logdata['name']."【权值】：".$data['level'];
        $log['keywords']='role/add';
        addLog('admin','add',  $log['content']);

        JCode(SUCCESS,'添加成功');
    }

    // 编辑
    private function Ajax_edit(){
        $data = getDataBy('post_');
        if(empty($data['id'])){
            JCode(ERROR,'业主信息错误');
        }
        $model = model('Admin');
        $user = $model->getDataById($data['id']);
        if(!$user){
            JCode(ERROR,'账号不存在');
        }
        $this->checkLevel($user['level']);
        if(empty($data['level'])){
            $data['level'] = 99;
        }
        $res = $model->validate('Admin.edit')->save($data,['id'=>$data['id']]);
        if(!$res){
            JCode(ERROR,$model->getError());
        }
        JCode(SUCCESS,'编辑成功');
    }

    // 删除
    private function Ajax_delete(){
       $id = input('put.id'); // 所有数据集
        $name = input('put.name');
        $model = model('Admin');
        $user = $model->getDataById($id);
        if(!$user){
            JCode(ERROR,'账号不存在');
        }
        $this->checkLevel($user['level']);
        $res = $model->where(['id'=>$id])->delete();
        if(!$res){
            JCode(ERROR,'删除失败');
        }
        //日志

        $log['content']="删除账号  "  ."【账号】：".$name;
        addLog('admin','delete', $log['content']);
        JCode(SUCCESS,'删除成功');
    }

    // 重置密码
    private function Ajax_resetPass(){
        $id = input('ac.id','');
        $password = input('post_password','');
        $username = input('post_username','');
        $model = model('Admin');
        if(!checkUser($password)){
            JCode(ERROR,'密码不符合规范');
        }
        if(!checkId($id)){
            JCode(ERROR,'账号ID错误');
        }
        if($id == $this->admin['id']){
            // 修改自己的
            $data['password'] = getUserPass($this->admin['username'],$password);
            $data['token'] = ''; // 修改token 会导致管理员掉线
            $res = $model->saveData($data,['id'=>$id]);
        }else{
            $user = $model->getDataById($id);
            if(!$user){
                JCode(ERROR,'账号不存在');
            }
            // 修改别人
            $this->checkLevel($user['level']);
            $data['password'] = getUserPass($user['username'],$password);
            $data['token'] = ''; // 修改token 会导致管理员掉线
            $res = $model->saveData($data,['id'=>$id]);
        }
        if(!$res){
            JCode(ERROR,'修改失败');
        }
        //日志
        $log['content']=($this->admin)['username'].'重置了'.$username.'密码';
        addLog('admin','resetPass', $log['content']);
        JCode(SUCCESS,'修改成功');
    }
  private  function Ajax_editAccount(){
        $id=input('post_id','');
        $rolename=input('post_rolename','');
        $username=input('post_username','');
        $newrole=input('post_newrole','');
        $newname=trim(input('post_newname',''));

        $model = db('admin');
        if($newrole != '' && is_numeric($newrole) ){
            $data['accesstype']=$newrole;
        }
        if($newname != '' &&  preg_match('/^[a-z0-9]{4,20}$/i', $newname)){
           $res= $model->where(['username'=>$newname])->find();
           if(!$res){
               $data['username']=$newname;
           } else{
               JCode(ERROR,"账号名子已被占用");
           }
        }elseif($newname == ''){

        }else{
            JCode(ERROR,"账号名子不合法");
        }
        if($id){
            $where['id']=$id;
        }
        if($username){
            $where['username']=$username;
        }

      $res = $model->where($where)->update($data);
        if(!$res){
            JCode(ERROR,"修改失败");
        }
      JCode(SUCCESS,'修改成功',$data);
  }
    /*
     * 个人中心修改密码
     */
    private function Ajax_setPass(){
        $id =session('_admin')['id'];
        $oldpass = input('post_oldpass','');
        $password = input('post_newpass','');
        $model = model('Admin');
        if(!checkUser($password)){
            JCode(ERROR,'密码不符合规范');
        }
        if(!checkId($id)){
            JCode(ERROR,'账号ID错误');
        }

        //验证旧密码

        $pass = getUserPass(session('_admin')['username'],$oldpass);
        $where=["id"=>$id];
        $user = $model->where($where)->find();
        if($user['password'] != $pass){
            JCode(ERROR,'旧密码错误！');
        }

            // 修改自己的
            $data['password'] = getUserPass($this->admin['username'],$password);
//            $data['token'] = ''; // 修改token 会导致管理员掉线
            $res = $model->saveData($data,['id'=>$id]);

        if(!$res){
            JCode(ERROR,'修改失败');
        }
        //日志
        $log['content']=($this->admin)['username'].'修改了密码';
        addLog('admin','setPass', $log['content']);
        JCode(SUCCESS,'修改成功');
    }

}