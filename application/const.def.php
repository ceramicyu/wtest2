<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/12/2017
 * Time: 4:34 PM
 * 定义常量，  所有的常量都放置在这里
 */
define('DEFAULT_PROJECT_ID', 1); //  这是啥常量 ??
define('SUCCESS',0); // ajax操作成功常量
define('LOGOUT',40000); // 未登录常量
define('ILLEGAL',40001); // 非法访问
define('ERROR',40002); // 非法访问
define('ERROR_VERIFY',40010); // 验证码访问失败
define('NOALLOW',40003); // 不允许访问
define("TTYPE_ALL_PAY",10001);

define("SYSTEM_VERSION", "v1.3.4");
define("TABLE_SPLIT_COUNT", 32);	// 分表数量
define("MAX_PER_PAGE", 100);		// 分页每页数量
define("TTYPE_GS_PAY", 1);			// 公司入款
define("TTYPE_GS_PAY_BANK", 1);		// 公司入款-银行卡入款
define("TTYPE_GS_PAY_WEIXIN", 2);	// 公司入款-微信入款
define("TTYPE_GS_PAY_ALIPAY", 3);	// 公司入款-支付宝入款
define("TTYPE_GS_PAY_QQ", 4);		// 公司入款-QQ入款
define("TTYPE_GS_PAY_HUI", 5);		// 公司入款-转账汇款
define("TTYPE_GS_PAY_SAVE", 6);		// 公司入款-存款优惠
define("TTYPE_GS_PAY_FIRST", 7);	// 公司入款-首存优惠

define("TTYPE_OL_PAY", 2);			// 线上入款
define("TTYPE_OL_PAY_BANK", 1);		// 银联入款
define("TTYPE_OL_PAY_WXQ", 2);		// 微信扫码
define("TTYPE_OL_PAY_WXA", 3);		// 微信APP
define("TTYPE_OL_PAY_ALIQ", 4);		// 支付宝扫码
define("TTYPE_OL_PAY_ALIA", 5);		// 支付宝APP
define("TTYPE_OL_PAY_QUICK", 6);	// 快捷支付
define("TTYPE_OL_PAY_QQ", 7);		// QQ钱包
define("TTYPE_OL_PAY_QQ_WAP", 11);	// QQ钱包app
define("TTYPE_OL_PAY_SAVE", 8);		// 存款优惠
define("TTYPE_OL_PAY_FIRST", 9);	// 首存优惠
define("TTYPE_OL_PAY_MAX", 12);	    // 在线充值的最大值，  这个值可以任意改变

define("TTYPE_HM_PAY", 3);			// 人工入款
define("TTYPE_HM_PAY_HM", 1);		// 人工存入
define("TTYPE_HM_PAY_CUN", 2);		// 存款优惠
define("TTYPE_HM_PAY_CLEAR", 3);	// 负数额度清零
define("TTYPE_HM_PAY_NOTK", 4);		// 取消出款
define("TTYPE_HM_PAY_FP", 5);		// 返点优惠
define("TTYPE_HM_PAY_EVENT", 6);	// 活动优惠
define("TTYPE_HM_PAY_HUI", 7);		// 汇款优惠
define("TTYPE_HM_PAY_FIRST", 8);	// 首存优惠
define("TTYPE_HM_PAY_OTHER", 9);	// 其他
define("TTYPE_HM_TOUZHU_RET", 10);	// 人工撤单

define("TTYPE_EV_ODD", 4);			// 活动优惠
define("TTYPE_EV_ODD_REG", 1);		// 注册活动
define("TTYPE_EV_ODD_PAY", 2);		// 充值活动
define("TTYPE_EV_ODD_TZ", 3);		// 投注活动
define("TTYPE_EV_ODD_TG", 4);		// 推广活动
define("TTYPE_EV_ODD_OTHER", 5);	// 其他活动
define("TTYPE_EV_ODD_SIGN", 6);		// 签到活动
define("TTYPE_EV_ODD_BAG", 7);		// 红包活动
define("TTYPE_EV_ODD_RISE", 8);		// 晋级活动

