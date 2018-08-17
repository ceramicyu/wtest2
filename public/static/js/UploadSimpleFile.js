// 简单异步上传图片封装
// 
function UploadSimpleImage(img, url, _callok, _callfailed) {
	var _file = null;
	var allow_ext = ['jpg', 'png', 'jpeg'];
	var self = this;
	var id = Math.floor(Math.random()*8888+1000)+Math.floor((new Date()).getTime());
	var isLoading = false;
	var _progress = null;
	var _reset_src=null;
	var init_param = {};
	// 初始化..
	var init = function() {
		var file = $("body").append("<input id='file_"+id+"' style='display:none;' type='file' />");
		_file = file.find("#file_"+id);
		
		var _progress_format = "<div id='{id}' style='display:none;position:absolute;width:{width}px;height:16px;left:{left}px;top:{top}px;background:rgba(0,0,0,0.5);'> <div style='background:#45ca51;height:16px;line-height:16px;width:0%;text-align:center;color:#FFFFFF;transition:all 0.2s;-webkit-transition:all 0.2s;'></div> </div>";
		
		init_param.id = "progress_"+id;
		init_param.width = img.width();
		init_param.height = img.height();
		init_param.left = img.offset().left;
		init_param.top = init_param.height - img.offset().top - 16;
		img.parent().css("position","relative");
		img.parent().append(_progress_format.format(init_param));
		_progress = $("#progress_"+id);
		bindEvent();
		img.css("background","rgba(0,0,0,0.3)");
		_reset_src = img.attr("src");
	}


	var bindEvent = function() {
		img.unbind("click").bind("click", function(){
			if (isLoading){
				return;
			}
			_file.val("");
			_file.click();
		});

		_file.change(function(e) {

			var files = e.target.files || e.dataTransfer.files;
			file = files[0];
			var ext = file.name.substring(file.name.lastIndexOf(".")+1);
			var isallow = false;
			for(var k in allow_ext) {
				if (allow_ext[k] == ext) {
					isallow = true;
					break;
				}
			}

			if (!isallow) {
				toast("不允许的文件类型:"+ext+"!");
				return;
			}
			
			if (!/^[a-zA-Z0-9\_\.]+$/.test(file.name)) {
				toast("文件名不能包含中文或者特殊字符!");
				return;
			}
			isLoading = true;
			// 异步加载...
			var reader = new FileReader();
			reader.onload = function(e){
                img.attr("src", e.target.result);
                upload();
            };
			reader.readAsDataURL(file);
		});
	}

	var upload = function() {
		var formdata = new FormData();
        formdata.append("fileList", file);
       	_progress.show();		
        var xhr = new XMLHttpRequest();

	    xhr.addEventListener("load", function(e) {
	    	if (xhr.responseText.substr(0,1) == '<') {
	    		toast("服务器繁忙,请稍后再试...");
	    		return;
	    	}

            var obj = eval("("+xhr.responseText+")");
            if (typeof(obj) == 'object') {
            	if (obj.msg>0) {
            		toast(obj.param);
            		return;
            	}
            } else {
            	toast("服务器繁忙,请稍后再试!");
            	return;
            }

	    	img.data("value", obj.data);
	    	_progress.find("div").css("width","100%");
	    	_progress.find("div").text("UPLOAD OK");
	    	isLoading = false;
	    	if (_callok) {
	    		_callok(obj);
	    	}
	    }, false);
	    // 错误
	    xhr.addEventListener("error", function(e){
	    	if( _callfailed) { 
	    		_callfailed ();
	    	}
	    }, false);
		xhr.upload.onprogress = function(e){
	    	onProgress(e.loaded, e.total);
	    };
	    xhr.upload.onloadend = function(e){
	    	_progress.find("div").css("width","90%");
	    };
        xhr.open("POST", url, true);
        xhr.send(formdata);
	}

	var onProgress = function(loaded, total) {
		var percent = loaded/total*90;
		_progress.find("div").css("width",percent+"%");
	}

	init();

	//----------------接口区 ---------
	this.reset = function(){
		_progress.hide();
		img.attr("src", _reset_src);
	}
}