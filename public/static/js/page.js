// 分页表格实现
// @_obj: 容器指针
// @head: 表头html代码
// @body: 单个tr的格式
// @url: 获取数据url
// @param: 获取数据参数,
// @_count: 每页数据的数量,不传默认为20条
// @sucfunc: 事件绑定函数
function pageTable(_obj,url,param,head,body,sucfunc,sucpre,_count){
	this.params = {}; // 请求数据
	this.url = ''; // 请求地址
	this.pageIndex = 1; // 第一次请求
	this.pageNum = 20; // 每页显示10条数据
	this.requestNum = 100; // 每次求情一百条，  把一百条数据进行分页
	this.current = 1; // 当前页码
	this.count = 0; // 总页码数
	this.total = 0; // 总记录数
	this.num = 10; // 默认显示10个页码
	this.prevIndex = 0; // 上一页的索引
	this.nextIndex = 0; // 下一页的索引
	this.pageObj = ''; // 分页列表对象
	this.pageStr = '';
	this.pageList = {}; // 所有的分页数据
	this.head = null; // 头部
	this.body = null; // 列表
	this.firstRequest = 1; // 第一次请求
	var self = this;
	this.pageFunc = function(pageid){
		self.current = pageid;
		self.prevIndex = self.current - 1;
		self.nextIndex = self.current + 1;

		if (self.count <= 1) {
			return;
		}
		self.pageStr = "<ul class='pagination'>";
		// 上一页 首页等
		if (self.count > self.num) {
			if(self.current > 1){
				self.pageStr += '<li><a class="first">首页</a></li>';
				self.pageStr += '<li><a class="prev">上一页</a></li>';
			}else{
				self.pageStr += '<li class="disabled" ><span  style="color: #ddd">首页</span></li>';
				self.pageStr += '<li class="disabled"><span style="color: #ddd">上一页</span></li>';
			}
		}
		var offset = Math.floor(self.num / 2);
		if(offset % 2 == 0){
			var start = pageid - offset;
		}else{
			var start = pageid - offset + 1;
		}
		var end = pageid + offset;
		if(start < 1){
			start = 1;
			end = self.num;
		}
		if(end >= self.count){
			end = self.count;
			start = end - self.num + 1;
			if(start < 1){
				start = 1;
			}
		}
		for(var i = start; i <= end; i++ ){
			if (i == self.current) {
				self.pageStr += '<li class="active"><span>'+i+'</span></li>';
			}else{
				self.pageStr += '<li><a data-page="'+i+'">'+i+'</a></li>';
			}
		}
		if(end < self.count){
			self.pageStr += '<li class="active"><span>....</span></li>';
			self.pageStr += '<li><a data-page="'+self.count+'">'+self.count+'</a></li>';
		}
		// 下一页 
		if (self.count > self.num) {
			if (self.current < self.count) {
				self.pageStr += '<li><a class="next">下一页</a></li>';
				self.pageStr += '<li><a class="end">尾页</a></li>';
			}else{
				self.pageStr += '<li class="disabled"><span>下一页</span></li>';
			}
		}
		//总计

        self.pageStr += "总计 <b style='color: red'>"+this.count +"</b> 页：共  <b style='color: red'>"+this.total+"</b> 条数据  当前显示  <b style='color: red'>"+((this.current-1)*this.pageNum+1)+" </b>  to  <b style='color: red'>"+this.current*this.pageNum+"</b>";

		self.pageStr += "</ul>";
		self.pageObj.find('.pagination').html(self.pageStr);

		// 分页的点击事件
		self.pageEvent();
	}

	this.pageEvent = function(){
		self.pageObj.find('.pagination li>a').click(function(){
			var c = $(this).attr('class')
			if(c == 'first'){
				// 第一页
				self.showList(1);
			}else if(c == 'prev'){
				self.showList(self.prevIndex);
			}else if(c == 'next'){
				self.showList(self.nextIndex);
			}else if(c == 'end'){
				self.showList(self.count);
			}else{
				// 普通的页码
				var pageid = $(this).data('page');
				self.showList(pageid);
			}
		})
	}

	this.funcBefore = function(data){
		// 有列表数据
		if (sucpre && typeof(sucpre) == 'function') {
			data = sucpre(data);
		}
		return data;
	}
	this.funcAfter = function(data){
		data = self;
		// 有列表数据
		if (sucfunc && typeof(sucfunc) == 'function') {
			data = sucfunc(data);
		}
		return data;
	}

	// 参数初始化
	this.initParam = function(){
		self.head = head;
		if(_count != undefined){
            self.pageNum = _count;
		}
		if(self.pageNum >= 30 && self.pageNum < 50){
            self.requestNum = self.pageNum * 3;
		}else if(self.pageNum >= 50 && self.pageNum < 100){
            self.requestNum = self.pageNum * 2;
		}else if(self.pageNum >= 100){
            self.requestNum = self.pageNum;
		}else{
            self.requestNum = self.pageNum * 5;
		}
		if (_obj.length <= 0) {
			tipshow('请输入分页列表对象');return;
		}else{
			self.pageObj = _obj;
		}
		if (url) {
			if (typeof(url) != 'string') {
				tipshow('请求地址必须是字符串');return;
			}
			self.url = url;
		}else{
			tipshow('请输入请求地址');return;
		}
		if (typeof(param) == 'object' && param) {
			for(var i in param){
				self.params[i] = param[i];
			}
		}
	}

	this.showList = function(pageid){
		if(pageid > self.count){
			return;
		}
		// 根据id去解析是第几次请求
		var reqNum = Math.ceil(pageid / (self.requestNum / self.pageNum));
		if(self.pageList[reqNum] == undefined){
			self.current = pageid;
			self.pageIndex = reqNum;
			self.initData();return;
		}
		_pageid = pageid - (reqNum-1) * (self.requestNum / self.pageNum); // 第几次请求的开始页码

		var _start = (_pageid - 1) * self.pageNum ; 

		var j = 0;
		self.pageObj.find('table>tbody.list').html('');
		for(var i = _start; i < self.pageList[reqNum].length ; i++){
			if(j == self.pageNum){
				break;
			}
            self.pageList[reqNum][i]['_idx'] = i;
			// 显示页码
			self.pageObj.find('table>tbody.list').append(body.format(self.pageList[reqNum][i]));
			j++;
		}
		self.pageFunc(pageid);
		self.funcAfter();
	}

	this.getDataById = function(idx){
        var reqNum = Math.ceil(self.current / (self.requestNum / self.num));
		if(empty(self.pageList) || empty(self.pageList[reqNum])){
			return null;
		}
		for(var i in self.pageList[reqNum]){
			if(i == idx){
				return self.pageList[reqNum][i];
			}
		}
		return null;
	}

	this.initData = function(){
		self.initParam();
		self.params['_method'] = 'PUT';
		self.params['limit'] = self.requestNum;
		self.params['pageid'] = self.pageIndex;
        self.params['firstRequest'] = self.firstRequest;
		AjaxPost(self.url,self.params,function(obj){
			self.pageObj.html('<table class="ex_custom_table"><thead>'+self.head+'</thead><tbody class="list"></tbody></table><div class="pagination"></div>');
			if (!empty(obj.data.list)) {
				if(!empty(obj.data.param)){  self.param=obj.data.param}

				var data = obj.data.list;
				if(self.pageIndex === 1){
					if(data.length < 100){
						self.total = data.length;
						self.count = Math.ceil(data.length / self.pageNum);
					}else{
						if(obj.data.pageinfo){
							self.total = obj.data.pageinfo;
							self.count = Math.ceil(obj.data.pageinfo / self.pageNum);
						}else{
							self.total = data.length;
							self.count = Math.ceil(data.length / self.pageNum);
						}
					}
				}
                self.firstRequest = 2; // 设置成不是第一次请求
				data = self.funcBefore(data);
				self.pageList[self.pageIndex] = data;

				self.showList(self.current);
				self.funcAfter(self);
				
			}else{
                console.log("没有数据")
				// 没有列表数据
				self.pageObj.find('table>tbody.list').html('<tr><td colspan="100%" style="text-align:center">没有任何数据</td></tr>');
			}
		})
	}
	this.initData();
}