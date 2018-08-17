-- API列表
DROP TABLE IF EXISTS `api_list`;
CREATE TABLE api_list (
	`api_id` int(11) unsigned not null AUTO_INCREMENT comment 'API 的ID',
	`menuid` int(11) unsigned not null default '0' comment '所属菜单ID',
	`title` varchar(100) not null default 'API名称',
	`keywords` varchar(100) not null default 'API搜索关键词',
	`project_id` int(11) not null default '0' comment '所属项目ID',
	`description` varchar(1024) not null default '' comment 'API 介绍',
	PRIMARY KEY (`api_id`, `menuid`, `project_id`),
	KEY `api_id` (`api_id`),
	KEY `menuid` (`menuid`),
	KEY `project_id` (`project_id`)
) engine=InnoDB charset=utf8;

-- 菜单列表
DROP TABLE IF EXISTS api_menu;
CREATE TABLE api_menu (
	`menuid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL DEFAULT '',
	`project_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`menuid`),
	KEY menuid (`menuid`),
	KEY project_id (`project_id`)
) engine=InnoDB DEFAULT charset=utf8;

-- 项目列表
DROP TABLE IF EXISTS api_project;
CREATE TABLE api_project (
	`pid` int(11) UNSIGNED not null AUTO_INCREMENT COMMENT '项目ID',
	`title` varchar(255) not null DEFAULT '' COMMENT '项目名称',
	PRIMARY KEY (`pid`),
	KEY `pid_id` (`pid`)
) engine=InnoDB DEFAULT charset=utf8;