define("TTYPE_TZ_RET", 5);	// 投注退还
define("TTYPE_TZ_RET_TZ", 1);	// 投注退还

define("TTYPE_TK_RET", 6);	// 提款退还
define("TTYPE_TK_RET_TK", 1);	// 提款退还

define("TTYPE_DL_GAN", 7);	// 代理返点
define("TTYPE_DL_GAN_DL", 1);	// 代理返点

define("TTYPE_TZ_WIN", 9);	// 中奖派送
define("TTYPE_TZ_WIN_WIN", 1);	// 代理返点

define("TTYPE_TK_OK", 50);	// 提款
define("TTYPE_TK_OK_OL", 1);	// 线上提出
define("TTYPE_TK_OK_HM", 2);	// 人工提出
define("TTYPE_TK_OK_ERR", 3);	// 误存提出
define("TTYPE_TK_OK_FS", 4);	// 现金转账
define("TTYPE_TK_OK_DOUBlE", 5);	// 重复出款
define("TTYPE_TK_OK_DIG", 6);		// 会员负数回冲
define("TTYPE_TK_OK_SERVICE", 7);	// 手动申请出款
define("TTYPE_TK_OK_KC", 8);		// 扣除非法下注派彩
define("TTYPE_TK_OK_LOSEHUI", 9);	// 放弃优惠
define("TTYPE_TK_OK_SYS", 10);	// 误存提出
define("TTYPE_TK_OK_OTHER", 11);	// 其他


define("TTYPE_TZ_OK", 51);	// 投注
define("TTYPE_TZ_OK_1", 1);	// 投注

define("TTYPE_MONEY_TRAD", 52);	// 现金交易
define("TTYPE_MONEY_TRAD_GET", 1); // 代理转入
define("TTYPE_MONEY_TRAD_PAY", 2); // 代理转出

define("EXAMINE_TYPE_NORMAL", 0);	// 常态性稽核
define("EXAMINE_TYPE_TOTAL", 1);	// 综合性稽核
define("EXAMINE_TYPE_NONE", 2);	// 不计入稽核
// 冻结
define("LOCKED_LOGIN", 1);		// 冻结登陆
define("LOCKED_TK", 2);			// 冻结提款
define("LOCKED_TZ", 4);			// 冻结投注
define("LOCKED_PASS", 8);		// 冻结修改密码
define("LOCKED_REALNAME", 16);	// 冻结修改真实姓名
define("LOCKED_TUIGUANG", 32);	// 冻结推广连接

define("SHENGXIAO_SHU", 0);		// 生肖-鼠
define("SHENGXIAO_NIU", 1);		// 生肖-牛
define("SHENGXIAO_HU", 2);		// 生肖-虎
define("SHENGXIAO_TU", 3);		// 生肖-兔
define("SHENGXIAO_LONG", 4);		// 生肖-龙
define("SHENGXIAO_SHE", 5);		// 生肖-蛇
define("SHENGXIAO_MA", 6);		// 生肖-马
define("SHENGXIAO_YANG", 7);		// 生肖-羊
define("SHENGXIAO_HOU", 8);		// 生肖-猴
define("SHENGXIAO_JI", 9);		// 生肖-鸡
define("SHENGXIAO_GOU", 10);	// 生肖-狗
define("SHENGXIAO_ZHU", 11);	// 生肖-猪

define("ERR_NETWORK_DOWN", "执行错误!请重试<p style='color:#F33'>如果此问题依然存在,请联系技术人员进行排查!</p>");

