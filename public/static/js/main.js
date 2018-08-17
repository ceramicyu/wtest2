function autoResize() {
    $(".wrap_main").height($(window).height()-44);
}

$(window).resize(autoResize);
autoResize();
var obj_sheet1 = $(".sheet.sheet_1");
var obj_sheet2 = $(".sheet.sheet_2");
var obj_sheet3 = $(".sheet.sheet_3");

$("body").unbind("mousemove").bind("mousemove", function(e){
    var pos_x = e.clientX;
    var pos_y = e.clientY;
    var mid_width = $(window).width() / 2;
    var mid_height = $(window).height() / 2;
    const offset_x = pos_x - mid_width;
    const offset_y = pos_y - mid_height;
    obj_sheet1.css("background-position", (-offset_x/2)+"px "+(-offset_y/2)+"px");
    obj_sheet2.css("background-position", (-offset_x/3)+"px "+(-offset_y/3)+"px");
    obj_sheet3.css("background-position", (-offset_x/5)+"px "+(-offset_y/5)+"px");
});

var w_plugin_list = [];
var w_timer_list = [];
window.w_init_list = [];
var w_timer = 0;
var cur_index = 0;


// 初始化系统
function initSystem() {
    var app_format = '<div class="application_item" data-id="{id}" data-pname="{pname}"> <div class="app_icon" style="background-image:url(/static/icon/{icon});"></div> <div class="app_name">{appname}</div> </div>';
    var Desktop = $(".wrap_main");
    Desktop.html("");
    var self = this;
    for(var k in plugin_list) {
        if (undefined !== w_plugin_list[parseInt(plugin_list[k].id)]) {
            continue;
        }
        if(plugin_list[k].icon == undefined || plugin_list[k].icon == ''){
            plugin_list[k].icon = 'icon_admin.png';
        }
        if (plugin_list[k].appname !== '' && plugin_list[k].icon !== '') {
            Desktop.append(app_format.format(plugin_list[k]));
        }
        // loadPlugin(plugin_list[k].script);
        w_plugin_list[parseInt(plugin_list[k].id)] = plugin_list[k];
        if (plugin_list[k].timer > 0) {
            w_timer_list.push({func: plugin_list[k].pname, lasttime: 0, delay: plugin_list[k].timer});
        }
    }

    this.getDataById = function(id){
        if(w_plugin_list[id] !== undefined){
            return w_plugin_list[id];
        }
        return null;
    }
    $(".application_item").unbind("click").bind("click", function(){
        var id = $(this).data('id');
        var plugin = self.getDataById(id);
        if (!plugin) {
            console.error("必发总后台: 没有找到插件:"+$(this).data("pname"));
            return;
        }

        _showMenu(plugin.appname, plugin);
    });

    clearInterval(w_timer);
    w_timer = setInterval(function() {
        for(var k in w_timer_list) {
            w_timer_list[k].lasttime+=1000/60;
            if (w_timer_list[k].lasttime > w_timer_list[k].delay) {
                if ($.plugins[w_timer_list[k].func]) {
                    $.plugins[w_timer_list[k].func].timer();
                }
                w_timer_list[k].lasttime -= w_timer_list[k].delay;
            }
        }
    }, 1000/60)
}

// 准备完毕
function initReady() {
    // web socket 通讯模式
    // window.sock = io("sadmin.local.com:5000");
    //
    // // 初始化数据
    // sock.on("initData", function(msg){
    //     var obj = undefined;
    //     try{
    //         obj = eval('('+msg+')');
    //     }catch(e) {
    //         console.error("解析JSON错误",e);
    //         return 0;
    //     }
    //     if (typeof(window.w_init_list[obj.Datatype]) === 'function') {
    //         window.w_init_list[obj.Datatype](obj.Data);
    //     }else{
    //         console.error("无法解析的返回:"+obj.Datatype);
    //     }
    // });

    // Ajax 通讯模式
    for (var k in window.w_init_list) {
        var object = window.w_init_list[k];

    }
}

// 载入新的插件
function loadPlugin(pname) {
    var script = document.createElement("script");
    script.type="text/javascript";

    script.onload = function() {
        cur_index++;
        console.log("load:"+cur_index+"/"+plugin_list.length );
        if (cur_index === plugin_list.length) {
            initReady()
        }
    };
    script.src='/static/plugin/'+pname;
    document.body.appendChild(script);
}

initSystem();