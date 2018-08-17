<?php


use Phinx\Migration\AbstractMigration;

class YuboAdmin extends AbstractMigration
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
    {   $table = $this->table('api_admin');
       if($table){
           if($table->hasColumn('lock_status')){
               $table->removeColumn('lock_status');
           }
           if($table->hasColumn('lock_ip')){
               $table->removeColumn('lock_ip');
           }
           if($table->hasColumn('lock_time')){
               $table->removeColumn('lock_time');
           }
           $this->execute("ALTER TABLE `api_admin`
                            ADD COLUMN `lock_status`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '登录锁' AFTER `last_ip`;");
           $this->execute("ALTER TABLE `api_admin`
                            ADD COLUMN `lock_ip`  varchar(255) NOT NULL DEFAULT '' COMMENT '登录锁定ip' AFTER `last_ip`;");
           $this->execute("ALTER TABLE `api_admin`
                             ADD COLUMN `lock_time`  int(11) NOT NULL DEFAULT '0' COMMENT '锁定时间' AFTER `lock_ip`;");

       }

    }
    public function down()
    {
        $table = $this->table('api_admin');
        if($table) {
            if ($table->hasColumn('lock_status')) {
                $this->execute("alter table `api_admin` drop column `lock_status`;");
            }
            if ($table->hasColumn('lock_ip')) {
                $this->execute("alter table `api_admin` drop column `lock_ip`;");
            }
            if ($table->hasColumn('lock_time')) {
                $this->execute("alter table `api_admin` drop column `lock_time`;");
            }

        }
    }

}

