<?php


use Phinx\Migration\AbstractMigration;

class YuboApiPort extends AbstractMigration
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
        if(!$this->fetchAll("select * from api_port where id = 4")){
            $this->execute("insert into api_port (id,pid,appname,status,router,flag)values (4,0,'操盘管理',1,'operate',1) ");
        }
        if(!$this->fetchAll("select * from api_port where id = 3")){
            $this->execute("INSERT INTO api_port (id,pid,appname,status,router,flag) VALUES (3,0, '子账号管理',1, 'Admin',1) ");
        }
        if(!$this->fetchAll("select * from api_port where id = 30001")){
            $this->execute("INSERT INTO api_port (id,pid,appname,status,router,flag) VALUES (30001, 3, '设置账号状态',1, 'setStatus',1)");
        }
        if(!$this->fetchAll("select * from api_port where id = 30002")){
            $this->execute("INSERT INTO api_port (id ,pid,appname,status,router,flag) VALUES ('30002', '3', '修改权值',1, 'changeLevel',1)");
        }
        if(!$this->fetchAll("select * from api_port where id = 30003")){
            $this->execute("INSERT INTO api_port (id ,pid,appname,status,router,flag) VALUES ('30003', '3', '添加子账号',1, 'add',1)");
        }
        if(!$this->fetchAll("select * from api_port where id =30004")){
            $this->execute("INSERT INTO api_port (id ,pid,appname,status,router,flag) VALUES ('30004', '3', '删除子账号',1, 'delete',1)");
        }
        if(!$this->fetchAll("select * from api_port where id = 30005")){
            $this->execute("INSERT INTO api_port (id ,pid,appname,status,router,flag) VALUES (30005,3,'重置密码',1,'reSetPass',1)");
        }

    }
    public function down()
    {
        $this->execute("delete from api_port where id=4 ");
        $this->execute("delete from api_port where id=3 ");
        $this->execute("delete from api_port where id=30001 ");
        $this->execute("delete from api_port where id=30002 ");
        $this->execute("delete from api_port where id=30003 ");
        $this->execute("delete from api_port where id=30004 ");
        $this->execute("delete from api_port where id=30005 ");
    }
}
