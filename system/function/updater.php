<?php
if(!defined('IN_KKFRAME')) exit('Access Denied');
function check_update(){
	if(defined('UPDATE_CHECKED')) return;
	$current_version = getSetting('version', true);
	if ($current_version != VERSION){
		// load update script
		while($current_version){
			$filepath = SYSTEM_ROOT."./function/updater/{$current_version}.php";
			if(file_exists($filepath)){
				include $filepath;
				exit();
			} else{
				$current_version = substr($current_version, 0, strrpos($current_version, '.'));
			}
		}
		include SYSTEM_ROOT.'./function/updater/fallback.php';
		exit();
	} else{
		define('UPDATE_CHECKED', true);
		return;
	}
}
?>