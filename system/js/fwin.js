function createWindow(){
	var win = new Object();
	win.obj = document.createElement('div');
	win.obj.className = 'fwin';
	win.title = '提示信息';
	win.content = 'null';
	win.btns = document.createElement('p');
	win.btns.className = 'btns';
	win.allow_close = true;
	win.setTitle = function(str){
		this.title = str;
		return this;
	}
	win.setContent = function(str){
		this.content = str;
		return this;
	}
	win.addButton = function(title, callback){
		var btn = document.createElement('button');
		btn.innerHTML = title;
		btn.onclick = function(){
			callback();
			win.close();
		}
		this.btns.appendChild(btn);
		return this;
	}
	win.addCloseButton = function(title){
		var btn = document.createElement('button');
		btn.innerHTML = title;
		btn.onclick = function(){
			win.close();
		}
		this.btns.appendChild(btn);
		return this;
	}
	win.append = function(){
		if (this.allow_close) {
			var closebtn = document.createElement('span');
			closebtn.className = 'close';
			closebtn.innerText = 'x';
			closebtn.onclick = function(){ 
				win.close();
			};
			this.obj.appendChild(closebtn);
		}
		var win_title = document.createElement('h3');
		win_title.innerHTML = this.title;
		this.obj.appendChild(win_title);
		var win_content = document.createElement('div');
		win_content.className = 'fcontent';
		win_content.innerHTML = this.content;
		if (this.btns.innerHTML) {
			win_content.appendChild(this.btns);
		}
		this.obj.appendChild(win_content);
		$('#append_parent')[0].appendChild(this.obj);
		return false;
	}
	win.close = function(){
		win.obj.className = 'fwin h';
		setTimeout(function(){ $('#append_parent')[0].removeChild(win.obj); }, 1000);
	}
	return win;
}
function msg_win_action(link){
	link += link.indexOf('?') < 0 ? '?' : '&';
	link += "format=json";
	showloading();
	$.getJSON(link, function(result){
		createWindow().setTitle('系统消息').setContent(result.msg).addCloseButton('确定').append();
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法解析返回结果').addCloseButton('确定').append(); }).always(function(){ hideloading(); });
	return false;
}
function msg_redirect_action(link){
	link += link.indexOf('?') < 0 ? '?' : '&';
	link += "format=json";
	showloading();
	$.getJSON(link, function(result){
		createWindow().setTitle('系统消息').setContent(result.msg).addButton('确定', function(){ location.href = result.redirect; }).append();
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法解析返回结果').addCloseButton('确定').append(); }).always(function(){ hideloading(); });
	return false;
}
function showloading(){
	$('.loading-icon')[0].className = 'loading-icon';
}
function hideloading(){
	$('.loading-icon')[0].className = 'loading-icon h';
}
function show_updater_win(_url){
	$.ajax({
		type: "get",
		async: false,
		url: _url,
		dataType: "jsonp",
		jsonp: "callback",
		jsonpCallback: "handleNewVersion",
		success: function(json){
			if(json.new){
				createWindow().setTitle('检查更新').setContent('<p>发现新版本'+json.ver+'！</p><p>要查看更新说明吗？</p>').addButton('确定', function(){ window.open(json.url); }).addCloseButton('取消').append();
			}else{
				createWindow().setTitle('检查更新').setContent('您当前使用的是最新版本').addCloseButton('确定').append();
			}
		},
		error: function(){
			createWindow().setTitle('检查更新').setContent('检查更新过程出现错误').addCloseButton('确定').append();
		}
	});
	return false;
}