$.plugins = {};
$(function(){
    // 数字控件
    $('input.number').keyup(function(e){
        var code = e.keyCode;
        if((code >= 48 && code <= 57) || (code >= 96 && code <= 105) || code == 108 || code == 110 || code == 13 || code == 116 || code == 37 || code == 38 || code == 8 || code == 9 || code == 46){
            return true;
        }
        return false;
    })
    // 数字控件
    $('input.number').keydown(function(e){
        var code = e.keyCode;
        if((code >= 48 && code <= 57) || (code >= 96 && code <= 105) || code == 108 || code == 110 || code == 13 || code == 116 || code == 37 || code == 38 || code == 8 || code == 9 || code == 46){
            return true;
        }
        return false;
    })
})
$(".no_select").bind("selectstart",function() {
	return false;
});

// String.format 实现类似C++中format功能, 便于字符串处理
String.prototype.format = function(args){
    var result = this;
    if ( arguments.length > 0) {
        if (arguments.length === 1 && typeof(args) === "object") {
            for (var key in args) {
                var reg = new RegExp("(\\{"+key+"\\})", "g");
                result = result.replace(reg, args[key]);
            }
        }else{
            for(var i = 0; i < arguments.length; i++) {
			var reg = new RegExp("(\\{"+i+"\\})", "g");
                if (arguments[i] == undefined || arguments[i] == null) {
                    result = result.replace(reg, "null"); 
                } else {
                    result = result.replace(reg, arguments[i]); 
                }
            }
        }
    }
    return result;
}

// 小数点保留指定尾数
function _parseFloat(val, em) {
	var str = String(val); if (!em) { em = 0; } var pos = str.indexOf(".");
	if (pos<0) { if (em===0) { return str; } str+="."; for(var i = 0; i<em; i++) { str+="0"; } }else if (pos==0){ return "0."+str.substr(0, 1+em); }else{ return str.substr(0, pos+1+em); } return str;
}

/**
 * 显示指定模板页面
 * @param 模板页面的地址
 * @param 参数列表
 * @private
 */
function _display(model, page, title, options) {
    var def = {
        fullmode : true,    // 是否全屏
        lazy: false,        // 是否启用懒加载
        width: 1000,        // 宽度
        height: 400,        // 高度
    };
    var opt = $.extend(def, options);

    if (opt.fullmode) {
        // 全屏模式, 宽高无效
        var id = "dialog_"+model+"_page_"+page+"_fullmode";
        if ($("#"+id).length > 0) {
            // 该窗口已经存在
            $(".dialog").removeClass("active");
            $("#"+id).addClass("active");
            $("#"+id).fadeIn(200);
            return true;
        }else{
            // 该窗口不存在...重新创建
            var tmp = '<div class="dialog dialog_full_mode active" id="{id}" data-lazy="{lazy}">' +
                '<div class="menu_bar"> {title}' +
                '   <div class="dialog_min_btn"></div> ' +
                '   <div class="dialog_close_btn"></div>' +
                '</div>' +
                '   <iframe src="/{model}/{page}"></iframe>' +
                '</div>';
            $("body").append(tmp.format({id:id,title:title, model: model, page: page, lazy: opt.lazy ? "1" : "0"}));
            bindDialogEvent();
            return true;
        }
    }
}

/**
 * 绑定窗口事件
 */
function bindDialogEvent() {
    // 关闭
    $(".dialog_close_btn").click(function(){
        var _parent = $(this).parents(".dialog");
        if (parseInt(_parent.data('lazy')) === 1) {
            _parent.fadeOut(200, function(){
                _parent.removeClass("active");
            })
        }else{
            _parent.fadeOut(200, function() {
                _parent.remove();
            })
        }
    });

    // 最小化
    $(".dialog_min_btn").click(function(){
        var _parent = $(this).parents(".dialog");
        if (parseInt(_parent.data('lazy')) === 1) {
            _parent.fadeOut(200, function(){
                _parent.removeClass("active");
            })
        }else{
            _parent.fadeOut(200, function() {
                _parent.remove();
            })
        }
    });
}

/**
 * 显示菜单
 * @param list
 * @private
 */
function _showMenu(title, list) {
    console.log(list);
    var menustr = '<div class="mask mask_menulist"> <div class="menu_item"><div class="menu_title"> {title}</div>';
    menustr += '<div class="menu_container">{menu_lists}</div> </div> </div>';
    if(list['submenu'] == undefined || list['submenu'].length == 0){
        return;
    }
    var menulist = '';
    for (var k in list['submenu']) {
        var menu = list['submenu'][k];
        if (menu === undefined) {
            continue;
        }
        menulist += '<div class="menu_btn" data-key="'+k+'">'+menu.appname+'</div>';
    }

    $("body").append(menustr.format({ title: title, menu_lists: menulist}));

    $(".menu_btn").click(function(){
       var key = $(this).data("key");
       var o = list['submenu'][key];
        _display(o.router, 1, o.appname);
    });


    $(".menu_btn").mousedown(function(ev){
        if(ev.button ==2){
            var key = $(this).data("key");
            var o = list['submenu'][key];
                menus.style.position="fixed" ;
                menus.style.background="white";
                menus.innerHTML= '&nbsp;新窗口打开&nbsp;  '
                menus.style.zIndex=999;
                if(ev.pageY >= (innerHeight*3/5)){
                    menus.style.top=ev.pageY-300+"px";
                }else {
                    menus.style.top=ev.pageY+"px";
                }
                menus.style.left=ev.pageX+10+"px";
                menus.style.display="block";
                window.onclick=function (ev) {
                    if(ev.target.id !='menus'){
                        menus.style.display="none";
                    }else{
                        window.location.reload()
                        window.open("./"+o.router)
                    }
                }


        }

    });

    $(".mask_menulist").click(function(){
        _hideMenu();
    })
}

/**
 * 隐藏菜单
 * @private
 */
function _hideMenu() {

    $(".mask_menulist").fadeOut(200, function(){
        $(this).remove();
        $(".menu_btn").unbind("click");
        $(".mask_menulist").unbind("click");
    })
}


/**
 * 显示气泡提示
 */
function toast(content, delay) {
    if (delay === undefined || delay === 0) {
        delay = 3000;
    }
    $("body").append('<div class="toast_lines"><div class="toast_container">'+content+'</div></div>');
    setTimeout(function(){
        $(".toast_lines").fadeOut(200, function(){
            $(this).remove();
        })
    }, delay);
}


/**
 显示加载框
 **/
function showLoading(title) {
    if(title == undefined){
        title = "加载中..."
    }
    $("body").append('<div class="mask showLoading"><div class="loading_img"><div class="animation"><p></p></div><span>'+title+'</span></div></div>');
}

/**
 * 隐藏加载框
 **/
function hideLoading() {
    $(".showLoading").fadeOut(200, function(){
        $(this).remove();
    });
}
// 模仿php empty函数
function empty(obj,key){
    var type = typeof(obj);
    if(type == 'object' || type == 'array'){
        if(key != undefined && key != ''){
            // 有值
            if(obj[key] != undefined && obj[key] != ''){
                return false;
            }
        }else{
            for(var i in obj){
                return false;
            }
        }
    }else{
        if(obj){
            return false;
        }
    }
    return true;
}