<?php
namespace app\index\controller;
class Operate extends PublicController
{
    // 操盘管理
    public function request($name){
        if(!isAjax()){
            JCode(ILLEGAL,'非法访问');
        }
        $func = "Ajax_".$name;
        if(!method_exists($this,$func)){
            JCode(ERROR,'模块'.$name.'不存在');
        }
        $this->checkRole(API_USER,$name,false);
        $this->$func();
    }

    /*
     * 获取游戏列表
     *
     */
    private function Ajax_list(){
           //TODO:彩种列表
        $this->order=" game_id asc ";
        $tag=input("gametag",'');
        $where['js_tag']  = ['like','%'.$tag.'%'];
        $where['1 or game_name']  = ['like','%'.$tag.'%'];
        if(!$tag){
            $where=[];
        }
        $list = $this->page('Game_list',$where,'*',true);

        JCode(SUCCESS,'msgok',$list);

    }
    /*
       * 获取游戏分类列表
       *
       */
    private function Ajax_typelist(){
        //TODO:彩种列表
        $this->order=" js_tag asc ";
        $list=$this->page("Play_menu",['js_tag'=>'pk10'],"*",true);

        JCode(SUCCESS,'msgok',$list);

    }
    /*
     * 获取彩种列表
     * 玩法管理页面
     */
    private function Ajax_playlist(){
          $tag=input("tag",'');
          $gid=input("gid",'');
          $keywords=trim(input("keywords",''));
          if($keywords){
              $where['1 and wanfa']  = ['like','%'.$keywords.'%'];
//              $where['1 or playname']  = ['like','%'.$keywords.'%'];
          }

          $where['gameid']  = $gid;
          $where['disable']  = 0;

          $result = db('game_list')->where(['game_id'=>$gid])->find();
          $tag=$result['js_tag'];
          $this->order=" playid asc ";
          $list =$this->page("Play_config",$where,"*",true);

        $list['list']= $list['list']->toArray();

        $res2=db('play_menu')->where(['js_tag'=>$tag])->where("parentid = 0 ")->select()->toArray();
        $res=db('play_menu')->where(['js_tag'=>$tag])->where("parentid > 0 ")->select()->toArray();
        foreach ($res as $k=>$v){
                 foreach ($res2 as $k2=>$v2){
                    if($v2["menuid"] == $v['parentid']){

                        $res[$k]['parentname']=$v2["name"];
                    }
                 }
        }
        $arr=[];
        foreach($res as $k=>$v){
            $arr[$v['menuid']]=$v['parentname'];
        }
        foreach($list['list'] as $key=>$value){
                $list['list'][$key]['name']=$arr[$value['menuid']];
        }
        JCode(SUCCESS,'msgok', $list);
    }




}