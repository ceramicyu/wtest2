<?php


use Phinx\Migration\AbstractMigration;

class YuboApiMenu extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
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
        if(!$this->fetchAll("select * from api_menu where id = 5")){
            $this->execute("insert into api_menu (id,pid,appname,status,router,flag,icon)values (5,0,'日志管理',1,'',1,'icon_admin.png') ");
        }
        if(!$this->fetchAll("select * from api_menu where id = 50001")){
            $this->execute("insert into api_menu (id,pid,appname,status,router,flag)values (50001,5,'后台日志',1,'loglist',1) ");
        }
        if(!$this->fetchAll("select * from api_menu where id = 50002")){
            $this->execute("insert into api_menu (id,pid,appname,status,router,flag)values (50002,5,'登录日志',1,'loginLog',1) ");
        }
    }
    public function down()
    {
        $this->execute("delete from api_menu where id=5 ");
        $this->execute("delete from api_menu where id=50001 ");
        $this->execute("delete from api_menu where id=50002 ");
    }
}
