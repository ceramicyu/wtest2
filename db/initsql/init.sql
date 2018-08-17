-- MySQL dump 10.13  Distrib 5.7.18, for Linux (x86_64)
--
-- Host: localhost    Database: bxsadmin
-- ------------------------------------------------------
-- Server version	5.7.18-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `api_admin`
--

DROP TABLE IF EXISTS `api_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `username` char(20) NOT NULL DEFAULT '' COMMENT '账号',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `locked` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否锁定',
  `accesstype` int(11) NOT NULL DEFAULT '1' COMMENT '管理员权限',
  `phone` varchar(20) NOT NULL DEFAULT '',
  `login_ip` int(11) NOT NULL DEFAULT '0' COMMENT '当前登录的ip',
  `last_ip` int(11) NOT NULL DEFAULT '0' COMMENT '上次登录的ip',
  `login_time` int(11) NOT NULL DEFAULT '0' COMMENT '登录时间',
  `last_time` int(11) NOT NULL DEFAULT '0' COMMENT '上次登录时间',
  `token` char(32) not null default '' comment '登录的令牌',
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_admin`
--

LOCK TABLES `api_admin` WRITE;
/*!40000 ALTER TABLE `api_admin` DISABLE KEYS */;
INSERT INTO `api_admin` VALUES (1,'qweqwe','1e31165d696b187fa773f1b82f00afab',1970,1970,1,1,'',192168,192168,1513415043,2017);
/*!40000 ALTER TABLE `api_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_list`
--

DROP TABLE IF EXISTS `api_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_list` (
  `api_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'API 的ID',
  `menuid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属菜单ID',
  `title` varchar(100) NOT NULL DEFAULT 'API名称',
  `keywords` varchar(100) NOT NULL DEFAULT 'API搜索关键词',
  `project_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属项目ID',
  `description` varchar(1024) NOT NULL DEFAULT '' COMMENT 'API 介绍',
  PRIMARY KEY (`api_id`,`menuid`,`project_id`),
  KEY `api_id` (`api_id`),
  KEY `menuid` (`menuid`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_list`
--

LOCK TABLES `api_list` WRITE;
/*!40000 ALTER TABLE `api_list` DISABLE KEYS */;
INSERT INTO `api_list` VALUES (1,1,'获取彩种列表','getGameList',1,'通过此接口可以调取当前开放的彩种，或者全部彩种的列表'),(2,2,'用户登陆','userLogin',1,'用户登陆接口'),(3,2,'获取试玩账号','getGuestUsername',1,'随机生成一个试玩账号返回, 用于试玩账号注册'),(4,2,'注册试玩账号','guestReg',1,'注册一个试玩账号'),(5,1,'获取彩票开奖信息','getCPLogInfo',1,'获取完整的彩票开奖信息，包括前面几期和后面几期。'),(6,1,'获取系统公告','getSystemNotice',1,'获取各种各样的系统消息的接口'),(7,1,'获取系统轮播图','getSystemBanner',1,'获取当前系统所启用的所有轮播图片'),(8,1,'获取启动图','getApiWelcome',1,'获取当前系统所启用的所有启动图片的信息'),(9,2,'刷新用户金额','getTotalPrice',1,'刷新用户金额，重置用户状态，领取中奖金额，判断活动条件等。该接口处理事情比较复杂。当前APP和网页均每5秒刷新一次，耗费服务器资源较大。');
/*!40000 ALTER TABLE `api_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `api_menu`
--

DROP TABLE IF EXISTS `api_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_menu` (
  `menuid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`menuid`),
  KEY `menuid` (`menuid`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_menu`
--

LOCK TABLES `api_menu` WRITE;
/*!40000 ALTER TABLE `api_menu` DISABLE KEYS */;
INSERT INTO `api_menu` VALUES (1,'游戏接口',1),(2,'用户接口',1),(3,'代理接口',1),(4,'系统接口',1);
/*!40000 ALTER TABLE `api_menu` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `api_user`
--

DROP TABLE IF EXISTS `api_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `api_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `username` char(20) NOT NULL DEFAULT '' COMMENT '业主名字',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '写入数据表的时间',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `locked` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否维护',
  `admin_url` varchar(200) NOT NULL DEFAULT '' COMMENT '后台接口域名',
  `index_url` varchar(200) NOT NULL DEFAULT '' COMMENT '主域名接口域名',
  `token` char(32) NOT NULL DEFAULT '' COMMENT '接口token',
  `website` char(20) NOT NULL DEFAULT '' COMMENT 'website',
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  `view` varchar(20) NOT NULL DEFAULT 'haocai' COMMENT '使用模板',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `api_user`
--

LOCK TABLES `api_user` WRITE;
/*!40000 ALTER TABLE `api_user` DISABLE KEYS */;
INSERT INTO `api_user` VALUES (1,'七天',1512638465,1513427192,1,'http://myadmin.com','http://myindex.com','bda4c5ec44e0169e1f4f63e42c5a9c08','qitiancai','qweqwe','haocai');
/*!40000 ALTER TABLE `api_user` ENABLE KEYS */;
UNLOCK TABLES;

