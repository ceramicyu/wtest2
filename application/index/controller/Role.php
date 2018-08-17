<?php
namespace app\index\controller;


class Role extends PublicController
{
    public function request($name){
        if(!isAjax()){
            JCode(ILLEGAL,'非法访问');
        }
        $func = "Ajax_".$name;
        if(!method_exists($this,$func)){
            JCode(ERROR,'模块'.$name.'不存在');
        }
        $this->checkRole(API_ACCOUNT,$name,false);
        $this->$func();
    }

    // 列表
    private function Ajax_list(){
        $where = [];
        $list = $this->page('Role',$where);
        JCode(SUCCESS,'msgok',$list);
    }

    // 设置角色状态
    private function Ajax_setStatus(){
        $id = input('id','');
        $status = input('status','');
        if($id == 1){
            JCode(ERROR,'超级管理员不允许修改');
        }
        if(!preg_match('/^[0-9]$/',$status)){
            JCode(ERROR,'非法的status');
        }
        $model = model('Role');
        $role = $model->getDataById($id);
        if(!$role){
            JCode(ERROR,'角色不存在');
        }
        if($role['status'] == $status){
            JCode(ERROR,'status未做更改');
        }
        $data['status'] = $status;
        $res = $model->saveData($data,['id'=>$id]);
        if(!$res){
            JCode(ERROR,'修改失败');
        }

        //日志
        if($status==0){
            $log['content']="设置角色状态  "  ."【角色】：".$role['name']."【状态】：锁定";
        }else{
            $log['content']="设置角色状态  "  ."【角色】：".$role['name']."【状态】：启用";
        }
        addLog('Role','setStatus' , $log['content'] );

        JCode(SUCCESS,'修改成功');
    }

    // 添加(新增角色)
    private function Ajax_add(){
        $data = getDataBy('post_'); //
        $result = $this->validate($data,'Role.add');
        if(true !== $result){
            JCode(ERROR,$result);
        }
        $model = model('Role');
        $res = $model->save($data);
        if(!$res){
            JCode(ERROR,'添加失败');
        }
        $model->setCache();

        $content='添加角色<b>'.input('post_name').'</b> ';
        addLog('Role','add' , $content );
        JCode(SUCCESS,'添加成功');
    }

    // 修改菜单管理
    private function Ajax_editmenu(){
        $id = input('id','');
        $port = input('port','');
        if($id == 1){
            JCode(ERROR,'超级管理员已经拥有所有的权限');
        }

        if(!empty($port)){
            // 检测menu的合法性
            $model = model('Menu');
            $port = $model->checkMenuStr($port);
            if(!$port){
                JCode(ERROR,'菜单数据不合法');
            }
        }else{
            $port = '';
        }
        $data['menulist'] = $port;
        $model = model('Role');
        $res = $model->save(['menulist'=>$port],['id'=>$id]);
        if(!$res){
            JCode(ERROR,'修改失败');
        }
        //日志
        addLog('Role','editmenu' , input() );

        JCode(SUCCESS,'修改成功',$port);
    }

    // 修改接口管理
    private function Ajax_editapi(){
        $id = input('id','');
        $port = input('port','');
        if($id == 1){
            JCode(ERROR,'超级管理员已经拥有所有的权限');
        }

        if(!empty($port)){
            // 检测menu的合法性
            $model = model('Port');
            $port = $model->checkPortStr($port);
            if(!$port){
                JCode(ERROR,'菜单数据不合法');
            }
        }else{
            $port = '';
        }
        $data['portlist'] = $port;
        $model = model('Role');
        $res = $model->save(['portlist'=>$port],['id'=>$id]);
        if(!$res){
            JCode(ERROR,'修改失败');
        }
        JCode(SUCCESS,'修改成功',$port);
    }
    // 删除
    private function Ajax_delete(){
        $id = input('put.id'); // 所有数据集
        $name = input('put.name');
        $model = model('Role');
        $res = $model->where(['id'=>$id])->delete();
        if(!$res){
            JCode(ERROR,'删除失败');
        }
        $model->setCache();
        $content='删除角色<b>'.$name.'</b> ID:'.$id;
        addLog("Role","delete",$content);
        JCode(SUCCESS,'删除成功');
    }
   /*
    * 角色信息编辑
    */
    private function Ajax_edit(){
        $id=input('post_id','');
        $name=trim(input('post_name',''));
        $mark=trim(input('post_mark',''));

        $model = db('role');


        $arr=$model->where(['id'=>$id])->find();
        if($arr['mark'] == $mark && $arr['name'] == $name){
            JCode(ERROR,"未作任何修改");
        }
        if($arr['mark'] != $mark){
            $data['mark']=$mark;
        }
        if($arr['name'] != $name){
            $r=$model->where(['name'=>$id])->find();
            if(!$r){
                $data['name']=$name;
            }else{
                JCode(ERROR,"角色名字占用，修改失败");
            }
        }



        $res = $model->where(['id'=>$id])->update($data);
        if(!$res){
            JCode(ERROR,"修改失败");
        }

        JCode(SUCCESS,'修改成功');
    }
}