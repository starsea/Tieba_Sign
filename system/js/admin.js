(function(){
	$('#menu_user')[0].onclick = function (){
		if($('#menu_user').hasClass('selected')) return;
		if($('.menu li.selected')[0]) $('.menu li.selected')[0].className = "";
		$('#menu_user').addClass('selected');
		$('.main-content div').addClass('hidden');
		$('#content-user')[0].className = '';
		load_user();
		if(mobile) $('.sidebar').fadeOut();
	}
	$('#menu_stat')[0].onclick = function (){
		if($('#menu_stat').hasClass('selected')) return;
		if($('.menu li.selected')[0]) $('.menu li.selected')[0].className = "";
		$('#menu_stat').addClass('selected');
		$('.main-content div').addClass('hidden');
		$('#content-stat')[0].className = '';
		load_userstat();
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
	function load_user(){
		$('.loading-icon').finish();
		$('.loading-icon').fadeIn();
		$.getJSON("admin.php?action=load_user", function(result){
			$('.loading-icon').finish();
			$('.loading-icon').fadeOut();
			if(!result) return;
			$('#content-user table tbody')[0].innerHTML = '';
			$.each(result, function(i, field){
				$("#content-user table tbody").append("<tr><td>"+field.uid+"</td><td>"+field.username+"</td><td>"+field.email+"</td><td><a href=\"admin.php?action=update_liked_tieba&uid="+field.uid+"&formhash="+formhash+"\">刷新喜欢的贴吧</a> | <a href=\"admin.php?action=deluser&uid="+field.uid+"&formhash="+formhash+"\">删除用户</a></td></tr>");
			});
		});
	}
	function load_userstat(){
		$('.loading-icon').finish();
		$('.loading-icon').fadeIn();
		$.getJSON("admin.php?action=load_userstat", function(result){
			$('.loading-icon').finish();
			$('.loading-icon').fadeOut();
			if(!result) return;
			$('#content-stat table tbody')[0].innerHTML = '';
			$.each(result, function(i, field){
				if(parseInt(field.unsupport) > 0) field.unsupport += ' (<a href="admin.php?action=reset_failure&uid='+field.uid+'&formhash='+formhash+'">重置</a>)';
				$("#content-stat table tbody").append("<tr><td>"+field.uid+"</td><td>"+field.username+"</td><td>"+field.succeed+"</td><td>"+field.waiting+"</td><td>"+field.retry+"</td><td>"+field.unsupport+"</td></tr>");
			});
		});
	}
	function load_setting(){
		$('.loading-icon').finish();
		$('.loading-icon').fadeIn();
		$.getJSON("admin.php?action=load_setting", function(result){
			$('.loading-icon').finish();
			$('.loading-icon').fadeOut();
			if(!result) return;
			$('#autoupdate')[0].checked = result.autoupdate ? 'checked' : '';
			$('#block_register')[0].checked = result.block_register ? 'checked' : '';
			$('#invite_code')[0].value = result.invite_code ? result.invite_code : '';
		});
	}
	function parse_hash(){
		var hash = location.hash.substring(1);
		if(hash == "user"){
			$('#menu_user')[0].onclick();
		}else if(hash == "stat"){
			$('#menu_stat')[0].onclick();
		}else if(hash == "config"){
			$('#menu_config')[0].onclick();
		}else{
			$('#menu_user')[0].onclick();
		}
	}
	$('.menubtn').click(function(){
		$('.sidebar').fadeToggle();
	});
	$(window).on('hashchange', function() {
		parse_hash();
	});
	parse_hash();
})();