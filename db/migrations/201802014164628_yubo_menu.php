<?php


use Phinx\Migration\AbstractMigration;

class YuboMenu extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:Game
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        if(!($this->fetchAll("select *from `api_menu` where id = 40005 ;"))){
            $this->execute("INSERT INTO `api_menu` (`id`, `pid`, `appname`, `router`, `flag`) VALUES ('40005', '4', '交易记录', 'price', '1');");
        }
        if(!($this->fetchAll("select *from `api_menu` where id = 30003 ;"))){
            $this->execute("INSERT INTO `api_menu` (`id`, `pid`, `appname`, `router`, `flag`) VALUES ('30003', '3', '个人中心', 'center', '1');");
        }
        if(($this->fetchAll("select *from `api_menu` where id = 40003 and appname = '投注记录';"))){
            $this->execute("UPDATE `api_menu` SET `id`='10003', `pid`='1' WHERE (`id`='40003')");
        }
        if(($this->fetchAll("select *from `api_menu` where id = 40005 and appname = '交易记录';"))){
            $this->execute("UPDATE `api_menu` SET `id`='10004', `pid`='1' WHERE (`id`='40005')");
        }
        if(!($this->fetchAll("select *from `api_menu` where id = 40003 ;"))){
            $this->execute("INSERT INTO `api_menu` (`id`, `pid`, `appname`, `router`, `flag`) VALUES ('40003', '4', '自动赔率设置', 'oddConfig', '1')");
        }
        if(!($this->fetchAll("select *from `api_menu` where id = 10005 ;"))){
            $this->execute("INSERT INTO `api_menu` (`id`, `pid`, `appname`, `router`, `flag`) VALUES ('10005', '1', '业主网站设置', 'webConfig', '1')");
        }

    }
    public function down()
    {
        if($this->fetchAll("select *from `api_menu` where id = 40005 and appname = '交易记录';")){
            $this->execute("DELETE FROM `api_menu` WHERE `id` = 40005");
        }
        if($this->fetchAll("select *from `api_menu` where id = 30003 and appname = '个人中心';")){
            $this->execute("DELETE FROM `api_menu` WHERE `id` = 30003");
        }


    }


}

