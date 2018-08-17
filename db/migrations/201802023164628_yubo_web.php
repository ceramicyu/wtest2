<?php


use Phinx\Migration\AbstractMigration;

class YuboWeb extends AbstractMigration
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
     * DROP TABLE IF EXISTS `api_menu`;
    CREATE TABLE `api_menu` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
    `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
    `appname` varchar(20) NOT NULL DEFAULT '',
    `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否使用',
    `router` char(20) NOT NULL DEFAULT '' COMMENT '路由',
    `flag` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0表是菜单 1表示接口',
    `icon` varchar(50) NOT NULL DEFAULT '' COMMENT 'icon',
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=60004 DEFAULT CHARSET=utf8;
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {
        if(!$this->fetchAll("SELECT * FROM `api_port` WHERE  `id` =10007 AND `appname` = '业主网站设置';")){
            $this->execute("INSERT INTO `api_port` (`id`, `pid`, `appname`, `router`) VALUES ('10007', '1', '业主网站设置', 'setting');");
            echo  "增加接口>>>业主网站设置";
        }
        if($this->fetchAll("SELECT * FROM `api_menu` WHERE  `id` =40003 AND `appname` = '自动赔率设置';")){
            $this->execute("DELETE FROM `api_menu` WHERE  `id` =40003 AND `appname` = '自动赔率设置'; ;");
            echo  "删除菜单>>>自动赔率设置";
        }

    }
    public function down()
    {


    }


}

