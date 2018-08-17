<?php
namespace app\index\controller;

use app\index\model\AdminLog;

class User extends PublicController
{
    public function request($name){
        if(!isAjax()){
            JCode(ILLEGAL,'非法访问');
        }
        $func = "Ajax_".$name;
        if(!method_exists($this,$func)){
            JCode(ERROR,'模块'.$name.'不存在');
        }
        $this->checkRole(API_MENBER,$name,false);
        $this->$func();
    }

    // 业主列表
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
        $list = $this->page('User',$where);


        JCode(SUCCESS,'msgok',$list);
    }

    // 添加
    private function Ajax_add(){
        $data = getDataBy('post_'); //

        $result = $this->validate($data,'User.add');
        if(true !== $result){
            JCode(ERROR,$result);
        }
        $model = model('User');
        $res = $model->save($data);
        if(!$res){
            JCode(ERROR,'添加失败');
        }
        $model->setCache();
        $content='添加新业主<b>'.$data['username'].'</b>';
        addLog("User","add",$content);
        JCode(SUCCESS,'添加成功');
    }

    // 编辑
    private function Ajax_edit(){
        $data = getDataBy('post_');
        if(empty($data['id'])){
            JCode(ERROR,'业主信息错误');
        }
        $model = model('User');
        $res = $model->validate('User.edit')->save($data,['id'=>$data['id']]);
        if(!$res){
            JCode(ERROR,$model->getError());
        }
        $model->setCache();
        $content='修改了<b>'.$data['username'].'</b>的信息';
        addLog("User","edit",$content);
        JCode(SUCCESS,'编辑成功');
    }

    // 删除
    private function Ajax_delete(){
        $id = input('put.id'); // 所有数据集
        $model = model('User');
        $res = $model->where(['id'=>$id])->delete();
        if(!$res){
            JCode(ERROR,'删除失败');
        }
        $model->setCache();
        $content='删除了ID为'.$id.'的业主';
        addLog("User","delete",$content);
        JCode(SUCCESS,'删除成功');
    }

}