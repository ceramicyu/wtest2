function UploadSignFile(opt) {
	var filename = "";
	var def_opt = {
		fileInput : null,
		url : "",
		uploadFile : [],
		maxSignSize: 0,
		curSelectId: "",
		fileExt:["jpg", "png"],
		filterFile : function(files) {
            if (this.fileExt.length == 0) {
                return files;
            }
                    
            var ext = files[0].name.substring( files[0].name.lastIndexOf('.') + 1);
            for (key in this.fileExt) {
                if (this.fileExt[key] == ext) {
                    return files;
    			}
            }
            alert("该文件类型不允许上传!");
            return null;
		},
		onSelect : function(selectFile, files){      // 提供给外部获取选中的文件，供外部实现预览等功能  selectFile:当前选中的文件  allFiles:还没上传的全部文件
			
		},
		onDelete : function(index, files){            // 提供给外部获取删除的单个文件，供外部实现删除效果  file:当前删除的文件  files:删除之后的文件
			
		},
		onProgress : function(file, loaded, total){  // 提供给外部获取单个文件的上传进度，供外部实现上传进度效果
			
		},
		onSuccess : function(file, responseInfo){    // 提供给外部获取单个文件上传成功，供外部实现成功效果
			
		},
		onFailure : function(file, responseInfo){    // 提供给外部获取单个文件上传失败，供外部实现失败效果
		
		},
		onComplete : function(responseInfo){         // 提供给外部获取全部文件上传完成，供外部实现完成效果
			
		},
		funGetFiles : function(e){  
			var self = this;
			var files = e.target.files || e.dataTransfer.files;
            // 过滤掉文件
            var f_files = this.filterFile(files);
            if (!f_files) {
                return true;
            }
            // 索引填充
            f_files[0].index = self.curSelectId;
            self.uploadFile.push(f_files[0]);
            
            var reader = new FileReader();
            reader.onload = function(e){
            	console.log("加载文件成功:"+f_files[0].index);
                $(f_files[0].index).attr("src",e.target.result );
            };
            reader.readAsDataURL(f_files[0]);
            
            this.onSelect(f_files, self.uploadFile);
			return true;
		},
		funDeleteFile : function(delFileIndex, isCb){
			var self = this;  // 在each中this指向没个v  所以先将this保留
            var tmpFile = [];
			// 目前是遍历所有的文件，对比每个文件  删除
			$.each(this.uploadFile, function(k, v){
				if (v.index != delFileIndex) { // 如果不是删除的那个文件 就放到临时数组中
					tmpFile.push(v);
				}
			});
			this.uploadFile = tmpFile;
			if(isCb){  // 执行回调
                // 回调删除方法，供外部进行删除效果的实现
                self.onDelete(delFileIndex, self.uploadFile);
			}
			return true;
		},
		funUploadFiles : function() {
			var self = this;  // 在each中this指向没个v  所以先将this保留
			if(self.uploadFile.length==0){
	            self.onComplete("OK");
		   	}
			// 遍历所有文件  ，在调用单个文件上传的方法
			$.each(this.uploadFile, function(k, v){
                self.funUploadFile(v);
			});       
		},
		funUploadFile : function(file){
            var self = this;  // 在each中this指向没个v  所以先将this保留
			var formdata = new FormData();
            formdata.append("fileList", file);	         		
            var xhr = new XMLHttpRequest();
	    	xhr.upload.addEventListener("progress",	 function(e){
		    	self.onProgress(file, e.loaded, e.total);
		    }, false); 

	    	xhr.addEventListener("load", function(e){
	            $(file.index).attr("data-bind", xhr.responseText);
	                    
		    	self.onSuccess(file, xhr.responseText);
		    	self.funDeleteFile(file.index, false);
		    	if(self.uploadFile.length==0){
					self.onComplete("OK");
		    	}
		    }, false);

		    // 错误
		    xhr.addEventListener("error", function(e){
		    	self.onFailure(file, xhr.responseText);
		    }, false);  
		
            xhr.open("POST",self.url, true);
            // xhr.setRequestHeader("X_FILENAME", file.name);
            xhr.send(formdata);
		},
         
		// 初始化
		init : function() {  // 初始化方法，在此给选择、上传按钮绑定事件
            var self = this;  // 克隆一个自身
			// 如果选择按钮存在
 			if(self.fileInput){
				// 绑定change事件
				this.fileInput.addEventListener("change", function(e) {
					console.log("打开文件");
					self.funGetFiles(e); 
				}, false);	
			}
		}
	};

	var param = $.extend(def_opt, opt);
	param.init();

	this.upload = function() {
		param.funUploadFiles();
	}

	this.isempty = function() {
		if (!param.uploadFile || param.uploadFile.length == 0) {
			return true;
		}
		return false;
	}
}