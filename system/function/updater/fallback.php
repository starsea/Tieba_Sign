<?php
if(!defined('IN_KKFRAME')) exit('Access Denied');
$usr = DB::fetch_first("SELECT * FROM member LIMIT 0,1");
$member_field = array_keys($usr);
if(!in_array('sign_method', $member_field)){
	include SYSTEM_ROOT.'./function/updater/1.0.php';
}else{
	include SYSTEM_ROOT.'./function/updater/1.13.8.18.php';
}
error::system_error('自动更新“贴吧签到助手”失败，请尝试手动安装');
?>