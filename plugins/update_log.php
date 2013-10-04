<?php
if(!defined('IN_KKFRAME')) exit();
function create_updatelog_menu(){
	echo '<li id="menu_update_log"><a href="#update_log">更新公告</a></li>';
}
function updatelog_js(){
	echo <<<EOF
<script type="text/javascript">
$('#menu_update_log').click(function (){
	if($('#menu_update_log').hasClass('selected')) return;
	$('.menu li.selected').removeClass('selected');
	$('#menu_update_log').addClass('selected');
	$('.main-content div').addClass('hidden');
	$('#content-update_log').removeClass('hidden');
	if(mobile) $('.sidebar').fadeOut();
	hideloading();
});
</script>
EOF;
}
function create_updatelog_tabs(){
	echo <<<EOF
<div id="content-update_log" class="hidden">
<h2>更新公告</h2>
<!-- 从服务器上获取公告 -->
<script src="http://sign.ikk.me/update_log.js"></script>
</div>
EOF;
}
HOOK::register('main_menu', 'create_updatelog_menu');
HOOK::register('page_footer_js', 'updatelog_js');
HOOK::register('tabs', 'create_updatelog_tabs');
?>