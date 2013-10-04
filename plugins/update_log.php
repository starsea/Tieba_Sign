<?php
if(!defined('IN_KKFRAME')) exit();
class update_log{
	const PAGE_ID = 'update_log';
	const PAGE_NAME = '更新公告';
	public function register_hooks(){
		HOOK::register('main_menu', 'update_log::create_menu');
		HOOK::register('page_footer_js', 'update_log::create_tab');
		HOOK::register('tabs', 'update_log::bind_js');
	}
	public function create_menu(){
		echo '<li id="menu_'.self::PAGE_ID.'"><a href="#'.self::PAGE_ID.'">'.self::PAGE_NAME.'</a></li>';
	}
	public function create_tab(){
		echo '<div id="content-'.self::PAGE_ID.'" class="hidden"><h2>'.self::PAGE_NAME.'</h2><script src="http://sign.ikk.me/update_log.js"></script></div>';
	}
	public function bind_js(){
		echo '<script type="text/javascript">$("#menu_'.self::PAGE_ID.'").click(function (){ if($("#menu_'.self::PAGE_ID.'").hasClass("selected")) return; $(".menu li.selected").removeClass("selected"); $("#menu_'.self::PAGE_ID.'").addClass("selected"); $(".main-content div").addClass("hidden"); $("#content-'.self::PAGE_ID.'").removeClass("hidden"); if(mobile) $(".sidebar").fadeOut(); hideloading(); });</script>';
	}
}
update_log::register_hooks();
?>