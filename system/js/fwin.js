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
			win.destroy();
		}
		this.btns.appendChild(btn);
		return this;
	}
	win.addCloseButton = function(title){
		var btn = document.createElement('button');
		btn.innerHTML = title;
		btn.onclick = function(){
			win.destroy();
		}
		this.btns.appendChild(btn);
		return this;
	}
	win.append = function(){
		if (this.allow_close) {
			var closebtn = document.createElement('span');
			closebtn.className = 'close';
			closebtn.innerText = 'x';
			closebtn.onclick = function(){ win.destroy(); }
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
	win.destroy = function(){
		$('#append_parent')[0].removeChild(win.obj);
	}
	return win;
}
function msg_win_action(link){
	link += link.indexOf('?') < 0 ? '?' : '&';
	link += "format=json";
	$('.loading-icon').finish();
	$('.loading-icon').fadeIn();
	$.getJSON(link, function(result){
		$('.loading-icon').finish();
		$('.loading-icon').fadeOut();
		createWindow().setTitle('系统消息').setContent(result.msg).addCloseButton('确定').append();
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法解析返回结果').addCloseButton('确定').append(); });
	return false;
}
function msg_redirect_action(link){
	link += link.indexOf('?') < 0 ? '?' : '&';
	link += "format=json";
	$('.loading-icon').finish();
	$('.loading-icon').fadeIn();
	$.getJSON(link, function(result){
		$('.loading-icon').finish();
		$('.loading-icon').fadeOut();
		return;
		createWindow().setTitle('系统消息').setContent(result.msg).addButton('确定', function(){ location.href = result.redirect; }).append();
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法解析返回结果').addCloseButton('确定').append(); });
	return false;
}