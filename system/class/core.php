<?php
if(!defined('IN_KKFRAME')) exit();

class kk_sign{
	var $system_modules = array('db', 'cache', 'debug', 'error');
	var $modules = array('updater', 'hooks', 'mail');
	function kk_sign($modules = array()){
		global $_config;
		require_once SYSTEM_ROOT.'./config.cfg.php';
		foreach($this->system_modules as $module){
			require_once SYSTEM_ROOT."./class/{$module}.php";
		}
		DEBUG::INIT();
		require_once SYSTEM_ROOT.'./function/core.php';
		$modules = $modules ? $modules : $this->modules;
		foreach($modules as $module){
			$method = "_load_module_{$module}";
			if(method_exists($this, $method)){
				$this->$method();
			}else{
				$this->_load_module($module);
			}
		}
		$this->init_system();
	}
	function __destruct(){
		HOOK::run('on_unload');
	}
	function init_output(){
		ob_start();
		header('Content-type: text/html; charset=utf-8');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		@date_default_timezone_set('Asia/Shanghai');
	}
	function init_useragent(){
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(strpos($ua, 'wap') || strpos($ua, 'mobi') || strpos($ua, 'opera') || $_GET['mobile']){
			define('IN_MOBILE', true);
		}else{
			define('IN_MOBILE', false);
		}
		if(strpos($ua, 'bot') || strpos($ua, 'spider')) define('IN_ROBOT', true);
	}
	function init_encrypt_key(){
		if(SYS_KEY){
			define('ENCRYPT_KEY', SYS_KEY);
		}elseif(!getSetting('SYS_KEY')){
			$key = random(32);
			saveSetting('SYS_KEY', $key);
			define('ENCRYPT_KEY', $key);
		}else{
			define('ENCRYPT_KEY', getSetting('SYS_KEY'));
		}
	}
	function init_cookie(){
		global $cookiever, $uid, $username;
		$cookiever = '2';
		if(!empty($_COOKIE['token'])) {
			list($_cookiever, $uid, $username, $login_exp, $password_hash) = explode("\t", authcode($_COOKIE['token'], 'DECODE'));
			if(!$uid || $_cookiever != $cookiever){
				unset($uid, $username, $login_exp);
				dsetcookie('token');
			}elseif($login_exp < TIMESTAMP){
				$user = DB::fetch_first("SELECT * FROM member WHERE uid='{$uid}'");
				$_password_hash = substr(md5($user['password']), 8, 8);
				if($user && $password_hash == $_password_hash){
					$login_exp = TIMESTAMP + 900;
					dsetcookie('token', authcode("{$cookiever}\t{$uid}\t{$user[username]}\t{$login_exp}\t{$password_hash}", 'ENCODE'));
				}else{
					unset($uid, $username, $login_exp);
					dsetcookie('token');
				}
			}
		} else {
			$uid = $username = '';
		}
	}
	function init_system(){
		$this->init_output();
		$this->init_useragent();
		$this->init_encrypt_key();
		$this->init_cookie();
		require_once SYSTEM_ROOT.'./function/safeguard.php';
		_init();
		HOOK::run('on_load');
	}
	function _load_module_hooks(){
		require_once SYSTEM_ROOT.'./class/hooks.php';
		HOOK::INIT();
	}
	function _load_module_updater(){
		require_once SYSTEM_ROOT.'./function/updater.php';
		check_update();
	}
	function _load_module($classname){
		require_once SYSTEM_ROOT."./class/{$classname}.php";
	}
}