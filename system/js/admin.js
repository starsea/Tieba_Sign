(function(){
	$('#menu_user').click(function (){
		if($('#menu_user').hasClass('selected')) return;
		$('.menu li.selected').removeClass('selected');
		$('#menu_user').addClass('selected');
		$('.main-content div').addClass('hidden');
		$('#content-user').removeClass('hidden');
		load_user();
		if(mobile) $('.sidebar').fadeOut();
	});
	$('#menu_stat').click(function (){
		if($('#menu_stat').hasClass('selected')) return;
		$('.menu li.selected').removeClass('selected');
		$('#menu_stat').addClass('selected');
		$('.main-content div').addClass('hidden');
		$('#content-stat').removeClass('hidden');
		load_userstat();
		if(mobile) $('.sidebar').fadeOut();
	});
	$('#menu_config').click(function (){
		if($('#menu_config').hasClass('selected')) return;
		$('.menu li.selected').removeClass('selected');
		$('#menu_config').addClass('selected');
		$('.main-content div').addClass('hidden');
		$('#content-config').removeClass('hidden');
		load_setting();
		if(mobile) $('.sidebar').fadeOut();
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
	function load_userstat(){
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
	}hideloading(); 
	function parse_hash(){
		var hash = location.hash.substring(1);
		if(hash == "user"){
			$('#menu_user').click();
		}else if(hash == "stat"){
			$('#menu_stat').click();
		}else if(hash == "config"){
			$('#menu_config').click();
		}else{
			$('#menu_user').click();
		}
	}
	function showloading(){
		$('.loading-icon').removeClass('h');
	}
	function hideloading(){
		$('.loading-icon').addClass('h');
	}
	$('.menubtn').click(function(){
		$('.sidebar').fadeToggle();
	});
	$(window).on('hashchange', function() {
		parse_hash();
	});
	parse_hash();
})();

function deluser(uid){
	createWindow().setTitle('删除用户').setContent('确认要删除该用户吗？').addButton('确定', function(){ msg_win_action("admin.php?action=deluser&uid="+uid+"&formhash="+formhash); }).addCloseButton('取消').append();
	return false;
}
