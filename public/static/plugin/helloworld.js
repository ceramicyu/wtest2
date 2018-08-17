
/**
 * helloworld插件只为显示hello world没有实际用途
 *
 * 事件:
 *
 * click: 当图标被点击的时候触发
 *
 * timer: 时钟轮询的时候触发
 *
 *
 * @return {[type]} [description]
 */
function HelloWorld() {
    var _self = this;

    // 点击事件
    _self.click = function(){
        _showMenu("业主管理", [
            {title:"业主列表", func : function(){ _display('user',1,'业主列表') }},
            {title:"会员列表", func : function(){ alert("hello world2") }}
        ]);
    }


    // 时钟事件
    _self.timer = function() {

    }
}

// 引入这个插件
$.plugins.helloworld = new HelloWorld();