// 幻灯片实现
function slideBanner(_container, _delay, default_url) {
		var container = _container;
		var delay = _delay;
		var self = this;
		var width = _container.width();
		var height = _container.height();
		var pic_list = [];
		var timerid = 0;
		var cur_idx = 0;
		// 初始化
		this.init = function() {
			$("style").append(".banner_list{height:"+height+"px;}");
			$("style").append(".banner_tools{ position: absolute; width:100%; bottom:10px; text-align:center; height:18px; line-height:18px;}");
			$("style").append(".banner_tools .banner_anchor { width:10px; height:10px; background:#eee; display:inline-block; border-radius:10px; margin:0px 2px 0px 2px; }");
			$("style").append(".banner_tools .banner_anchor.active{ background:#33f; }");
			$("style").append(".banner_list .banner_item {width:"+width+"px; height:"+height+"px; float:left}")
			$("style").append(".banner_list .banner_item img{ width:100%; height:100%; }");
			_container.html('<div class="banner_list"></div><div class="banner_tools"></div>');
		}

		// 更新图片
		this.updateBanner = function() {
			container.html('<div class="banner_list"></div><div class="banner_tools"></div>');
			container.find(".banner_list").css("height", height+"px");
			container.find(".banner_list").css("width", ((pic_list.length+1)*width)+"px");
			var default_str = '';
			if (default_url) {
				default_str = "onerror='this.src=\""+default_url+"\"'";
			}
			for(var i = 0; i < pic_list.length; i++) {
				container.find(".banner_list").append('<div class="banner_item"><img src="'+pic_list[i].url+'" data-link="'+pic_list[i].link+'" title="'+pic_list[i].title+'" '+default_str+' /></div>');
				container.find(".banner_tools").append('<a class="banner_anchor" data-id="'+i+'"></a>');
			}
			container.find(".banner_list").append('<div class="banner_item"><img src="'+pic_list[0].url+'" data-link="'+pic_list[0].link+'" title="'+pic_list[0].title+'" '+default_str+'  /></div>');
			container.find(".banner_tools").find(".banner_anchor").eq(0).addClass("active");
		}
		// 添加图片
		this.addChild = function(_url, _link, _title){
			pic_list.push({url: _url, link: _link, title: _title});
			self.updateBanner();
			self.bindEvent();
		}

		// 绑定事件
		this.bindEvent = function() {
			// 幻灯片锚点点击事件
			container.find(".banner_tools").find(".banner_anchor").unbind("click");
			container.find(".banner_tools").find(".banner_anchor").bind("click", function(){
				container.find(".banner_tools").find(".banner_anchor").removeClass("active");
				$(this).addClass("active");
				var idx = parseInt($(this).data("id"));
				cur_idx = idx;
				var _left = -(cur_idx*width)+"px";
				container.find(".banner_list").animate({marginLeft:_left });
			});

			container.find(".banner_list").mouseenter(function(e){
				self.stop();
			});

			container.find(".banner_list").mouseleave(function(e){
				self.start();
			});

			container.find(".banner_list").find(".banner_item").find("img").unbind("click");
			container.find(".banner_list").find(".banner_item").find("img").bind("click", function(){
				window.open($(this).data("link"));
			});
		}

		// 更新幻灯片
		this.update = function() {
			cur_idx++;
			var _left = -(cur_idx*width)+"px";
			container.find(".banner_list").animate({marginLeft:_left}, function(){
				if (cur_idx == pic_list.length) {
					container.find(".banner_list").animate({marginLeft:"0px"},0);
					cur_idx=0;
				}
				container.find(".banner_tools").find(".banner_anchor").removeClass("active");
				container.find(".banner_tools").find(".banner_anchor").eq(cur_idx).addClass("active");
			});
		}

		// 启动幻灯片
		this.start = function() {
			timerid = setInterval(self.update, delay);
		}

		// 停止幻灯片
		this.stop = function() {
			clearInterval(timerid);
		}

		this.init();
}

