<?php


use Phinx\Migration\AbstractMigration;

class YuboPort extends AbstractMigration
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
        $this->execute("INSERT INTO `api_port` (`id`, `pid`, `appname`, `router`) VALUES ('20006', '2', '修改角色信息', 'edit');");
        $this->execute("INSERT INTO `api_port` (`id`, `pid`, `appname`, `router`) VALUES ('30006', '2', '修改账号信息', 'editAccount');");
    }
    public function down()
    {
        $this->execute("DELETE FROM `api_port` WHERE (`id`='20006')");
        $this->execute("DELETE FROM `api_port` WHERE (`id`='30006')");
    }
}

