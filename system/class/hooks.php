<?php
if(!defined('IN_KKFRAME')) exit();

class HOOK{
	function refresh_dropin(){
		global $_HOOKS;
		$_HOOKS['script'] = array();
		$dir = opendir(ROOT.'./plugins');
		while (false !== ($filename = readdir($dir))) {
			if(substr($filename, -4, 4) != '.php') continue;
			$_HOOKS['script'][] = $filename;
		}
		$_HOOKS['refresh_time'] = TIMESTAMP;
		CACHE::save('plugin', $_HOOKS);
		saveSetting('plugin_refresh', TIMESTAMP);
	}
	function INIT(){
		global $_HOOKS;
		$_HOOKS = CACHE::get('plugin');
		if($_HOOKS['refresh_time'] < TIMESTAMP - 300) self::refresh_dropin();
		if(is_array($_HOOKS['script'])){
			foreach($_HOOKS['script'] as $file){
				if(!file_exists(ROOT.'./plugins/'.$file)){
					self::refresh_dropin();
					continue;
				}
				@include ROOT.'./plugins/'.$file;
			}
		}
	}
	function register($hookname, $parameter){
		global $_REGISTED_HOOKS;
		$_REGISTED_HOOKS[$hookname][] = $parameter;
	}
	function run($hookname){
		global $_REGISTED_HOOKS;
		$hooks = $_REGISTED_HOOKS[$hookname];
		if(!$hooks) return;
		$args = func_get_args();
		foreach($hooks as $hook){
			@call_user_func($hook, $args);
		}
	}
}