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
		btn.onclick = callback;
		this.btns.appendChild(btn);
		return this;
	}
	win.addCloseButton = function(title){
		return this.addButton(title, function(){ win.destroy(); });
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