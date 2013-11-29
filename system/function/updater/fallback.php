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
throw new Exception('找不到更新程序，无法进行更新！');
?>