// 接口类型定义 --- CP后台
define("PORT_RANK_LOTTERY", 1001);		// 彩种收益排行
define("PORT_RANK_USERWIN", 1002);		// 用户盈利排行
define("PORT_RANK_USERLOSE", 1003);		// 用户亏损排行
define("PORT_USER_SETINFO", 2001);		// 资料设置
define("PORT_USER_SETNAME", 2002);		// 实名设置
define("PORT_USER_SETTYPE", 2003);		// 修改层级
define("PORT_USER_LOCKLOG", 2004);		// 冻结操作
define("PORT_USER_LOCKUSER", 2005);		// 停权操作
define("PORT_USER_TRADLOG", 2006);		// 现金明细
define("PORT_USER_RESETPASS", 2007);	// 重置密码
define("PORT_USER_VIEWLOG", 2008);		// 查看日志
define("PORT_USER_SENDMSG", 2009);		// 发送私信
define("PORT_USER_TZLOG", 2010);		// 投注记录
define("PORT_USER_VRENAME", 2011);		// 审核改名
define("PORT_USER_BANKLIST", 2012);		// 银行卡管理

define("PORT_STATIC_NEWUSER", 4001);	// 新增会员
define("PORT_STATIC_NEWAGENT", 4002);	// 新增代理
define("PORT_STATIC_ALLUSER", 4003);	// 会员总数
define("PORT_STATIC_ALLAGENT", 4004);	// 代理总数
define("PORT_STATIC_TOUZHU", 4005);		// 今日投注
define("PORT_STATIC_RTOUZHU", 4006);	// 今日撤单
define("PORT_STATIC_TZUSEFULL", 4007);	// 有效投注
define("PORT_STATIC_WINSUM", 4008);		// 中奖总额
define("PORT_STATIC_PAYSUM", 4009);		// 今日总充值
define("PORT_STATIC_GETSUM", 4010);		// 今日总提款
define("PORT_STATIC_EVENT", 4011);		// 活动礼金
define("PORT_STATIC_FANDIAN", 4012);	// 代理返点
define("PORT_STATIC_WIN_TODAY", 4013);	// 今日盈利
define("PORT_STATIC_WIN_YESTER", 4014);	// 昨日盈利
define("PORT_STATIC_WIN_MONTH", 4015);	// 本月盈利
define("PORT_STATIC_WIN_LMONTH", 4016);	// 上月盈利

// 上传类型定义
define("UPLOAD_TYPE_BANNER", 0);
define("UPLOAD_TYPE_EVENT", 1);
define("UPLOAD_TYPE_CACHE", 2);
define("UPLOAD_TYPE_QRCODE", 3);
define("UPLOAD_TYPE_USER", 4);
define("UPLOAD_TYPE_NOTICE", 5);
define("UPLOAD_TYPE_CPICON", 6);
define("UPLOAD_TYPE_LOGO", 7);
define("UPLOAD_TYPE_PACK", 8);


// 冻结操作
define("LOCKTYPE_LOGIN", 0);	// 冻结登录
define("LOCKTYPE_GET", 1);		// 冻结提款
define("LOCKTYPE_TOUZHU", 2);	// 冻结投注
define("LOCKTYPE_UPPASS", 3);	// 冻结修改密码
define("LOCKTYPE_UPNAME", 4);	// 冻结修改真实姓名
define("LOCKTYPE_TGLINK", 5);	// 冻结推广链接
define("LOCKTYPE_TRAD", 6);		// 冻结现金交易

// graylog常量定义
define("GRAYLOG_INFO", "info");
define("GRAYLOG_WARNING", "warning");
define("GRAYLOG_ERROR", "error");
define("GRAYLOG_DEBUG", "debug");

//手动采集配置设置
define("CAIJI_URL", "http://a.apiplus.net");
define("TOKEN", "43c15ad9bc726ed7");
define("AUTOKJ_TOKEN", "a8d4c32573a053346e8c185e1d498a3207c06552");

// 手机短信验证码类型
define('PHONE_TYPE_LOGIN',1); // 登录验证码
define('PHONE_TYPE_RESET',2); // 重置密码,  忘记密码等
define('PHONE_TYPE_BIND',3);  // 绑定验证码
define('PHONE_TYPE_UNBIND',4);// 解绑验证码
define('PHONE_TYPE_OPEN',5);// 开启手机验证
define('PHONE_TYPE_CLOSE',6);// 关闭手机验证

include('const.def.api.php'); // 接口常量类型

