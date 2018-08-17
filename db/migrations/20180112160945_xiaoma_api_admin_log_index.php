<?php


use Phinx\Migration\AbstractMigration;

class XiaomaApiAdminLogIndex extends AbstractMigration
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
        $res = $this->fetchAll("show index from api_admin_log where key_name = 'title';");
        if(!$res){
            $this->execute("alter table api_admin_log add index title(title);");
        }
        $res = $this->fetchAll("show index from api_admin_log where key_name = 'keyword';");
        if(!$res){
            $this->execute("alter table api_admin_log add index keyword(keywords);");
        }
    }
}