-- 接口参数列表
DROP TABLE IF EXISTS api_params;
CREATE TABLE api_params (
	`guid` int(11) NOT NULL AUTO_INCREMENT COMMENT '参数ID',
	`api_id` int (11) NOT NULL DEFAULT '0' COMMENT '对应的API ID',
	`param_name` varchar(50) NOT NULL DEFAULT '' COMMENT '参数名称',
	`param_help` varchar(255) NOT NULL DEFAULT '' COMMENT '参数帮助说明',
	`param_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '参数类型,0=文本框,1=文本域,2=选择框',
	`param_def` varchar(255) NOT NULL DEFAULT '' COMMENT '参数默认值,如果是选择框默认值为选择框的索引ID',
	`param_extra` varchar(1024) NOT NULL DEFAULT '' COMMENT '如果是选择框,则为选择框项目文本',
	`can_modify` tinyint(1) NOT NULL DEFAULT '0' COMMENT '该参数是否可改, 0为可改,1为不可改',
	`disable` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否禁用, 0为不禁用, 1为禁用',
	PRIMARY KEY (`guid`),
	KEY guid (`guid`),
	KEY api_guid (`api_id`, `guid`)
) engine=InnoDB DEFAULT charset=utf8;

-- 菜单
TRUNCATE TABLE api_menu;
INSERT INTO `api_menu` (`menuid`, `title`, `project_id`) VALUES('1', '游戏接口', '1');
INSERT INTO `api_menu` (`menuid`, `title`, `project_id`) VALUES('2', '用户接口', '1');
INSERT INTO `api_menu` (`menuid`, `title`, `project_id`) VALUES('3', '代理接口', '1');
INSERT INTO `api_menu` (`menuid`, `title`, `project_id`) VALUES('4', '系统接口', '1');

-- API
TRUNCATE TABLE api_list;
INSERT INTO `api_list` (`api_id`, `menuid`, `title`, `project_id`, `description`, `keywords`) 
VALUES	('1', '1', '获取彩种列表', '1', '通过此接口可以调取当前开放的彩种，或者全部彩种的列表', 'getGameList');
INSERT INTO `api_list` (`api_id`, `menuid`, `title`, `project_id`, `description`, `keywords`) 
VALUES	('2', '2', '用户登陆', '1', '用户登陆接口', 'userLogin');
INSERT INTO `api_list` (`api_id`, `menuid`, `title`, `project_id`, `description`, `keywords`) 
VALUES	('3', '2', '获取试玩账号', '1', '随机生成一个试玩账号返回, 用于试玩账号注册', 'getGuestUsername');
INSERT INTO `api_list` (`api_id`, `menuid`, `title`, `project_id`, `description`, `keywords`) 
VALUES	('4', '2', '注册试玩账号', '1', '注册一个试玩账号', 'guestReg');
INSERT INTO `api_list` (`api_id`, `menuid`, `title`, `project_id`, `description`, `keywords`) 
VALUES	('5', '1', '获取彩票开奖信息', '1', '获取完整的彩票开奖信息，包括前面几期和后面几期。', 'getCPLogInfo');
INSERT INTO `api_list` (`api_id`, `menuid`, `title`, `project_id`, `description`, `keywords`) 
VALUES	('6', '1', '获取系统公告', '1', '获取各种各样的系统消息的接口', 'getSystemNotice');
INSERT INTO `api_list` (`api_id`, `menuid`, `title`, `project_id`, `description`, `keywords`) 
VALUES	('7', '1', '获取系统轮播图', '1', '获取当前系统所启用的所有轮播图片', 'getSystemBanner');
INSERT INTO `api_list` (`api_id`, `menuid`, `title`, `project_id`, `description`, `keywords`) 
VALUES	('8', '1', '获取启动图', '1', '获取当前系统所启用的所有启动图片的信息', 'getApiWelcome');
INSERT INTO `api_list` (`api_id`, `menuid`, `title`, `project_id`, `description`, `keywords`) 
VALUES	('9', '2', '刷新用户金额', '1', '刷新用户金额，重置用户状态，领取中奖金额，判断活动条件等。该接口处理事情比较复杂。当前APP和网页均每5秒刷新一次，耗费服务器资源较大。', 'getTotalPrice');
INSERT INTO `api_list` (`api_id`, `menuid`, `title`, `project_id`, `description`, `keywords`) 
VALUES	('10', '2', '用户登出', '1', '用户登出,登出后将重置SESSION,清除缓存', 'userLogout');
INSERT INTO `api_list` (`api_id`, `menuid`, `title`, `project_id`, `description`, `keywords`) 
VALUES	('11', '2', '用户加密登录', '1', '用户加密登录方式,主要用于APP长时间未登录后重新启动APP重新登陆使用。', 'encodeLogin');







-- 参数
TRUNCATE TABLE api_params;
INSERT INTO `api_params` (`guid`, `api_id`, `param_name`, `param_help`, `param_type`, `param_def`, `can_modify`, `disable`, `param_extra`) 
VALUES (NULL, '1', 'ac', '模块名称,通常不需要改变.', 0, 'getGameList', '1', '0', ''),
(NULL, '1', 'tag', '需要查看的彩种tag, 如果不设置则搜寻全部彩种.', 0, '', '0', '0', ''),
(NULL, '1', 'enable', '是否拉取全部的彩种，或者是只拉取启用的彩种', 2, '', '0', '0', '0=全部彩种|1=启用的彩种');

INSERT INTO `api_params` (`guid`, `api_id`, `param_name`, `param_help`, `param_type`, `param_def`, `can_modify`, `disable`, `param_extra`) 
VALUES  (NULL, '2', 'ac', '模块名称,通常不需要改变.', 0, 'userLogin', '1', '0', ''),
(NULL, '2', 'username', '登陆用户名', 0, '', '0', '0', ''),
(NULL, '2', 'password', '登陆密码', 0, '', '0', '0', ''),
(NULL, '2', 'client_type', '登陆的设备码', 2, '', '0', '0', '0=网页端|1=手机网页|2=安卓端|3=IOS端');

INSERT INTO `api_params` (`guid`, `api_id`, `param_name`, `param_help`, `param_type`, `param_def`, `can_modify`, `disable`, `param_extra`) 
VALUES  (NULL, '3', 'ac', '模块名称,通常不需要改变.', 0, 'getGuestUsername', '1', '0', '');

INSERT INTO `api_params` (`guid`, `api_id`, `param_name`, `param_help`, `param_type`, `param_def`, `can_modify`, `disable`, `param_extra`) 
VALUES  (NULL, '4', 'ac', '模块名称,通常不需要改变.', 0, 'guestReg', '1', '0', ''),
(NULL, '4', 'username', '登陆用户名', 0, '', '0', '0', ''),
(NULL, '4', 'password', '登陆密码', 0, '', '0', '0', ''),
(NULL, '4', 'client_type', '登陆的设备码', 2, '', '0', '0', '0=网页端|1=手机网页|2=安卓端|3=IOS端');

INSERT INTO `api_params` (`guid`, `api_id`, `param_name`, `param_help`, `param_type`, `param_def`, `can_modify`, `disable`, `param_extra`) 
VALUES  (NULL, '5', 'ac', '模块名称,通常不需要改变.', 0, 'getCPLogInfo', '1', '0', ''),
(NULL, '5', 'tag', '需要查看的彩种tag, 如果不设置则搜寻全部。', 0, '', '0', '0', ''),
(NULL, '5', 'date', '搜寻的日期，如: 2017-10-22 则搜索2017-10-22的开奖记录, 为空或者today为搜寻当天的开奖记录包含后面几期的倒计时数据', 0, '', '0', '0', ''),
(NULL, '5', 'pcount', '前面期数的开奖记录数量, 默认为100', 0, '', '0', '0', ''),
(NULL, '5', 'ncount', '后面期数的开奖时间数量, 默认为100', 0, '', '0', '0', ''),
(NULL, '5', 'pageid', '页码,从0开始计数', 0, '', '0', '0', '');

INSERT INTO `api_params` (`guid`, `api_id`, `param_name`, `param_help`, `param_type`, `param_def`, `can_modify`, `disable`, `param_extra`) 
VALUES  (NULL, '6', 'ac', '模块名称,通常不需要改变.', 0, 'getSystemNotice', '1', '0', ''),
(NULL, '6', 'type', '是否拉取全部，还是只拉取正在启用的。', 2, '', '0', '0', '0=已启用|1=全部'),
(NULL, '6', 'count', '拉取数量, 可设置1~20中的任意值,其他数值均视为20条', 0, '', '0', '0', ''),
(NULL, '6', 'ispage', '是否以分页形式返回数据。', 2, '', '0', '0', '0=一般形式|1=分页形式'),
(NULL, '6', 'first', '是否第一次拉取,如果第一次拉取则会返回总数据量。', 0, '', '0', '0', '1=是|0=否'),
(NULL, '6', 'pageid', '页码,从0开始计数', 0, '', '0', '0', '');

INSERT INTO `api_params` (`guid`, `api_id`, `param_name`, `param_help`, `param_type`, `param_def`, `can_modify`, `disable`, `param_extra`) 
VALUES  (NULL, '7', 'ac', '模块名称,通常不需要改变.', 0, 'getSystemBanner', '1', '0', '');

INSERT INTO `api_params` (`guid`, `api_id`, `param_name`, `param_help`, `param_type`, `param_def`, `can_modify`, `disable`, `param_extra`) 
VALUES  (NULL, '8', 'ac', '模块名称,通常不需要改变.', 0, 'getApiWelcome', '1', '0', '');

INSERT INTO `api_params` (`guid`, `api_id`, `param_name`, `param_help`, `param_type`, `param_def`, `can_modify`, `disable`, `param_extra`) 
VALUES  (NULL, '9', 'ac', '模块名称,通常不需要改变.', 0, 'getTotalPrice', '1', '0', ''),
(NULL, '9', 'uid', '用户的UID, 通过登录接口可获得。', 0, '', '1', '0', ''),
(NULL, '9', 'token', '用户的TOKEN, 通过登录接口可获得', 0, '', '1', '0', '');

INSERT INTO `api_params` (`guid`, `api_id`, `param_name`, `param_help`, `param_type`, `param_def`, `can_modify`, `disable`, `param_extra`) 
VALUES  (NULL, '10', 'ac', '模块名称,通常不需要改变.', 0, 'userLogout', '1', '0', '');

INSERT INTO `api_params` (`guid`, `api_id`, `param_name`, `param_help`, `param_type`, `param_def`, `can_modify`, `disable`, `param_extra`) 
VALUES  (NULL, '11', 'ac', '模块名称,通常不需要改变.', 0, 'encodeLogin', '1', '0', ''),
(NULL, '11', 'uid', '用户的UID, 通过登录接口可获得。', 0, '', '1', '0', ''),
(NULL, '11', 'code', '用户的密码加密密文, 加密算法为 "账号:密码" 全部转换为大写后进行SHA1加密.', 0, '', '1', '0', ''),
(NULL, '11', 'edition', 'APP的版本号.', 0, '', '1', '0', ''),
(NULL, '11', 'client_type', '登陆的设备码', 2, '', '0', '0', '0=网页端|1=手机网页|2=安卓端|3=IOS端');
