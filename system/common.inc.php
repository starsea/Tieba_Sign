<?php
error_reporting(E_ALL ^ E_NOTICE);
define('IN_KKFRAME', true);
define('SYSTEM_ROOT', dirname(__FILE__).'/');
define('ROOT', dirname(SYSTEM_ROOT).'/');
define('TIMESTAMP', time());
define('VERSION', '1.13.9.19');
if(!defined('IN_API')) define('IN_API', false);
error_reporting(E_ALL ^ E_NOTICE);
ob_start();
header('Content-type: text/html; charset=utf-8');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-cache');
header('Pragma: no-cache');
@date_default_timezone_set('Asia/Shanghai');

require_once SYSTEM_ROOT.'./config.cfg.php';
require_once SYSTEM_ROOT.'./class/error.php';
require_once SYSTEM_ROOT.'./class/db.php';
require_once SYSTEM_ROOT.'./class/debug.php';
require_once SYSTEM_ROOT.'./class/cache.php';
require_once SYSTEM_ROOT.'./function/core.php';
require_once SYSTEM_ROOT.'./function/updater.php';
require_once SYSTEM_ROOT.'./function/safeguard.php';

DEBUG::INIT();

$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
if(strpos($ua, 'wap') || strpos($ua, 'mobi') || strpos($ua, 'opera') || $_GET['mobile']){
	define('IN_MOBILE', true);
}else{
	define('IN_MOBILE', false);
}
if(strpos($ua, 'bot') || strpos($ua, 'spider')) define('IN_ROBOT', true);
check_update();

if(SYS_KEY){
	define('ENCRYPT_KEY', SYS_KEY);
}elseif(!getSetting('SYS_KEY')){
	$key = random(32);
	saveSetting('SYS_KEY', $key);
	define('ENCRYPT_KEY', $key);
}else{
	define('ENCRYPT_KEY', getSetting('SYS_KEY'));
}

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
$formhash = substr(md5(substr(TIMESTAMP, 0, -7).$username.$uid.ENCRYPT_KEY.ROOT), 8, 8);

if($uid && SYS_KEY && getSetting('SYS_KEY') != SYS_KEY){
	saveSetting('SYS_KEY', SYS_KEY);
}

safeguard_init();

$sitepath = substr($_G['PHP_SELF'], 0, strrpos($_G['PHP_SELF'], '/'));
$siteurl = htmlspecialchars('http://'.$_SERVER['HTTP_HOST'].$sitepath.'/');
