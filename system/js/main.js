(function(){
	var stat = [];
	stat[0] = stat[1] = stat[2] = stat[3] = 0;
	$('#menu_loved_tb')[0].onclick = function (){
		if($('#menu_loved_tb').hasClass('selected')) return;
		if($('.menu li.selected')[0]) $('.menu li.selected')[0].className = "";
		$('#menu_loved_tb').addClass('selected');
		$('.main-content div').addClass('hidden');
		$('#content-loved-tb')[0].className = '';
		load_loved_tieba();
		if(mobile) $('.sidebar').fadeOut();
	}
	$('#menu_sign_log')[0].onclick = function (){
		if($('#menu_sign_log').hasClass('selected')) return;
		if($('.menu li.selected')[0]) $('.menu li.selected')[0].className = "";
		$('#menu_sign_log').addClass('selected');
		$('.main-content div').addClass('hidden');
		$('#content-sign-log')[0].className = '';
		load_sign_log();
		if(mobile) $('.sidebar').fadeOut();
	}
	$('#menu_config')[0].onclick = function (){
		if($('#menu_config').hasClass('selected')) return;
		if($('.menu li.selected')[0]) $('.menu li.selected')[0].className = "";
		$('#menu_config').addClass('selected');
		$('.main-content div').addClass('hidden');
		$('#content-config')[0].className = '';
		load_setting();
		if(mobile) $('.sidebar').fadeOut();
	}
	$('.reload')[0].onclick = function (){
		if($('#menu_loved_tb').hasClass('selected')) load_loved_tieba();
		if($('#menu_sign_log').hasClass('selected')) load_sign_log();
		if($('#menu_config').hasClass('selected')) load_setting();
		if(mobile) $('.sidebar').fadeOut();
	}
	function load_loved_tieba(){
		$('.loading-icon').fadeIn();
		$.getJSON("ajax.php?v=loved-tieba", function(result){
			$('.loading-icon').finish();
			$('.loading-icon').fadeOut();
			if(!result) return;
			$('#content-loved-tb table tbody')[0].innerHTML = '';
			$.each(result, function(i, field){
				$("#content-loved-tb table tbody").append("<tr><td>"+(i+1)+"</td><td><a href=\"http://tieba.baidu.com/f?kw="+field.unicode_name+"\" target=\"_blank\">"+field.name+"</a></td></tr>");
			});
		});
	}
	function load_sign_log(){
		$('.loading-icon').fadeIn();
		$.getJSON("ajax.php?v=sign-log", function(result){
			show_sign_log(result);
		});
	}
	function load_sign_history(date){
		if($('.menu li.selected')[0]) $('.menu li.selected')[0].className = "";
		$('.main-content div').addClass('hidden');
		$('#content-sign-log')[0].className = '';
		$('.loading-icon').fadeIn();
		$.getJSON("ajax.php?v=sign-history&date="+date, function(result){
			show_sign_log(result);
		});
	}
	function show_sign_log(result){
		stat[0] = stat[1] = stat[2] = stat[3] = 0;
		$('.loading-icon').finish();
		$('.loading-icon').fadeOut();
		if(!result || result.count == 0) return;
		$('#content-sign-log table tbody')[0].innerHTML = '';
		$('#content-sign-log h2')[0].innerHTML = result.date+" 签到记录";
		$.each(result.log, function(i, field){
			$("#content-sign-log table tbody").append("<tr><td>"+(i+1)+"</td><td><a href=\"http://tieba.baidu.com/f?kw="+field.unicode_name+"\" target=\"_blank\">"+field.name+"</a></td><td>"+_status(field.status)+"</td><td>"+_exp(field.exp)+"</td></tr>");
		});
		var result_text = "";
		result_text += "共计 "+(stat[0] + stat[1] + stat[2] + stat[3])+" 个贴吧";
		result_text += ", 成功签到 "+(stat[3])+" 个贴吧";
		if(stat[1]) result_text += ", 有 "+(stat[1])+" 个贴吧尚未签到";
		if(stat[2]) result_text += ", "+(stat[2])+" 个贴吧正在等待重试";
		if(stat[0]) result_text += ", "+(stat[0])+" 个贴吧无法签到, <a href=\"index.php?action=reset_failure\">点此重置无法签到的贴吧</a>";
		$('#sign-stat').html(result_text);
		var pager_text = '';
		if(result.before_date) pager_text += '<a href="#history-'+result.before_date+'">&laquo; 前一天</a> &nbsp; ';
		if(!$('#menu_sign_log').hasClass('selected')) pager_text += '<a href="#signlog">今天</a>';
		if(result.after_date) pager_text += ' &nbsp; <a href="#history-'+result.after_date+'">后一天 &raquo;</a>';
		$('#page-flip').html(pager_text);
	}
	function load_setting(){
		$('.loading-icon').fadeIn();
		$('#bdbowser')[0].disabled = true;
		$('#error_mail')[0].disabled = true;
		$('#send_mail')[0].disabled = true;
		$.getJSON("ajax.php?v=get-setting", function(result){
			$('.loading-icon').finish();
			$('.loading-icon').fadeOut();
			if(!result) return;
			$('#bdbowser')[0].checked = result.use_bdbowser == "1";
			$('#error_mail')[0].checked = result.error_mail == "1";
			$('#send_mail')[0].checked = result.send_mail == "1";
			$('#bdbowser')[0].disabled = false;
			$('#error_mail')[0].disabled = false;
			$('#send_mail')[0].disabled = false;
			if(result.sign_method == 2){
				$('#sign_method_1')[0].checked = false;
				$('#sign_method_2')[0].checked = true;
			}else{
				$('#sign_method_1')[0].checked = true;
				$('#sign_method_2')[0].checked = false;
			}
		});
	}
	function _status(status){
		if(typeof status == 'undefined') status = 0;
		status = parseInt(status);
		stat[ (status+1) ]++;
		if(mobile){
			switch(status){
				case -1:	return '<img src="style/error.gif" />';
				case 0:		return '<img src="style/retry.gif" />';
				case 1:		return '<img src="style/warn.png" />';
				case 2:		return '<img src="style/done.gif" />';
			}
		}else{
			switch(status){
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
			$('#menu_loved_tb')[0].onclick();
		}else if(hash == "signlog"){
			$('#menu_sign_log')[0].onclick();
		}else if(hash == "setting"){
			$('#menu_config')[0].onclick();
		}else if(hash.split('-')[0] == "history"){
			load_sign_history(hash.split('-')[1]);
		}else{
			$('#menu_sign_log')[0].onclick();
		}
	}
	$('#menu_logout')[0].onclick = function(){
		if(!confirm('确认要退出登录吗？')) return false;
	}
	$('.menubtn').click(function(){
		$('.sidebar').fadeToggle();
	});
	$(window).on('hashchange', function() {
		parse_hash();
	});
	parse_hash();
})();