<?php


use Phinx\Migration\AbstractMigration;

class XiaomaZd extends AbstractMigration
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
        // 添加创建总代帐号接口
        if(!$this->fetchAll("select * from api_port where id = 1008 and pid = 1")){
            $this->execute("INSERT INTO api_port (id,pid,appname,status,router,flag) VALUES (10008,1, '创建总代',1, 'addUserZd',1) ");
        }
        $table = $this->table("api_admin_log");
        if($table){
            if(!$table->hasColumn("update_time")){
                $this->execute("alter table api_admin_log add column update_time int(11) not null default 0 ;
");
            }
        }
    }
    public function down (){
        $this->execute("delete from api_port where id = 1008 and pid = 1");
    }
}
