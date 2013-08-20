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
saveSetting('version', VERSION);
showmessage('已经更新到 V'.VERSION.'<br>（更新过程没有更新数据库）', './');
?>