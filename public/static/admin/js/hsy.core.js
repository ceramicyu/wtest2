/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// 实现一些通用的JS脚本
// @编写者: 和顺源技术团队
// @编写日期: 2016-10-03
// 需要Jquery库的支持

$(".noselect").bind("selectstart",function() {
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

// 封装一些正则表达式, 实现各种格式数据的前端校验

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

// 校验该字符是否是正确的常规密码格式
String.prototype.checkUsername = function () {
    return /^[0-9A-Z_\.\@\#\$\%\^\!\*\(\)]{6,20}$/i.test(this);
}

String.prototype.checkVerify = function() {
	return /^[0-9]{4}$/.test(this);
}
//校验该字符是否是时间规格
String.prototype.checkTimeHis = function() {
	return /^([0-1]?[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])/.test(this);
}

// 简化$.ajax操作的函数
function AjaxPost(posturl, postdata, funsuccess, funerror) {
	
	var errfunc = funerror;
	if (!errfunc) {
		errfunc = function() {
			alert("服务器繁忙,请稍后再试。");
		};
	}
	
    $.ajax({
       url : posturl,
       data: postdata,
       dataType: "json",
       type: "post",
       success: function(obj){
       		
       		if (obj.msg == 40000) {
       			tipshow("登录超时,请重新登录!");
       			if (window.parent) {
       				window.parent.location.reload(true);
       			} else {
       				window.location.reload(true);
       			}
       		}
       		
       		if (obj.msg>0) {
       			hideLoading();
       			tipshow(obj.param);
       			return;
       		}

       		if (funsuccess) {
       			funsuccess(obj);
       		}
       },
       error: funerror
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
	return {d:day, h:hour, m:min, s:sec};
}

// 实现特殊弹窗方法
function _alert(title, content) {
	if (!title || title=='') {
		title = "提示";
	}
	if (!content) {
		content = '';
	}
	
	var html_text = "<div class='_alert_mark'><div class='_alert_box'><div class='_alert_title'>"+title+"</div>";
	html_text += "<div class='_alert_content'>"+content+"</div><div class='_alert_tools'><a class='_alert_btn_ok' href='javascript:;'>确定</div></div></div></div>";
	$("body").append(html_text);
	_alert_click_event();
}

// 确定按钮绑定事件
function _alert_click_event() {
	$("._alert_btn_ok").unbind("click");
	$("._alert_btn_ok").bind('click',function(){
		$("._alert_mark").fadeOut(300, function(){
			$("._alert_mark").remove();
		});
	});
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

// 取小数点后几位
function FloatConvert(v, o) {
	var pos = v.toString().indexOf('.');
	if (pos<0) {
		v+=".";
		for (var i=0; i<o; i++) {
			v+="0";
		}
	} else {
		var zc = v.toString().length - pos - 1;
		if (zc>o) {
			return v.toString().slice(0, pos+3);
		}else if (zc<o) {
			for(var i = zc; i < o; i++) {
				v+="0";
			}
		}else{
			return v;
		}
	}
	return v;
}

// 返回金额格式
function _getPrice(v) {
	
}



$(function(){
	$("input[type=text].number").keyup(function(){
		var value = $(this).val();
		value = value.replace(/\D/g, '');
		if ($(this).data('max')) {
			value = parseInt(value) > $(this).data('max') ? $(this).data('max') : value;
		}
		if ($(this).data('min')) {
			value = parseInt(value) < $(this).data('min') ? $(this).data('min') : value;
		}
		if (value=='') {
			value=1;
		}
			
		$(this).val(value);
			if ($(this).data("target")) {
				$("."+$(this).data("target")).html(2*parseInt(value));
		}
	});
	
	
});

