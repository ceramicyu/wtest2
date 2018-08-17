/**
 * 通过这里定义一串插件的方式
 * 来给主站植入新功能。
 * 插件包括如下属性:
 *
 * pid: 插件的唯一标识, 必须是数字, 如果跟前面的重复, 就不会添加进系统中
 * icon: 图标的文件名, 图标均放置在icon文件夹中, 图片尺寸建议为100x200
 * appname: 插件的名字, 最好不要超过10个字符
 * script: 脚本的文件名，所有插件脚本均放置在 plugin 文件夹中
 * timer: 是否启用时钟，大于0为启用，且为时钟的延迟(单位:秒)
 */

 // 插件列表
 var plugin_list = [
 	// 业主管理的插件
 	{
 		pid:1,
 		pname: "helloworld",
 		icon: 'icon_admin.png',
 		appname: '业主管理',
 		script: 'helloworld.js',
		timer: 2000,
 	},
 	// {
 	// 	pid: 2,
 	// 	icon: 'icon_admin.png',
 	// 	pname: "heheda",
 	// 	appname: '维护设定',
 	// 	script: 'helloworld.js',
		// timer: 0,
 	// }
 ];