// 封装自定义滚动条
function ScrollContainer(domname) {
	var press_scroll = false;
	var offset_y = 0;
	var soffset_y = 0;
	var cheight = $(domname).get(0).scrollHeight;
	var dheight = $(domname).height();
	// 更新滚动条尺寸
	this.update = function() {
		sheight = Math.floor(dheight/cheight*dheight);
		sheight = sheight < 100 ? 100 : sheight;
		$(domname + " .scrollbar").height(sheight);
	}
					
	this.update();
					
	// 鼠标滚轮事件
	$(domname).bind("mousewheel",function(event){
		var i = $(this).scrollTop()+event.originalEvent.deltaY;
		$(this).scrollTop(i);
	});
					
	// 滚动事件重绘滚动条
	$(domname).bind("scroll", function() {
		var stop = $(this).scrollTop() / ($(this).get(0).scrollHeight - $(this).height()) * ($(this).height() - sheight) + $(this).scrollTop();
		$(domname+" .scrollbar").css("top",(stop)+"px");
	});
					
	// 绑定滚动条拖拽事件
	$(domname+" .scrollbar").bind("mousedown", function(event){
		press_scroll = true;
		soffset_y = $(this).offset().top;
		offset_y = event.pageY;
	});
					
	// 鼠标放开
	$(domname).bind("mouseup", function(){
		press_scroll = false;
	});
					
	// 鼠标移动
	$(domname).bind("mousemove", function(event){
		if (press_scroll == true) {
			var newTop = event.pageY - offset_y + soffset_y;
			var stop = newTop / ($(this).height() - $(domname+" .scrollbar").height()) * ($(domname).get(0).scrollHeight - $(domname).height())
			$(domname).scrollTop(stop);
		}
	});
}

// 倒计时类
function CDTimer(opt) {
	var def_opt = {
		day_obj: null,		// 天数控件
		hour_obj: null,		// 小时控件
		min_obj: null,		// 分钟控件
		sec_obj: null,		// 秒控件
		callback: null,		// 倒计时结束后的回调函数
		endTime: 0,			// 什么时候结束
		url: '',			// 倒计时结束后从哪里获取新的倒计时记录
		type: '',			// 彩种类型
	};
	var param = $.extend(def_opt, opt);
	
	var self = this;
	
	// 启动倒计时
	this.start = function (endTime) {
		param.endTime = endTime+this.getTime();
		self.isrun = true;
		self.update();
	}
	
	// 获取当前时间戳
	this.getTime = function() {
		return Math.floor((new Date()).getTime()/1000);
	}
	
	// 更新倒计时
	this.update = function() {
		if (!self.isrun) {
			return;	// 终止倒计时
		}
		
		var now = self.getTime();
		var delay = param.endTime - now;
		
		if (delay<=0) {
			self.isrun = false;
			if (param.callback) {
				param.callback();
			}
			return;
		}
		
		self.updateOutput(delay);	// 更新附加DOM
		
		setTimeout(self.update, 100);
	}
	
	// 终止倒计时
	this.stop = function () {
		self.isrun = false;
	}
	
	// 更新数据到控件上
	this.updateOutput = function(delay) {
		if (param.day_obj) {
			param.day_obj.html(Math.floor(delay/86400));
		}
		if (param.hour_obj) {
			param.hour_obj.html(Math.floor((delay%86400)/3600));
		}
		if (param.min_obj) {
			param.min_obj.html(Math.floor((delay%3600)/60));
		}
		if (param.sec_obj) {
			param.sec_obj.html(delay%60);
		}		
	}
}


