/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// if (Raven) {
// 	Raven.config('https://59993f72f0204176a49dbaf83190d5da@sentry.io/211913').install();
// }

// 实现一些通用的JS脚本
// 需要Jquery库的支持

$(".no_select").bind("selectstart",function() {
	return false;
});

// String.format 实现类似C++中format功能, 便于字符串处理
String.prototype.format = function(args){
    var result = this;
    if ( arguments.length > 0) {
        if (arguments.length == 1 && typeof(args) == "object") {
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
	if (pos<0) { if (em==0) { return str; } str+="."; for(var i = 0; i<em; i++) { str+="0"; } }else if (pos==0){ return "0."+str.substr(0, 1+em); }else{ return str.substr(0, pos+1+em); } return str;
}

// HTML字符解码
String.prototype.html_decode = function () {
	var result = this;
	// reg = 
	result = result.replace(/&gt;/g,">");
	result = result.replace(/&lt;/g,"<");
	result = result.replace(/&quot;/g,'"');
	result = result.replace(/&amp;/g, "&");
	return result.replace(/&#039;/g, "'");
}

// 根据DOM类名自动填充DOM结构
// @domKey: DOM容器的ID或者CLASS
// @prex: 需要填充的类型前缀
// @obj: 用来填充的结构体
function outPutDOM(dom, obj, pre) {
	if (!pre) {
		pre='';
	}
	var _mode = 0;
	var len = pre.length;
	if (pre[0] == ".") {
		_mode = 0;
	}else if (pre[0] == '#') {
		_mode = 1;
	}else {
		_mode = 2;		// post模式
	}
	for(var key in obj) {
		var val = obj[key];
		if (_mode < 2) {
			var _d = dom.find(pre+key);
			if (!_d) {
				continue;
			}
			outOutDomTo(_d, val);
		}else{
			// post模式
			var _d2 = dom.find("*");
			_d2.each(function(){
				var _post = $(this).data("post");
				if (_post == pre+key) {
					outOutDomTo($(this), val);
				} 
			});
		}
	}
}

function outOutDomTo(_d, val) {
	switch(_d.data("type")) {
		case "money":
		val = _parseFloat(val, 2);
		break;
		case "number":
		val = getGoldFormat(val);
		break;
		case "date2":
		val = getLastTime(val);
		break;
	}
	if (_d.is("input")) {
		if(_d.hasClass("ex_number_input")) { // 数值范围控件
			var _va = val.split(",");
			_d.val(_va[0] ? _parseFloat(_va[0],1) : 0);
			_d.data("max", _parseFloat(_va[1],1) ? _va[1] : 0);
			_d.data("min", _parseFloat(_va[2],1) ? _va[2] : 0);
			_d.update();
		}else{
			_d.val(val);
		}
	} else if(_d.is("select") || _d.hasClass("ex_selector")) {
		_d.val(parseInt(val));
	} else {
		// 复选框
		if (_d.find(".ex_checkbox").length>0) {
			_d.find(".ex_checkbox").removeClass("checked");
			if (typeof(val) == "object") {
				// 是数组,直接遍历
				_d.find(".ex_checkbox").each(function(){
					if (contains(val, $(this).data("value"))) {
						$(this).addClass("checked");
					}
				});
			} else if (typeof(val) == 'string') {
				// 是字符串。分割成数组
				val = val.split(/[\,\.\|\;\s]/);
				_d.find(".ex_checkbox").each(function(){
					if (contains(val, $(this).data("value"))) {
						$(this).addClass("checked");
					}
				});
			}
			if (!_d.find(".ex_checkbox").eq(0).hasClass("checked") && _d.find(".ex_checkbox").length-1 == _d.find(".ex_checkbox.checked").length) {
				if (_d.find(".ex_checkbox").eq(0).data("value")=="-1") {
					_d.find(".ex_checkbox").eq(0).addClass("checked");
				}
			}
		} else if (_d.find(".ex_radiobox").length>0) {
			// 单选框
			_d.find(".ex_radiobox").each(function(){
				if($(this).data("value") == val) {
					$(this).addClass('checked');
				}else{
					$(this).removeClass("checked");
				}
			});
		} else if(_d.is("img")){
			_d.attr("src", _d.data("link")+val);
			_d.data("value", val);
		} else if (_d.hasClass("ex_slider_bar")){
			var _arr = val.split(",");
			_d.data("value", _arr[0]);
			if (!_arr[1]) {
				_arr[1] = _arr[0];
			}
			_d.data("max", _arr[1]);
			if (!_arr[2]) {
				_arr[2] = 0;
			}
			// 移动滑块
			var _x = (parseFloat(_arr[0]) / (parseFloat(_arr[1]) - parseFloat(_arr[2]))) * (_d.innerWidth()-_d.find(".ex_slider_trigger").width());
			_d.find(".ex_slider_trigger").css("left", _x);
			_d.find(".ex_slider_bg").css("width", _x+10);
			// 标签设置
			var _text = _d.data("format");
			$(_d.data("target")).text(_text?_text.format({v:_parseFloat(_arr[0],2)}):_parseFloat(_arr[0],2));
		} else {
			_d.html(val);
		}
	}
}


// 校验该字符串是否是正确的邮箱, 正确返回true, 错误返回false
String.prototype.checkEmail = function() {
    return /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(this);
}

// 校验该字符串是否是正确的手机号码, 正确返回true, 错误返回false;
String.prototype.checkMobile = function() {
    return /^(13|15|17|18)[0-9]{9}$/.test(this);
}

// 校验该字符串是否是正确的IP地址, 正确返回true, 错误返回false
String.prototype.checkIP = function() {
    return /^([0-9]{1,3}\.){3}[0-9]{1,3}$/.test(this);
}

// 校验该字符是否是正确的试玩账号格式
String.prototype.checkGuestUser = function () {
    return /^Guest[0-9]{3,10}$/i.test(this);
}

// 校验该字符是否是正确的常规密码格式
String.prototype.checkUsername = function () {
    return /^[0-9A-Z]{4,20}$/i.test(this);
}

String.prototype.checkVerify = function() {
	return /^[0-9]{4}$/.test(this);
}

String.prototype.checkURL = function() {
	return /^([hH][tT]{2}[pP]:\/\/|[hH][tT]{2}[pP][sS]:\/\/)/.test(this);
}

// ajax函数封装
function AjaxPost(_url,params,sucfunc,errfunc,async){
    showLoading();
    if (_url == undefined) {
        hideLoading();
        IndexController('请提交ajax要访问的路由');return;
    }
    var _param = {};
    if (params != undefined) {
        // 参数处理
        if (typeof(params) == 'object') {
            for(var i in params){
                _param[i] = params[i];
            }
        }
    }
    _param['_method'] = 'PUT';
    // 成功处理函数
    var funcok = function(obj){
        hideLoading();
        if (typeof(obj) != 'object') {
            return;
        }
        if (obj.msg === undefined || obj.msg === '') {
            return;
        }

        // if (obj.msg > 0) {
        //     toast(obj.param?obj.param:"数据错误");
        //     return;
        // }

        if (sucfunc != undefined && typeof(sucfunc) == 'function') {
            sucfunc(obj);
        }
    }

    // 错误函数处理
    if (errfunc == undefined || errfunc == '') {
        var errfunc = function(){
            hideLoading();toast("服务器繁忙");return;
        }
    }else{
        if (typeof(errfunc) != 'function') {
            hideLoading();
            toast('第五个参数只能函数');return;
        }
    }
	var __async = false;
    // 设置异步或则同步
    if (async == undefined || async == '') {
        __async = false; // 异步提交
    }else{
        __async = true; // 同步提交
    }
    $.ajax({
        url:_url,
        type:"post",
        data:_param,
        dataType:'json',
        // async:__async, // 暂时去除这个需求
        success:function(obj){
            if(obj.msg === 40000){
                hideLoading();
                // 未登录
                window.top.location.reload();return;
            }
            funcok(obj);
        },error:function(){
            errfunc();
        }
    })
}
// 简化$.ajax操作的函数
function JsonPost(posturl, postdata, funsuccess, funerror) {
	var errfunc = funerror;
	if (!errfunc) {
		errfunc = function(err, status) {
			if (window.hideLoading) {
				hideLoading();
			}
			funsuccess(err.responseText, err, status);
		};
	}

	var funok = function(obj){
		funsuccess(obj, undefined, undefined);
	}

    $.ajax({
       url : posturl,
       data: postdata,
       dataType: "json",
       type: "post",
       success: funok,
       error: errfunc
    });
}

// 获取随机数
function randInt(min, max, letter) {
	
	if (!letter) {
		letter = 0;
	}
	
	if (min>max) {
		var temp = min;
		min = max;
		max = temp;
	}else if (min == max) {
		return min;
	}
	
	var res = Math.floor(Math.random()*(max+1-min))+min;
	for (var i =0; i<(letter-res.toString().length); i++) {
		res = '0'+res;
	}
	return res;
}

// 获取当前时间戳
function getTimestamp() {
	return Math.floor((new Date()).getTime()/1000);
}

// 时间戳获取时间
function getDateByTime(sec) {
	var day = Math.floor(sec / 86400);
	var hour = Math.floor((sec%86400)/3600);
	var min = Math.floor((sec%3600)/60);
	var sec = sec%60;
	if (day > 0) {
		hour += day*24;
	}
	return {d:day, h:getNumberByZero(hour,2), m:getNumberByZero(min,2), s:getNumberByZero(sec,2)};
}

// 时间戳获取时间格式
function getCDTimerObj(sec) {
	var hour = Math.floor(sec/3600);
	var min = Math.floor((sec%3600)/60);
	var sec = sec%60;
	return {h1:Math.floor(hour/10), h2:hour%10 , m1:Math.floor(min/10), m2: min%10, s1:Math.floor(sec/10), s2: sec%10};
}

// 返回最少几个位的数字
function getNumberByZero(number, offset) {
	if (number.toString().length > offset) {
		return number;
	}else{
		var result = number;
		for (var i = number.toString().length; i < offset; i++) {
			result = "0"+result;
		}
		return result;
	}
}

//JS获取地址栏参数 地址栏 ?id=20
function GetQueryString(name){
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg);
     if(r!=null)return  unescape(r[2]); return null;
}

// 取上次时间
function getLastTime(sec) {
	if (sec > 86400*365) {
		return "一年前";
	}
	if (sec > 86400 * 180) {
		return "半年前";
	}
	if (sec > 86400 * 30) {
		return Math.floor(sec/86400/30)+"月前";
	}
	if (sec > 86400) {
		return Math.floor(sec/86400)+"天前";
	}
	if (sec > 3600) {
		return Math.floor(sec/3600)+"时前";
	}
	if (sec > 60) {
		return Math.floor(sec/60)+"分前";
	}
	return "1分钟内";
}

// 以千位逗号分割的数字
function getGoldFormat(price) {
	if (price<1000) {
		return price;
	}
	var price_str = String(price);
	var result = '';
	var idx = 0;
	for(var i = price_str.length-1; i >= 0; i--) {
		if (idx==3) {
			result = ","+result;
			idx = 0;
		}
		result = price_str[i]+result;
		idx++;
	}
	return result;
}

// 时间戳转时间格式
Date.prototype.format = function(format) {
   var date = {
          "M+": this.getMonth() + 1,
          "d+": this.getDate(),
          "h+": this.getHours(),
          "m+": this.getMinutes(),
          "s+": this.getSeconds(),
          "q+": Math.floor((this.getMonth() + 3) / 3),
          "S+": this.getMilliseconds()
		       };
   if (/(y+)/i.test(format)) {
          format = format.replace(RegExp.$1, (this.getFullYear() + '').substr(4 - RegExp.$1.length));
   }
   for (var k in date) {
          if (new RegExp("(" + k + ")").test(format)) {
                 format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? date[k] : ("00" + date[k]).substr(("" + date[k]).length));
          }
   }
   return format;
}

// 模仿PHP的date函数
Date.prototype._format = function(str, timestamp) {
	if (typeof(str) != 'string') {
		return '';
	}

	if (!timestamp) {
		timestamp = this.getTime();
	}else{
		timestamp = timestamp*1000;
	}
	this.setTime(timestamp);
	var week = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
	var date = {
		y: this.getFullYear(),
		m: (this.getMonth()+1)<10?('0'+(this.getMonth()+1)):(this.getMonth()+1),
		d: this.getDate()<10?('0'+this.getDate()):this.getDate(),
		h: this.getHours()<10?('0'+this.getHours()):this.getHours(),
		i: this.getMinutes()<10?('0'+this.getMinutes()):this.getMinutes(),
		s: this.getSeconds()<10?('0'+this.getSeconds()):this.getSeconds(),
		w: week[this.getDay()]
	};
	return str.format(date);
}

function setCookie(c_name,value,expiredays) {
	if (!expiredays) {
		expiredays = 1;
	}
	var exdate=new Date()
	exdate.setDate(exdate.getDate()+expiredays)
	document.cookie=c_name+ "=" +escape(value)+
	((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
}

function getCookie(c_name){
	if (document.cookie.length>0) {
	  	c_start=document.cookie.indexOf(c_name + "=")
	  	if (c_start != -1) { 
		    c_start = c_start + c_name.length+1;
		    c_end = document.cookie.indexOf(";",c_start);
		    if (c_end==-1) {
		    	c_end = document.cookie.length;
		    }
		    return unescape(document.cookie.substring(c_start,c_end));
	    }
	}
	return "";
}

//判断一个是否在数组中
function contains(arr, obj){
	for(var k in arr) {
		if (arr[k]==obj){
			return true;
		}
	}
	return false;
}

//去除数组中的重复值
function repeatArr(arr){
	var res = [];
	var jsons = {};
	for(var i = 0; i < arr.length; i++){
		if (!jsons[arr[i]]) {
			res.push(arr[i]);
			jsons[arr[i]] = 1;
		}
	}
	return res;
}

// 自动判断合法程式
// 根据数据类型来使用正则进行校验
// @_value: 需要判断的值
// @_type: 判断的类型
// user: 用户名/密码
// email: 邮箱类型
// ip: IP类型
// bankcard: 银行卡类型
// realname: 真实姓名
// date: 日期
// time: 时间
// datetime: 日期时间
function checkValues(_value, _type) {
	if (_type == 'user') {
		// 账号类型
		if (!/^[0-9a-z\_]{4,20}$/i.test(_value)) {
			return false;
		}
	}
	if (_type == 'email') {
		// 邮箱类型
		if (!/^[0-9a-zA-Z\_]+(\.[0-9a-zA-Z\_]+)*\@[0-9a-zA-Z\_]+(\.[0-9a-zA-Z\_]+)+$/.test(_value)) {
			return false;
		}
	}
	if (_type == 'ip') {
		// IP地址
		if (!/^[0-9]{1,3}(\.[0-9]{1,3}){3}$/.test(_value)) {
			return false;
		}
	}

	if (_type == 'bankcard') {
		// 银行卡类型
		if (!/^[0-9]{10,20}$/.test(_value)) {
			return false;
		}
	}

	if (_type == 'realname') {
		// 真实姓名
		// if (!/^[\u4E00-\u9FA5]{2,5}$/.test(_value)) {
		// 	return false;
		// }
		if (/[\;\,\'\"\\\/\(\)\^\%\#\@\*\&]/.test(_value)) {
			return false;
		}
	}

	if (_type == 'url') {
		if (!/^http(s)?\:\/\/[a-zA-Z0-9\-\%\$\#\@\_\/]+(\.[a-zA-Z0-9\%\$\#\@\_\-\/]+)+$/.test(_value)) {
			return false;
		}
	}

	if (_type == 'enom') {
		if (!/^[a-zA-Z0-9\_\-]+(\.[a-zA-Z0-9\_\-]+)+$/.test(_value)) {
			return false;
		}
	}

	if (_type == 'date') {
		// 日期格式
		if (!/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/.test(_value)) {
			return false;
		}
	}

	if (_type == 'time') {
		// 时间格式
		if (!/^[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/.test(_value)) {
			return false;
		}
	}

	if (_type == 'number') {
		// 数值类型
		if (!/^[0-9]+(\.[0-9]+)?$/.test(_value)) {
			return false;
		}
	}

	if (_type == 'int') {
		// 整数型
		if (!/^[0-9]+$/.test(_value)) {
			return false;
		}
	}
	if (_type == 'float') {
		// 小数型
		if (!/^[0-9]+\.[0-9]+$/.test(_value)) {
			return false;
		}
	}

	if (_type == 'datetime') {
		// 日期时间格式
		if (!/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/.test(_value)) {
			return false;
		}
	}

	if (_type == 'notnull') {
		if (_value == '') {
			return false;
		}
	}

    if (_type == 'alpha') {
        if (!/^[a-z]+$/i.test(_value)) {
            return false;
        }
    }

    if (_type == 'alphaNum') {
        if (!/^[a-z0-9]+$/i.test(_value)) {
            return false;
        }
    }

	return true;
}

// 史上最方便的提交函数...
// 能够根据参数全自动组装数据结构自动传送给服务器并返回服务器状态。
// 能够根据数据类型自动使用正则进行校验
// @_dom 包含所有需要提交的对象的框架
// @_url 提交到目标url
// @_prex 所有对象的data-post的前缀
// @_callok 提交成功调用的函数(msg>0的时候也不会调用它)
// @_callerr 提交失败调用的函数(服务器繁忙的时候调用)
function quickSubmit(_dom, _url, _ac, _prex, _callok, _callerr) {
	var doms = _dom.find("*");
	var params = {ac: _ac};
	var len = _prex.length;
	var isfalse = false;
	var false_tip = "";
	doms.each(function(){
		var post = $(this).data("post");
		if (post && post != '') {
			var prex = post.substr(0,len);
			if (prex == _prex) {
				// 是我们需要的东东
				// 判断他的类型决定如何取值
				var _key = post;
				if ($(this).hasClass("ex_editor_box")) {
					// 是文本框
					_v = $(this).val();
					if(!checkValues(_v, $(this).data("vtype"))) {
						isfalse = true;
						false_tip = $(this).data("vtip");
					}
					params[_key] = $(this).val();
				} else if ($(this).hasClass("ex_date_editor")) {
					// 日期选择框
					var _v = $(this).val();
					if(!checkValues(_v, $(this).data("vtype"))) {
						isfalse = true;
						false_tip = $(this).data("vtip");
					}
					params[_key] = _v;
				} else if ($(this).hasClass("ex_selector")) {
					// 选择框
					params[_key] = $(this).val();
				} else if ($(this).find(".ex_checkbox").length>0){
					var value = [];
					$(this).find(".ex_checkbox.checked").each(function() {
						var _v = $(this).data("value");
						if (_v && _v != '-1') {
							value.push(_v);
						}
					});
					params[_key] = value.join(",");
				} else if ($(this).find(".ex_radiobox").length>0){
					params[_key] = $(this).find(".ex_radiobox.checked").data("value");
				} else if ($(this).hasClass("ex_number_input")){
					params[_key] = $(this).val() ? $(this).val() : $(this).data("min");
				} else if ($(this).hasClass("ex_umeditor")) {	// UE编辑器
					if (!UM) {
						return;
					}
					if (!UM.getEditor($(this).attr("id"))) {
						return;
					}
					params[_key] = UM.getEditor($(this).attr("id")).getConetnt();
				} else {
					params[_key] = $(this).data("value");
				}
			}
		}
	});
	if (isfalse) {
		hideLoading();
		toast("<a style='color:#f33;'>"+false_tip+"</a>格式不合法,请重新输入!");
		return;
	}
	showLoading("正在提交...");
	AjaxPost(_url, params, function(obj){
		hideLoading();
		if (obj.msg>0) {
			toast(obj.param);
			return;
		}
		if (_callok) {
			_callok(obj);
		}
	}, _callerr);
}

// 快速删除数据
// @_dom: 要删除的tr结构体
// @_url: 删除事件提交给指定的模块
// @_ac: 删除事件提交给指定的方法
// @_callok: 删除成功的回调函数
function quickDelete(_dom, _url, _ac, _callok) {
	var id = _dom.data('id');
	askShow("你确定要删除ID:<a style='color:#f33'>"+id+"</a>的数据吗? 该操作不可逆,请谨慎选择!", "请核实", function(){
		showLoading("正在删除....");
		AjaxPost(_url, {ac: _ac, id: id}, function(obj){
			hideLoading();
			if (_callok) {
				_callok();
			}

			okshow("删除成功!");
			_dom.remove();
			return;
		});
	});
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

// 显示窗口并自动初始化的函数
// 通过此函数调用窗口显示的时候,将自动初始化窗口状态.
// 不用再使用手动清理界面了。
function showDialog(dom) {
	dom.find("*").each(function(){
		if ($(this).hasClass("ex_editor_box")) {
			if ($(this).hasClass("number")) {
				$(this).val("0");
			}else{
				$(this).val("");
			}
		}else if ($(this).hasClass("ex_checkbox")) {
			$(this).removeClass("checked");
		}else if ($(this).hasClass("ex_selector")) {
			$(this).val(0);
		}else if ($(this).hasClass("ex_radiobox")) {
			$(this).parent().find(".ex_radiobox").removeClass("checked");
			$(this).parent().find(".ex_radiobox").eq(0).addClass("checked");
		} else if ($(this).is("img")) {
			$(this).attr("src", $(this).data("default"));
			$(this).data("value", '');
		} else if ($(this).hasClass("ex_date_editor")) {
			$(this).val("");
		} else if ($(this).hasClass("ex_slider_bar")) {
			$(this).find(".ex_slider_bg").css("width",0);
			$(this).find(".ex_slider_trigger").css("left",0);
			var _text = $(this).data("format");
			$($(this).data("target")).text(_text ? _text.format({v:$(this).data("min")}) : $(this).data("min"));
		} else if ($(this).hasClass("ex_number_input")) {		// 范围数值容器
			var _min = $(this).data("min") ? $(this).data("min") : 0;
			$(this).val(_min);
			$(this).keyup();
		}
	});

	dialog_pop(dom);
}

// 以弹跳方式打开一个弹窗
function dialog_pop(_dom) {
	var _dialog  = _dom.find(".ex_dialog");
	_dialog.removeClass("dialog_popup");
	_dialog.addClass("dialog_popup");
	_dom.show();
}

// 打开客服系统
function openService(url) {
	window.open(url,'myServiceWindow','width=1016,height=766,resizable=no,menubar=no,location=no',true);
}

// 判断数组元素是否都为一种格式
function checkArrIs(_arr, preg) {
	for(var k in _arr) {
		if (!preg.test(_arr[k])) {
			return false;
		}
	}
	return true;
}

function utf8(wide) {
	 var c, s;
	 var enc = "";
	 var i = 0;
	 while(i<wide.length) {
	 c= wide.charCodeAt(i++);
	 // handle UTF-16 surrogates
	 if (c>=0xDC00 && c<0xE000) continue;
	 if (c>=0xD800 && c<0xDC00) {
	 if (i>=wide.length) continue;
	 s= wide.charCodeAt(i++);
	 if (s<0xDC00 || c>=0xDE00) continue;
	 c= ((c-0xD800)<<10)+(s-0xDC00)+0x10000;
	 }
	 // output value
	 if (c<0x80) enc += String.fromCharCode(c);
	 else if (c<0x800) enc += String.fromCharCode(0xC0+(c>>6),0x80+(c&0x3F));
	 else if (c<0x10000) enc += String.fromCharCode(0xE0+(c>>12),0x80+(c>>6&0x3F),0x80+(c&0x3F));
	 else enc += String.fromCharCode(0xF0+(c>>18),0x80+(c>>12&0x3F),0x80+(c>>6&0x3F),0x80+(c&0x3F));
	 }
	 return enc;
	 }
	 var hexchars = "0123456789ABCDEF";
	 function toHex(n) {
	 return hexchars.charAt(n>>4)+hexchars.charAt(n & 0xF);
	 }
	 var okURIchars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-";
	 function encodeURIComponentNew(s) {
	 var s = utf8(s);
	 var c;
	 var enc = "";
	 for (var i= 0; i<s.length; i++) {
	 if (okURIchars.indexOf(s.charAt(i))==-1)
	 enc += "%"+toHex(s.charCodeAt(i));
	 else
	 enc += s.charAt(i);
	 }
	 return enc;
 }


function lhc_getColor(number) {
 	var red_arr = [1,2,7,8,12,13,18,19,23,24,29,30,34,35,40,45,46];
 	var green_arr = [5,6,11,16,17,21,22,27,28,32,33,38,39,43,44,49];
 	var blue_arr = [3,4,9,10,14,15,20,25,26,31,36,37,41,42,47,48];
 	if (contains(red_arr, number)) {
 		return "red";
 	}else if (contains(green_arr, number)) {
 		return "green";
 	}else if (contains(blue_arr, number)) {
 		return "blue";
 	}else{
 		return "gray";
 	}
 }



// 判断数字的单双大小
function ballsToSDDX(ball) {
    var ball = parseInt(ball);
    var str = "";
    if (ball<5) { str ="小"; } else { str = "大"; }
    if (ball%2==0) { str += "双"; } else { str += "单"; }
    return str;
}

// 判断组三组六
function ball3or6(ball){
    str = "";
    if (ball.length != 5) {
        return "未知";
    }
    if (ball[2] == ball[3] && ball[3] == ball[4]) {
        return "豹子";
    }
    
    if (ball[2] == ball[3] || ball[3] == ball[4] || ball[4] == ball[2]) {
        return "组三";
    }
    
    return "组六";
}

    // 判断大小
function getDaxiao(ball) {
    var da = 0;
    var xiao = 0;
    for(var key in ball) {
        var val = ball[key];
        if (parseInt(val)<6) {
            xiao++;
        }else{
            da++;
        }
    }
    return da+":"+xiao;
}

// 判断奇偶
function getJiOu(ball) {
    var ji = 0;
    var ou = 0;
    for(var key in ball) {
    	var val = parseInt(ball[key]);
        if (val %2 == 1) {
            ji++;
        }else{
            ou++;
        }
    }
    return ji+":"+ou; 
}

// 判断形态
function getXingTai(ball) {
    if (!ball || ball.length !=3) {
        return "未知";
    }

    var xingtai = "三不同号";
    if (parseInt(ball[0]) + 1 == parseInt(ball[1]) && parseInt(ball[1]) + 1 == parseInt(ball[2])) {
        xingtai = "三连号";
    }else if (parseInt(ball[0]) == parseInt(ball[1]) && parseInt(ball[1]) == parseInt(ball[2])) {
        xingtai = "三同号";
    }else if (parseInt(ball[0]) == parseInt(ball[1]) || parseInt(ball[1]) == parseInt(ball[2]) || parseInt(ball[0]) == parseInt(ball[2])) {
        xingtai = "二同号";
    }
    return xingtai;
}

// 判断和值
function getHeZhi (ball) {
    if (!ball) {
        return 0;
    }
    var sum = 0;
    for(var key in ball) {
    	var val = ball[key];
        sum += parseInt(val);
    }
    return sum;
}

// 快速排序
// 修改了一点点
function quickSort(arr,key,type){
	if (type == undefined) {
		type = "asc";
	}
    if(arr.length <= 1){
    	return arr; 
    }

    var privotIndex = Math.floor(arr.length/2);  //向下取数组的中间值
    var pivot = arr.splice(privotIndex,1)[0];	//把当前数组的中间值，也就是对比值删除
    var left = [];
    var right = [];
    if (type == 'asc') {						//正序   			
    	for(var i = 0;i<arr.length;i++){
	        if(arr[i][key] < pivot[key]){
	            left.push(arr[i]);				//大于中间值的放后面
	        }else{
	            right.push(arr[i]);				//小于中间值的放前面
	        }
	    }
    }else{										//倒序	则反之
    	for(var i = 0;i<arr.length;i++){
	        if(arr[i][key] >= pivot[key]){
	            left.push(arr[i]);
	        }else{
	            right.push(arr[i]);
	        }
	    }
    }
    											//合并数组 
    return quickSort(left,key,type).concat([pivot],quickSort(right,key,type));
}



