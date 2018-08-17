<?php


use Phinx\Migration\AbstractMigration;

class YuboSport extends AbstractMigration
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
        if(!$this->fetchAll("SELECT * FROM `api_menu` WHERE  `id` =7 ;")){
            $this->execute("INSERT INTO `api_menu`(`id`, `pid`, `appname`, `status`, `router`, `flag`, `icon`) VALUES (7, 0, '体育管理', 1, '', 0, 'icon_admin.png');");
            echo  "增加菜单>>>体彩管理";
        }
        if(!$this->fetchAll("SELECT * FROM `api_menu` WHERE  `id` =70001 ;")){
            $this->execute("INSERT INTO  `api_menu`(`id`, `pid`, `appname`, `status`, `router`, `flag`, `icon`) VALUES (70001, 7, '批量补开', 1, 'spbkj', 0, '');");
            echo  "增加子菜单>>>体彩开奖";
        }

    }
    public function down()
    {




    }


}

