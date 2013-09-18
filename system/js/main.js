(function(){
	var stat = [];
	stat[0] = stat[1] = stat[2] = stat[3] = stat[4] = 0;
	$('#menu_loved_tb').click(function (){
		if($('#menu_loved_tb').hasClass('selected')) return;
		$('.menu li.selected').removeClass('selected');
		$('#menu_loved_tb').addClass('selected');
		$('.main-content div').addClass('hidden');
		$('#content-loved-tb').removeClass('hidden');
		load_loved_tieba();
		if(mobile) $('.sidebar').fadeOut();
	});
	$('#menu_sign_log').click(function (){
		if($('#menu_sign_log').hasClass('selected')) return;
		$('.menu li.selected').removeClass('selected');
		$('#menu_sign_log').addClass('selected');
		$('.main-content div').addClass('hidden');
		$('#content-sign-log').removeClass('hidden');
		load_sign_log();
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
	$('.reload').click(function (){
		if($('#menu_loved_tb').hasClass('selected')) load_loved_tieba();
		if($('#menu_sign_log').hasClass('selected')) load_sign_log();
		if($('#menu_config').hasClass('selected')) load_setting();
		if(mobile) $('.sidebar').fadeOut();
	});
	function load_loved_tieba(){
		showloading();
		$.getJSON("ajax.php?v=loved-tieba", function(result){
			if(!result) return;
			$('#content-loved-tb table tbody').html('');
			$.each(result, function(i, field){
				$("#content-loved-tb table tbody").append("<tr><td>"+(i+1)+"</td><td><a href=\"http://tieba.baidu.com/f?kw="+field.unicode_name+"\" target=\"_blank\">"+field.name+"</a></td><td><input type=\"checkbox\" value=\""+field.tid+"\""+(field.skiped=='1' ? ' checked' : '')+" class=\"skip_sign\" /></td></tr>");
			});
			$('#content-loved-tb .skip_sign').click(function(){
				showloading();
				this.disabled = 'disabled';
				$.getJSON('index.php?action=skip_tieba&format=json&tid='+this.value+'&formhash='+formhash, function(result){ load_loved_tieba(); }).fail(function() { hideloading(); createWindow().setTitle('系统错误').setContent('发生未知错误: 无法修改当前贴吧设置').addCloseButton('确定').append(); });
				return false;
			});
		}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取喜欢的贴吧列表').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
	}
	function load_sign_log(){
		showloading();
		$.getJSON("ajax.php?v=sign-log", function(result){
			show_sign_log(result);
		}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取签到报告').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
	}
	function load_sign_history(date){
		$('.menu li.selected').removeClass('selected');
		$('.main-content div').addClass('hidden');
		$('#content-sign-log').removeClass('hidden');
		showloading();
		$.getJSON("ajax.php?v=sign-history&date="+date, function(result){
			show_sign_log(result);
		}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取签到报告').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
	}
	function show_sign_log(result){
		stat[0] = stat[1] = stat[2] = stat[3] = stat[4] = 0;
		if(!result || result.count == 0) return;
		$('#content-sign-log table tbody').html('');
		$('#content-sign-log h2').html(result.date+" 签到记录");
		$.each(result.log, function(i, field){
			$("#content-sign-log table tbody").append("<tr><td>"+(i+1)+"</td><td><a href=\"http://tieba.baidu.com/f?kw="+field.unicode_name+"\" target=\"_blank\">"+field.name+"</a></td><td>"+_status(field.status)+"</td><td>"+_exp(field.exp)+"</td></tr>");
		});
		var result_text = "";
		result_text += "共计 "+(stat[0] + stat[1] + stat[2] + stat[3] + stat[4])+" 个贴吧";
		result_text += ", 成功签到 "+(stat[4])+" 个贴吧";
		if(stat[2]) result_text += ", 有 "+(stat[2])+" 个贴吧尚未签到";
		if(stat[0]) result_text += ", 已跳过 "+(stat[0])+" 个贴吧";
		if(stat[3]) result_text += ", "+(stat[3])+" 个贴吧正在等待重试";
		if(stat[1]) result_text += ", "+(stat[1])+" 个贴吧无法签到, <a href=\"index.php?action=reset_failure&formhash="+formhash+"\" onclick=\"return msg_redirect_action(this.href)\">点此重置无法签到的贴吧</a>";
		$('#sign-stat').html(result_text);
		var pager_text = '';
		if(result.before_date) pager_text += '<a href="#history-'+result.before_date+'">&laquo; 前一天</a> &nbsp; ';
		if(!$('#menu_sign_log').hasClass('selected')) pager_text += '<a href="#signlog">今天</a>';
		if(result.after_date) pager_text += ' &nbsp; <a href="#history-'+result.after_date+'">后一天 &raquo;</a>';
		$('#page-flip').html(pager_text);
	}
	function load_setting(){
		showloading();
		$.getJSON("ajax.php?v=get-setting", function(result){
			if(!result) return;
			$('#bdbowser').attr('checked', result.use_bdbowser == "1");
			$('#error_mail').attr('checked', result.error_mail == "1");
			$('#send_mail').attr('checked', result.send_mail == "1");
			$('#zhidao_sign').attr('checked', result.zhidao_sign == "1");
			$('#wenku_sign').attr('checked', result.wenku_sign == "1");
			$('#bdbowser').removeAttr('disabled');
			$('#error_mail').removeAttr('disabled');
			$('#send_mail').removeAttr('disabled');
			$('#zhidao_sign').removeAttr('disabled');
			$('#wenku_sign').removeAttr('disabled');
			if(result.sign_method == 2){
				$('#sign_method_1').attr('checked', false);
				$('#sign_method_2').attr('checked', true);
				$('#sign_method_3').attr('checked', false);
			}else if(result.sign_method == 3){
				$('#sign_method_1').attr('checked', false);
				$('#sign_method_2').attr('checked', false);
				$('#sign_method_3').attr('checked', true);
			}else{
				$('#sign_method_1').attr('checked', true);
				$('#sign_method_2').attr('checked', false);
				$('#sign_method_3').attr('checked', false);
			}
		}).fail(function() { createWindow().setTitle('系统错误').setContent('发生未知错误: 无法获取系统设置').addButton('确定', function(){ location.reload(); }).append(); }).always(function(){ hideloading(); });
	}
	function _status(status){
		if(typeof status == 'undefined') status = 0;
		status = parseInt(status);
		stat[ (status+2) ]++;
		if(mobile){
			switch(status){
				case -2:	return '<img src="style/warn.png" />';
				case -1:	return '<img src="style/error.gif" />';
				case 0:		return '<img src="style/retry.gif" />';
				case 1:		return '<img src="style/warn.png" />';
				case 2:		return '<img src="style/done.gif" />';
			}
		}else{
			switch(status){
				case -2:	return '跳过签到';
				case -1:	return '无法签到';
				case 0:		return '待签到';
				case 1:		return '签到失败';
				case 2:		return '已签到';
			}
		}
	}
	function _exp(exp){
		if(typeof exp == 'undefined') exp = 0;
		return parseInt(exp) == 0 ? '-' : '+'+exp;
	}
	function parse_hash(){
		var hash = location.hash.substring(1);
		if(hash == "loved"){
			$('#menu_loved_tb').click();
		}else if(hash == "signlog"){
			$('#menu_sign_log').click();
		}else if(hash == "setting"){
			$('#menu_config').click();
		}else if(hash.split('-')[0] == "history"){
			load_sign_history(hash.split('-')[1]);
		}else{
			$('#menu_sign_log').click();
		}
	}
	function showloading(){
		$('.loading-icon').removeClass('h');
	}
	function hideloading(){
		$('.loading-icon').addClass('h');
	}
	$('.menu_switch_user a').click(function(){
		var link = this.href;
		createWindow().setTitle('切换账号').setContent('确认要切换登陆账号吗？').addButton('确定', function(){ msg_redirect_action(link); }).addCloseButton('取消').append();
		return false;
	});
	$('.menu_switch_user .del').click(function(){
		var link = this.getAttribute('href');
		createWindow().setTitle('解除绑定').setContent('确认要解除账号绑定吗？').addButton('确定', function(){ msg_redirect_action(link); }).addCloseButton('取消').append();
		return false;
	});
	$('#menu_adduser a').click(function(){
		createWindow().setTitle('绑定账号').setContent('<form method="post" action="member.php?action=bind_user" id="bind_form" onsubmit="return post_win(this.action, this.id)"><input type="hidden" name="formhash" value="'+formhash+'"><p>使用此功能，你可以快速切换在本站注册的多个帐号。</p><p>输入您的用户名/密码即可绑定到本账号。</p><p><label>用户名： <input type="text" name="username" style="width: 200px" /></label></p><p><label>密　码： <input type="password" name="password" style="width: 200px" /></label></p></form>').addButton('确定', function(){ $('#bind_form').submit(); }).addCloseButton('取消').append();
		return false;
	});
	$('#menu_logout').click(function(){
		createWindow().setTitle('退出').setContent('确认要退出登录吗？').addButton('确定', function(){ location.href='member.php?action=logout&hash='+formhash; }).addCloseButton('取消').append();
		return false;
	});
	$('.menubtn').click(function(){
		$('.sidebar').fadeToggle();
	});
	$(window).on('hashchange', function() {
		parse_hash();
	});
	parse_hash();
})();