// 走势图控件
function ZSObject(data) {
					
	var array_3 = ['百位', '十位', '个位'];
	var array_5 = ['万位', '千位', '百位', '十位', '个位'];
	var array_10 = ['第一位', '第二位', '第三位', '第四位', '第五位','第六位','第七位','第八位','第九位','第十位'];
	var array_pcdd = ['特码1', '特码2', '特码3'];
	var head_array = [];
	if (data.ballcount == 3) {
		head_array = array_3;
	}else if (data.ballcount == 5) {
		head_array = array_5;
	}else if (data.ballcount == 4) {
		head_array = array_pcdd;
		data.ballcount = 3;
	}else{
		head_array = array_10;
	}
	
	// 统计数据
	this.staticData = function (obj) {
		var static_obj = new Object();
		static_obj.yilou = [];	// 遗漏
		static_obj.show = [];	// 出现次数
		static_obj.maxyilou = [];	// 最大遗漏
		static_obj.maxcombat = [];	// 最大连出
		static_obj.combat = [];		// 连出次数
		static_obj.avgyilou = [];	// 平均遗漏
						
		for (var i=0; i<obj.list.length; i++) {
			static_obj.yilou[i] = [];
			for (var n=0; n<obj.ballcount;n++) {
				static_obj.yilou[i][n] = [];
				// 最大遗漏初始化
				if (!static_obj.maxyilou[n]) {
					static_obj.maxyilou[n] = [];
				}
				// 出现次数初始化
				if (!static_obj.show[n]) {
					static_obj.show[n] = [];
				}
				// 连出次数初始化
				if (!static_obj.combat[n]) {
					static_obj.combat[n] = [];
				}
				// 最大连出初始化
				if (!static_obj.maxcombat[n]) {
					static_obj.maxcombat[n] = [];
				}
				for (var j=0; j<(parseInt(obj.maxnumber)-parseInt(obj.minnumber)+1); j++) {
					// 相等 遗漏清0
					if (parseInt(obj.minnumber)+j == obj.list[i].balls[n]) {
						static_obj.yilou[i][n][j] = 0;
										
						if (!static_obj.show[n][j]) {
							static_obj.show[n][j] = 1;
						} else {
							static_obj.show[n][j]++;
						}
										
						if (!static_obj.combat[n][j]) {
							static_obj.combat[n][j] = 1;
						}else{
							static_obj.combat[n][j]++;
						}
										
						if (!static_obj.maxcombat[n][j]) {
							static_obj.maxcombat[n][j] = static_obj.combat[n][j];
						}else if(static_obj.maxcombat[n][j] < static_obj.combat[n][j]){
							static_obj.maxcombat[n][j] = static_obj.combat[n][j];
						}
										
					}else{
						static_obj.combat[n][j] = 0;
									
						if (i==0) {
							static_obj.yilou[i][n][j] = 1;
							static_obj.maxyilou[n][j] = 1;
						}else{
							static_obj.yilou[i][n][j] = static_obj.yilou[i-1][n][j]+1;
							if ((!static_obj.maxyilou[n][j]) || static_obj.yilou[i][n][j] > static_obj.maxyilou[n][j]) {
								static_obj.maxyilou[n][j] = static_obj.yilou[i][n][j];	// 更新最大遗漏
							}
						}
					}
				}	// end - for numbercount
			}	// end - for ballcount					
		}	// end - for data count
		// 开始计算平均遗漏
		// 公式 总期数 / 出现次数
		var rows = obj.list.length;
		for (var i = 0 ;i < static_obj.show.length; i++) {
			if (!static_obj.avgyilou[i]) {
				static_obj.avgyilou[i] = [];
			}
			for (var j = 0; j < static_obj.show[i].length; j++) {
				if (!static_obj.show[i][j]) {
					static_obj.show[i][j] = 0;
					static_obj.avgyilou[i][j] = 0;
				}else{
					static_obj.avgyilou[i][j] = Math.floor( rows / static_obj.show[i][j]);
				}
			}
		}
		return static_obj;
	}	// end - for staticData function
	
	this.init = function (obj) {
		// 数据分析		
		var static_obj = this.staticData(obj);
		// 构建表格		
		$(".zoushi_table").html("");
		$(".model").remove();
		// 绘制表头
		var text = "<tr><td class='qishu' rowspan=2>期号</td><td rowspan=2 colspan="+obj.ballcount+">开奖号码</td>";
		for(var i=0; i<obj.ballcount; i++) {
			text += "<td colspan="+(obj.maxnumber-obj.minnumber+1)+">"+head_array[i]+"</td>";
		}
		text+="</tr><tr>";
		for (var i=0; i<obj.ballcount;i++) {
			for (var n=obj.minnumber; n<=obj.maxnumber;n++) {
				text+="<td>"+n+"</td>";
			}
		}
		text+="</tr>";
		
		// 绘制内容
		for (var i=0; i<obj.list.length; i++) {
			text+="<tr>";
			// 期数
			text+="<td>"+obj.list[i].qishu+"</td>";
			// 开奖号码
			for (var n=0; n<obj.list[i].balls.length;n++) {
				text+="<td><div class='ball_kjhm'>"+obj.list[i].balls[n]+"</div></td>";
			}
			// 走势
			for (var n=0; n<obj.list[i].balls.length;n++) {
				for (var j=0; j<(obj.maxnumber-obj.minnumber+1); j++) {
					if (parseInt(obj.minnumber)+j == obj.list[i].balls[n]) {
						if (n%2==0) {	// 设置为2种类型
							text+="<td><div class='ball1'></div><div class='ball r_"+n+"_c_"+i+"'>"+(parseInt(obj.minnumber)+j)+"</div></td>";
						}else{
							text+="<td><div class='ball1'></div><div class='balls r_"+n+"_c_"+i+"'>"+(parseInt(obj.minnumber)+j)+"</div></td>";
						}
					}else{
						text+="<td><div class='ball_yilou'>"+static_obj.yilou[i][n][j]+"</div></td>";
					}
				}
			}
			text+="</tr>";
		}
		
		// 开始写入统计数据
		// 写入出现总次数
		text += "<tr><td colspan="+(parseInt(data.ballcount)+1)+">出现总次数</td>";
		for (var i = 0; i < obj.ballcount; i++) {
			for (var j=0; j < (parseInt(obj.maxnumber)-parseInt(obj.minnumber)+1); j++) {
				text += "<td>"+(static_obj.show[i][j] ? static_obj.show[i][j] : 0 )+"</td>";
			}
		}
		text += "</tr>";
		// 写入平均遗漏值
		text += "<tr><td colspan="+(parseInt(data.ballcount)+1)+">平均遗漏值</td>";
		for (var i = 0; i < obj.ballcount; i++) {
			for (var j=0; j < (parseInt(obj.maxnumber)-parseInt(obj.minnumber)+1); j++) {
				text += "<td>"+(static_obj.avgyilou[i][j] ? static_obj.avgyilou[i][j] : 0 )+"</td>";
			}
		}
		text += "</tr>";
		// 写入最大遗漏值
		text += "<tr><td colspan="+(parseInt(data.ballcount)+1)+">最大遗漏值</td>";
		for (var i = 0; i < obj.ballcount; i++) {
			for (var j=0; j < (parseInt(obj.maxnumber)-parseInt(obj.minnumber)+1); j++) {
				text += "<td>"+(static_obj.maxyilou[i][j] ? static_obj.maxyilou[i][j] : 0 )+"</td>";
			}
		}
		text += "</tr>";
		// 写入平均遗漏值
		text += "<tr><td colspan="+(parseInt(data.ballcount)+1)+">最大连出</td>";
		for (var i = 0; i < obj.ballcount; i++) {
			for (var j=0; j < (parseInt(obj.maxnumber)-parseInt(obj.minnumber)+1); j++) {
				text += "<td>"+(static_obj.maxcombat[i][j] ? static_obj.maxcombat[i][j] : 0 )+"</td>";
			}
		}
		text += "</tr>";			
		// 再次写入表头
		text += "<tr><td class='qishu' rowspan=2>期号</td><td rowspan=2 colspan="+obj.ballcount+">开奖号码</td>";
		for(var i=0; i<obj.ballcount; i++) {
			text += "<td colspan="+(parseInt(obj.maxnumber)-parseInt(obj.minnumber)+1)+">"+head_array[i]+"</td>";
		}
		text+="</tr><tr>";
		for (var i=0; i<obj.ballcount;i++) {
			for (var n=obj.minnumber; n<=obj.maxnumber;n++) {
				text+="<td>"+n+"</td>";
			}
		}
		text+="</tr>";
		$(".zoushi_table").html(text);
		
		//  开始绘制折线
		setTimeout(function(){
			//alert("表格宽度:"+$(".zoushi_table").width()+"表格高度:"+$(".zoushi_table").height());
			$(".zoushi_container").find("canvas").remove();
			$(".zoushi_container").append("<canvas width='"+$(".zoushi_table").width()+"' height='"+$(".zoushi_table").height()+"' class='model'>您的浏览器不支持显示折线</canvas>");
			
			var ctx = $(".model").get(0).getContext("2d");
			ctx.clearRect(0,0,$(".zoushi_table").width(),$(".zoushi_table").height());
			ctx.lineWidth=2;
			var offsetX = $(".model").offset().left;
			var offsetY = $(".model").offset().top;
			for (var n=0; n<(obj.maxnumber-obj.minnumber+1);n++) {
				var isBegin = false;
				ctx.beginPath();
				if (n%2 == 0) {
					ctx.strokeStyle="#11B35E";
				}else{
					ctx.strokeStyle="#EA721A";
				}
				for (var i=0; i<obj.list.length; i++) {
					var target = $(".r_"+n+"_c_"+i);
					if (!target || !target.offset()) {
						continue;
					}
					var x = target.offset().left + target.width()/2-offsetX;
					var y = target.offset().top + target.height()/2-offsetY;
					if (!isBegin) {
						ctx.moveTo(x,y);
						isBegin=true;
					}else{
						ctx.lineTo(x,y);
					}
				}
				ctx.stroke();
				ctx.closePath();
			}
		
		},300);
		
	}
	
	this.showZX = function () {
		$(".model").fadeIn(300);
	}
	
	this.hideZX = function() {
		$(".model").fadeOut(300);
	}
	
	// 显示遗漏
	this.showYL = function() {
		$(".ball_yilou").show();
	}
	
	// 隐藏遗漏
	this.hideYL = function() {
		$(".ball_yilou").hide();
	}

	// 清除
	this.clear = function(){
		$(".zoushi_table").html("<tr><td colspan='100%' class='empty'>暂无相关数据</td></tr>");
		$(".zoushi_container canvas").remove();
		$(".zoushi_container").append("");
	}
	
	this.init(data);
}	// end - for object


