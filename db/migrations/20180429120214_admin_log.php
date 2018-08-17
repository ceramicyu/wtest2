<?php


use Phinx\Migration\AbstractMigration;

class AdminLog extends AbstractMigration
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
    public function change()
    {
        echo ">>>>新增  业主后台日志  <<<<<\n";
        $this->execute("INSERT INTO `api_menu`(`id`, `pid`, `appname`, `status`, `router`, `flag`, `icon`) VALUES (50003, 5, '业主后台日志', 1, 'adminLog', 1, '');");
        echo ">>>>新增  业主后台日志  <<<<<\n";
    }
}
