$(document).ready(function() {
	$('#menu>li').click(function (){
		if(!$(this).attr('id')) return;
		if($(this).attr('id') == 'menu_updater') return;
		if($(this).hasClass('selected')) return;
		$('.menu li.selected').removeClass('selected');
		$(this).addClass('selected');
		var content_id = $(this).attr('id').replace('menu_', '#content-');
		$('.main-content>div').addClass('hidden');
		$(content_id).removeClass('hidden');
		var callback = $(this).attr('id').replace('menu_', 'load_');
		eval('if (typeof '+callback+' == "function") '+callback+'(); ');
		if(mobile) $('.sidebar').fadeOut();
	});
	$('#mail_advanced_config').click(function(){
		post_win($('#mail_setting').attr('action'), 'mail_setting', function(){
			showloading();
			$.getJSON("admin.php?action=mail_advanced", function(result){
				if(!result) return;
				var content = '';
				for(var i=0; i<result.length; i++){
					content += '<p>'+result[i].name+':'+(result[i].description ? ' ('+result[i].description+')' : '')+'</p><p>';
					content += '<input type="'+result[i].type+'" name="'+result[i].key+'" value="'+result[i].value+'" style="width: 95%" />';
					content += '</p>';
				}
				createWindow().setTitle('邮件高级设置').setContent('<form method="post" action="admin.php?action=mail_advanced" id="advanced_mail_config" onsubmit="return post_win(this.action, this.id)"><input type="hidden" name="formhash" value="'+formhash+'">'+content+'</form>').addButton('确定', function(){ $('#advanced_mail_config').submit(); }).addCloseButton('取消').append();
			}).fail(function() { createWindow().setTitle('邮件高级设置').setContent('发生未知错误: 无法打开高级设置面板').addCloseButton('确定').append(); }).always(function(){ hideloading(); });
		}, true);
		return false;
	});
	$('.link_config').click(function(){
		var link = this.href;
		showloading();
		$.getJSON(link, function(result){
			createWindow().setTitle('插件设置').setContent('<form method="post" action="'+link+'" id="plugin_config" onsubmit="return post_win(this.action, this.id)"><input type="hidden" name="formhash" value="'+formhash+'">'+result.html+'</form>').addButton('确定', function(){ $('#plugin_config').submit(); }).addCloseButton('取消').append();
		}).fail(function() { createWindow().setTitle('插件设置').setContent('发生未知错误: 无法打开插件设置面板').addCloseButton('确定').append(); }).always(function(){ hideloading(); });
		return false;
	});
	$('.menubtn').click(function(){
		$('.sidebar').fadeToggle();
	});
	$('.link_install').click(function(){
		var link = this.href;
		createWindow().setTitle('安装插件').setContent('确定要安装这个插件吗？').addButton('确定', function(){ msg_redirect_action(link); }).addCloseButton('取消').append();
		return false;
	});
	$('.link_uninstall').click(function(){
		var link = this.href;
		createWindow().setTitle('卸载插件').setContent('确定要卸载这个插件吗？').addButton('确定', function(){ msg_redirect_action(link); }).addCloseButton('取消').append();
		return false;
	});
	$(window).on('hashchange', function() {
		parse_hash();
	});
	hideloading();
	while(location.hash.lastIndexOf('#') > 0) location.hash = location.hash.substring(0, location.hash.lastIndexOf('#'));
	parse_hash();
});

function load_user(){
	showloading();
	$.getJSON("admin.php?action=load_user", function(result){
		if(!result) return;
		$('#content-user table tbody').html('');
		$.each(result, function(i, field){
			$("#content-user table tbody").append("<tr><td>"+field.uid+"</td><td>"+field.username+"</td><td>"+field.email+"</td><td><a href=\"admin.php?action=update_liked_tieba&uid="+field.uid+"&formhash="+formhash+"\" onclick=\"return msg_win_action(this.href)\">刷新喜欢的贴吧</a> | <a href=\"javascript:;\" onclick=\"return deluser('"+field.uid+"')\">删除用户</a></td></tr>");
		});
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取用户列表').addCloseButton('确定').append(); }).always(function(){ hideloading(); });
}
function load_stat(){
	showloading();
	$.getJSON("admin.php?action=load_userstat", function(result){
		if(!result) return;
		$('#content-stat table tbody').html('');
		$.each(result, function(i, field){
			if(parseInt(field.unsupport) > 0) field.unsupport += ' (<a href="admin.php?action=reset_failure&uid='+field.uid+'&formhash='+formhash+'" onclick="return msg_win_action(this.href)">重置</a>)';
			$("#content-stat table tbody").append("<tr><td>"+field.uid+"</td><td>"+field.username+"</td><td>"+field.succeed+"</td><td>"+field.skiped+"</td><td>"+field.waiting+"</td><td>"+field.retry+"</td><td>"+field.unsupport+"</td></tr>");
		});
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取用户统计数据').addCloseButton('确定').append(); }).always(function(){ hideloading(); });
}
function load_setting(){
	showloading();
	$.getJSON("admin.php?action=load_setting", function(result){
		if(!result) return;
		$('#autoupdate').attr('checked', result.autoupdate == 1);
		$('#block_register').attr('checked', result.block_register == 1);
		$('#invite_code').attr('value', result.invite_code ? result.invite_code : '');
	}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取当前系统设置').addCloseButton('确定').append(); }).always(function(){ hideloading(); });
}
function parse_hash(){
	var hash = location.hash.substring(1);
	if(hash.indexOf('#') >= 0){
		location.href = location.href.substring(0, location.href.lastIndexOf('#'));
		location.reload();
		return;
	}
	if(hash == "user"){
		$('#menu_user').click();
	}else if(hash == "stat"){
		$('#menu_stat').click();
	}else if(hash == "setting"){
		$('#menu_setting').click();
	}else if(hash == "mail"){
		$('#menu_mail').click();
	}else if(hash == "plugin"){
		$('#menu_plugin').click();
	}else{
		$('#menu_user').click();
	}
}
function deluser(uid){
	createWindow().setTitle('删除用户').setContent('确认要删除该用户吗？').addButton('确定', function(){ msg_win_action("admin.php?action=deluser&uid="+uid+"&formhash="+formhash); }).addCloseButton('取消').append();
	return false;
}
