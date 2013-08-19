<?php
if(!defined('IN_KKFRAME')) exit('Access Denied');
$query = DB::query('SHOW TABLES');
$tables = array();
while($table = DB::fetch($query)){
	$tables[] = implode('', $table);
}

if(!in_array('member', $tables)){
	include SYSTEM_ROOT.'./function/updater/install.php';
}elseif(!in_array('setting', $tables)){
	include SYSTEM_ROOT.'./function/updater/1.0.php';
}elseif(!in_array('member_setting', $tables)){
	include SYSTEM_ROOT.'./function/updater/1.13.8.18.php';
}
error::system_error('自动更新“贴吧签到助手”失败，请尝试手动安装');
?>