// 多表联动
// @maxcount 最大的数量
// @rows 多少个表
// @dom_a 每个元素的前缀
// @tpl 每个联动的模版
function multiTableCreate (maxcount, rows, dom_a, tpl) {
	var av_count = maxcount/rows;
	var mod = maxcount%rows;
	var k = 1;
	for (var i = 0; i < rows; i++) {
		_max = k+av_count+(mod>i?1:0)-1;
		$(dom_a+i).html("");
		for (var n = k; n <= _max; n++) {
			$(dom_a+i).append(tpl.format({key:k}));
			k++;
		}
	}
}

// 测试自适应
// @f_dom: 父控件
// @child_dom: 子控件
// @child_don_w: 子控件的理想宽度
// @child_dom_h: 子控件的理想高度
function allDeviceDom(f_dom, child_dom, child_dom_w, child_dom_h) {
	var padding_left = f_dom.css("padding-left");
	var padding_right = f_dom.css("padding-right");
	var f_width = f_dom.width() - parseInt(padding_left.substring(0, padding_left.length-2)) - parseInt(padding_right.substring(0, padding_right.length-2)) - 5;

	// 获取单个子元素的宽高边框
	var child_left = child_dom.css("margin-left");
	var child_right = child_dom.css("margin-right");

	var spline = parseInt(child_left.substring(0, child_left.length-2)) + parseInt(child_right.substring(0, child_right.length-2));
	var max_count = f_width / (child_dom_w+spline);		// 理想状态下可以放几个
	var less_count = f_width % (child_dom_w+spline);	// 剩余多少空间
	var avger = less_count / max_count;
	var final_width = child_dom_w+avger;
	if (max_count < 1) {
		final_width = avger-spline;
	}
	child_dom.width(final_width);
	child_dom.height(child_dom_h*(final_width/child_dom_w));
}

// 分页表格实现
// @_container: 容器指针
// @_head: 表头html代码
// @_body_format: 单个tr的格式
// @_post_url: 获取数据url
// @_param: 获取数据参数,
// @_count: 每页数据的数量,不传默认为20条
// @_bindEvent: 事件绑定函数
function pageTable(_container, _head, _body_format, _post_url, _param, _bindEvent, _count, _callprev, _search) {
	
	if (!_container) {
		return;
	}
	if (!_count) { _count=20; }
	if (!_param) { _param={}; }
	if (_count > 100) {_param['limit'] = _count}
	var first = 1;
	this.container = _container;
	this.pagecount = 0;	// 页数量
	this.totaldata = 0;	// 总数据数
	this.curIndex = 0;	// 当前页ID
	this.datalist = [];	// 所有的数据
	this.loadPage = 0;	// 加载服务器的页码
	this.param = null;	// 扩展参数
	this.callprev = _callprev;
	this.servercount = 0;
	this.calltoggle = null;
	this.trycount = 0;	// 拉取数据重试次数,避免陷入死循环
	this.empty_string = "<tr><td colspan='100%'>暂无相关数据...</td></tr>";
	var self = this;
	_container.html('<div class="ex_page"><table class="ex_custom_table"><thead>'+_head+'</thead><tbody></tbody></table><div class="ex_page_tools"></div><div class="page_info"></div></div>');

	// 获取数据
	this.getData = function(order_field, order_type) {
		if (_search) {
			_search.find("*").each(function(){
				var _post = $(this).data("post");
				if ( _post && _post !== '') {
					if ($(this).hasClass("ex_editor_box") || $(this).hasClass("ex_selector") || $(this).hasClass("ex_date_editor") || $(this).hasClass("ex_search_box")) {
						_param[_post] = $(this).val();

					} else if ($(this).hasClass("ex_radiobox")) {
						if ($(this).hasClass("checked")) {
							_param[_post] = $(this).data("value");
						}
					} else if ($(this).hasClass("ex_checkbox")) {
						if ($(this).hasClass("checked")) {
							if (_param[_post] && _param[_post] !== '') {
								_param[_post] += ','+$(this).data("value");
							}else{
								_param[_post] = $(this).data("value");
							}
						}
					}else{
						_param[_post] = $(this).data("value");
					}
				}
			});
		}
		// self.servercount = self.datalist.length;
		if (self.servercount === 0) {
			self.loadPage = 0;
		} else {
			var bidx = (self.curIndex) * _count;
			if (!self.datalist[bidx]) {
				self.loadPage = Math.floor(bidx / self.servercount);
			}else {
				var eidx = (self.curIndex) * _count + _count;
				if (!self.datalist[eidx]) {
					self.loadPage = Math.floor(eidx / self.servercount);
				}
			}
		}
		showLoading("正在载入...");
		AjaxPost(_post_url, $.extend(_param, {pageid: self.loadPage, first: first, order: order_field, order_type:order_type}), function(obj){
			
			if (obj.msg>0){
				hideLoading();
				tipshow(obj.param);
				return;
			}

			if (!obj.data || !obj.data.list) {
				self.buildData();		// 构建数据
				return;
			}
			
			var _obj_list = obj.data.list;
			if (self.callprev) {
				_obj_list = self.callprev(_obj_list);
			}
			var idx = self.servercount === 0 ? 0 : self.servercount*self.loadPage;
			for(var k in _obj_list) {
				self.datalist[idx] =_obj_list[k];
				idx++;
			}
			if (first) {	// (优化方案1:) 第一次拉取数据得到总数据数, 后续操作将不再获取
				if (!obj.data.pageinfo || obj.data.pageinfo < obj.data.list.length) {
					obj.data.pageinfo = obj.data.list.length;
				}

				if (obj.data.pageinfo) {
					self.totaldata = obj.data.pageinfo;		// 总数据数
					self.pagecount = Math.ceil(obj.data.pageinfo / _count);
				}

				self.servercount = obj.data.list.length;		// 服务器的数据长度
				first = 0;
			}
			if (obj.data.param) {
				self.param = obj.data.param;
			}
			self.buildData();		// 构建数据
		});
	}
	
	// 构建数据
	this.buildData = function() {
		var _begin = self.curIndex*_count;
		var _end = _begin+_count;
		_end = _end >= self.totaldata ? self.totaldata : _end;
		self.trycount++;
		// 判断数据是否已经用完
		if ((_end < self.totaldata && (!self.datalist[_begin] || !self.datalist[_end])) || (!self.datalist[_begin] && !self.datalist[_end])) {
			if (self.trycount >= 3) {
				hideLoading();
				_container.find(".ex_custom_table tbody").html(self.empty_string);
				return;
			} else {
				self.trycount++;
				self.getData();
				return;
			}
		}
		hideLoading();
		_container.find(".page_info").html("共<a style='color:#f33'>"+self.pagecount+"</a>页: <a style='color:#f33'>"+self.totaldata+"</a>条数据 当前显示: <a style='color:#f33'>"+(_begin+1)+"</a> to <a style='color:#f33'>"+_end+"</a>");
		// 构建数据
		_container.find(".ex_custom_table tbody").html("");
		// 先写入锁定的数据
		var pageData = [];
		
		for (var k = _begin; k < _end; k++) {
			var _cache = self.datalist[k];
			if (!_cache) {
				continue;
			}
			_cache._idx = k;
			pageData.push(_cache);
			_container.find(".ex_custom_table tbody").append(_body_format.format(_cache));
		}

		if (self.calltoggle) {
			self.calltoggle(pageData);	// 绑定切换页面事件
		}

		if (_bindEvent) { _bindEvent(self); }	// 绑定事件如果有
		self.buildPageBtn();	// 创建页码按钮
	}

	// 构建追加数据
	this.appendData = function(datalist) {
		_container.find(".ex_custom_table tbody").append(_body_format.format(datalist));
		if (_bindEvent) { _bindEvent(self); }	// 绑定事件如果有
	}

	// 创建页码按钮
	this.buildPageBtn = function() {
		_container.find(".ex_page_tools").html("<div class='ex_page_btn first' data-id='first'>首页</div>");
		_container.find(".ex_page_tools").append("<div class='ex_page_btn left_arrow' data-id='prev'><</div>");
		var k = self.curIndex - 2;	// 返回前两个按钮
		k = k < 0 ? 0 : k;
		for(var i = k; i < self.curIndex; i++) {
			_container.find(".ex_page_tools").append("<div class='ex_page_btn' data-id='"+i+"'>"+(i+1)+"</div>");
		}
		_container.find(".ex_page_tools").append("<div class='ex_page_btn active' data-id='"+self.curIndex+"'>"+(self.curIndex+1)+"</div>");
		var n = self.curIndex + 3;			// 返回后两个按钮
		n = n > self.pagecount ? self.pagecount : n;

		for(var i = self.curIndex+1; i<n; i++) {
			_container.find(".ex_page_tools").append("<div class='ex_page_btn' data-id='"+i+"'>"+(i+1)+"</div>");
		}

		// 如果当前按钮后还有至少两个按钮,则显示...
		if (n < self.pagecount-1) {
			_container.find(".ex_page_tools").append("...");
		}

		if (self.curIndex < self.pagecount-3) {
			_container.find(".ex_page_tools").append("<div class='ex_page_btn' data-id='"+(self.pagecount-1)+"'>"+self.pagecount+"</div>");
		}
		_container.find(".ex_page_tools").append("<div class='ex_page_btn right_arrow' data-id='next'>></div>");
		_container.find(".ex_page_tools").append("<div class='ex_page_btn last' data-id='last'>尾页</div>");
		_container.find(".ex_page_tools").append("<div class='ex_page_go_container'><input class='ex_page_go_edit' maxlength='6' value='"+(self.curIndex+1)+"' type='text' /><div class='ex_page_go'></div></div>");
		self.bindEvent();
	}

	// 页码按钮事件绑定
	this.bindEvent = function() {
		// 点击页码按钮
		_container.find(".ex_page_btn").unbind("click").bind("click", function(){
			if ($(this).hasClass("active") || $(this).hasClass("disabled")) {
				return;
			}
			self.trycount = 0;
			var id = $(this).data("id");
			if (id === 'prev') {	// 上一页
				if (self.curIndex === 0) {
					return false;
				} else {
					self.curIndex--;
					self.buildData();	// 构建数据
				}
			} else if (id === 'next') {	// 下一页
				if (self.curIndex >= self.pagecount-1) {
					self.curIndex = self.pagecount-1;
					return false;
				} else {
					self.curIndex++;
					self.buildData();
				}
			} else if (id === 'first') {
				self.curIndex = 0;
				self.buildData();
			} else if (id === 'last') {
				self.curIndex = self.pagecount-1;
				self.buildData();
			} else {
				// 显示指定页码
				id = parseInt(id);
				if ( id < 0 ){ id = 0; } else if ( id > self.pagecount-1) { id = self.pagecount-1; }
				self.curIndex = id;
				self.buildData();
			}
		});

		_container.find(".ex_page_go").unbind("click").bind("click", function(){
			id = parseInt($(".ex_page_go_edit").val())-1;
			if ( id < 0 ){ id = 0; } else if ( id > self.pagecount-1) { id = self.pagecount-1; }
			self.curIndex = id;
			self.buildData();
		});

		// 升序
		_container.find(".arrow_up").unbind("click").bind("click", function(){
			if ($(this).hasClass("active")) {
				return;
			}

			$(".arrow_up").removeClass("active");
			$(".arrow_bottom").removeClass("active");

			var key = $(this).data("type");
			$(this).addClass("active");

			if (self.totaldata <= self.datalist.length) {
				
			}else{
				self.datalist = [];
				self.getData(key, "desc");
				self.curIndex = 0;
				return;
			}

			for (var i = 0; i < self.datalist.length; i++) {
				for(var j = i+1; j < self.datalist.length; j++) {
					var _val1 = self.datalist[i][key];
					var _val2 = self.datalist[j][key];
					if (/^[0-9]+(\.[0-9]+)*$/.test(_val1)) {
						_val1 = parseFloat(_val1);
					}
					if (/^[0-9]+(\.[0-9]+)*$/.test(_val2)) {
						_val2 = parseFloat(_val2);
					}
					if (_val1 > _val2) {
						temp = self.datalist[i];
						self.datalist[i] = self.datalist[j];
						self.datalist[j] = temp;
					}
				}
			}
			self.curIndex = 0;
			self.buildData();
		});

		// 降序
		_container.find(".arrow_bottom").unbind("click").bind("click", function(){
			if ($(this).hasClass("active")) {
				return;
			}
			$(".arrow_up").removeClass("active");
			$(".arrow_bottom").removeClass("active");
			var key = $(this).data("type");
			$(this).addClass("active");

			if (self.totaldata <= self.datalist.length) {
				
			}else{
				self.datalist = [];
				self.getData(key, "asc");
				self.curIndex = 0;
				return;
			}
			
			for (var i = 0; i < self.datalist.length; i++) {
				for(var j = i+1; j < self.datalist.length; j++) {
					var _val1 = self.datalist[i][key];
					var _val2 = self.datalist[j][key];
					if (/^[0-9]+(\.[0-9]+)*$/.test(_val1)) {
						_val1 = parseFloat(_val1);
					}
					if (/^[0-9]+(\.[0-9]+)*$/.test(_val2)) {
						_val2 = parseFloat(_val2);
					}
					if (_val1 < _val2) {
						temp = self.datalist[i];
						self.datalist[i] = self.datalist[j];
						self.datalist[j] = temp;
					}
				}
			}
			self.curIndex = 0;
			self.buildData();
		});
	}

	// 获取指定数据
	this.getDataById = function(idx) {
		return self.datalist[idx];
	}

	this.getParamEx = function(key) {
		return self.param[key];
	}

	this.bindToggleFunc = function(func) {
		this.calltoggle = func;
	}

	this.setEmptyString = function(string) {
		this.empty_string = string;
	}

	// 第一次获取数据1
	self.getData();
}

/* 封装结束 */
function importToExcel(_search, _url, _ex) {
	var _param = {import:1};
	if (_search) {
		_search.find("*").each(function(){
			var _post = $(this).data("post");
			if ( _post && _post != '') {
				if ($(this).hasClass("ex_editor_box") || $(this).hasClass("ex_selector") || $(this).hasClass("ex_date_editor") || $(this).hasClass("ex_search_box")) {
					_param[_post] = $(this).val();

				} else if ($(this).hasClass("ex_radiobox")) {
					if ($(this).hasClass("checked")) {
						_param[_post] = $(this).data("value");
					}
				} else if ($(this).hasClass("ex_checkbox")) {
					if ($(this).hasClass("checked")) {
						if (_param[_post] && _param[_post] != '') {
							_param[_post] += ','+$(this).data("value");
						}else{
							_param[_post] = $(this).data("value");
						}
					}
				}else{
					_param[_post] = $(this).data("value");
				}
			}
		});
	}
	_param = $.extend(_param, _ex);
	post_url = _url;
	for(var k in _param) {
		if (_param[k] && _param[k] != '') {
			post_url += "/"+k+"/"+_param[k];
		}
	}
	window.open(post_url);
}