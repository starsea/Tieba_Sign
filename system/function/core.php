<?php
if(!defined('IN_KKFRAME')) exit();
function is_admin($uid){
	global $_config;
	return in_array($uid, explode(',', $_config['adminid']));
}
function dsetcookie($name, $value = '', $exp = 2592000){
	$exp = $value ? TIMESTAMP + $exp : '1';
	setcookie($name, $value, $exp, '/');
}
function daddslashes($string, $force = 0, $strip = FALSE) {
	!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	if(!MAGIC_QUOTES_GPC || $force) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = daddslashes($val, $force, $strip);
			}
		} else {
			$string = addslashes($strip ? stripslashes($string) : $string);
		}
	}
	return $string;
}
function template($file){
	HOOK::run("template_load_{$file}");
	if(IN_MOBILE){
		$mobilefile = ROOT."./template/mobile/{$file}.php";
		if(file_exists($mobilefile)) return $mobilefile;
	}
	$path = ROOT."./template/{$file}.php";
	if(file_exists($path)) return $path;
	error::system_error("Missing template '{$file}'.");
}
function dgmdate($timestamp, $d_format = 'Y-m-d H:i') {
	$timestamp += 8 * 3600;
	$todaytimestamp = TIMESTAMP - (TIMESTAMP + 8 * 3600) % 86400 + 8 * 3600;
	$s = gmdate($d_format, $timestamp);
	$time = TIMESTAMP + 8 * 3600 - $timestamp;
	if($timestamp >= $todaytimestamp) {
		if($time > 3600) {
			return '<span title="'.$s.'">'.intval($time / 3600).'&nbsp;小时前</span>';
		} elseif($time > 1800) {
			return '<span title="'.$s.'">半小时前</span>';
		} elseif($time > 60) {
			return '<span title="'.$s.'">'.intval($time / 60).'&nbsp;分钟前</span>';
		} elseif($time > 0) {
			return '<span title="'.$s.'">'.$time.'&nbsp;秒前</span>';
		} elseif($time == 0) {
			return '<span title="'.$s.'">刚刚</span>';
		} else {
			return $s;
		}
	} elseif(($days = intval(($todaytimestamp - $timestamp) / 86400)) >= 0 && $days < 7) {
		if($days == 0) {
			return '<span title="'.$s.'">昨天&nbsp;'.gmdate('H:i', $timestamp).'</span>';
		} elseif($days == 1) {
			return '<span title="'.$s.'">前天&nbsp;'.gmdate('H:i', $timestamp).'</span>';
		} else {
			return '<span title="'.$s.'">'.($days + 1).'&nbsp;天前</span>';
		}
	} else {
		return $s;
	}
}
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;
	$key = md5($key ? $key : ENCRYPT_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);
	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);
	$result = '';
	$box = range(0, 255);
	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}
	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}
function showmessage($msg = '', $redirect = '', $delay = 3){
	if($_GET['format'] == 'json'){
		$result = array('msg' => $msg, 'redirect' => $redirect, 'delay' => $delay);
		echo json_encode($result);
		exit();
	}elseif(IN_MOBILE){
		$msg = $redirect ? "<p>{$msg}</p><ins><a href=\"{$redirect}\">如果您的浏览器没有自动跳转，请点击这里</a></ins><meta http-equiv=\"refresh\" content=\"{$delay};url={$redirect}\" />" : "<p>{$msg}</p>";
		echo '<!DOCTYPE html><html><meta charset=utf-8><title>系统消息</title><meta name=viewport content="initial-scale=1, minimum-scale=1, width=device-width"><style>*{margin:0;padding:0}html{background:#fff;color:#222;padding:15px}body{margin:20% auto 0;min-height:220px;padding:30px 0 15px}p{margin:11px 0 22px;overflow:hidden}ins, ins a{color:#777;text-decoration:none;font-size:10px;}a img{border:0;margin:0 auto;}</style>'.$msg.'</html>';
		exit();
	}
	$msg = $redirect ? "<p>{$msg}</p><ins><a href=\"{$redirect}\">如果您的浏览器没有自动跳转，请点击这里</a></ins><meta http-equiv=\"refresh\" content=\"{$delay};url={$redirect}\" />" : "<p>{$msg}</p>";
	echo '<!DOCTYPE html>
<html><meta charset=utf-8><title>系统消息</title><meta name=viewport content="initial-scale=1, minimum-scale=1, width=device-width"><style>*{margin:0;padding:0}html,code{font:15px/22px arial,sans-serif}html{background:#fff;color:#222;padding:15px}body{margin:7% auto 0;max-width:390px;min-height:220px;padding:75px 0 15px}* > body{background:url(style/msg_bg.png) 100% 5px no-repeat;padding-right:205px}p{margin:11px 0 22px;overflow:hidden}ins, ins a{color:#777;text-decoration:none;font-size:10px;}a img{border:0}@media screen and (max-width:772px){body{background:none;margin-top:0;max-width:none;padding-right:0}}</style><p><b>系统消息 - 贴吧签到助手</b></p>'.$msg.'</html>';
	exit();
}
function random($length, $numeric = 0) {
	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	$hash = '';
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}
function dreferer(){
	return $_SERVER['HTTP_REFERER'] && !strexists($_SERVER['HTTP_REFERER'], 'member') ? $_SERVER['HTTP_REFERER'] : './';
}
function strexists($string, $find) {
	return !(strpos($string, $find) === FALSE);
}
function cutstr($string, $length, $dot = ' ...') {
	if(strlen($string) <= $length) return $string;
	$pre = chr(1);
	$end = chr(1);
	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);
	$strcut = '';
	$n = $tn = $noc = 0;
	while($n < strlen($string)) {
		$t = ord($string[$n]);
		if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
			$tn = 1; $n++; $noc++;
		} elseif(194 <= $t && $t <= 223) {
			$tn = 2; $n += 2; $noc += 2;
		} elseif(224 <= $t && $t <= 239) {
			$tn = 3; $n += 3; $noc += 2;
		} elseif(240 <= $t && $t <= 247) {
			$tn = 4; $n += 4; $noc += 2;
		} elseif(248 <= $t && $t <= 251) {
			$tn = 5; $n += 5; $noc += 2;
		} elseif($t == 252 || $t == 253) {
			$tn = 6; $n += 6; $noc += 2;
		} else {
			$n++;
		}
		if($noc >= $length) break;
	}
	if($noc > $length) $n -= $tn;
	$strcut = substr($string, 0, $n);
	$strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
	$pos = strrpos($strcut, chr(1));
	if($pos !== false) $strcut = substr($strcut,0,$pos);
	return $strcut.$dot;
}
function wrap_text($str) {
	$str = trim($str);
	$str = str_replace("\t", '', $str);
	$str = str_replace("\r", '', $str);
	$str = str_replace("\n", '', $str);
	$str = str_replace(' ', '', $str);
    return trim($str);
}
function get_cookie($uid){
	static $cookie = array();
	if($cookie[$uid]) return $cookie[$uid];
	$cookie = CACHE::get('cookie');
	return $cookie[$uid];
}
function get_username($uid){
	static $username = array();
	if($username[$uid]) return $username[$uid];
	$username = CACHE::get('username');
	return $username[$uid];
}
function get_setting($uid){
	static $user_setting = array();
	if($user_setting[$uid]) return $user_setting[$uid];
	$cached_result = CACHE::get('user_setting_'.$uid);
	if(!$cached_result){
		$cached_result = DB::fetch_first("SELECT * FROM member_setting WHERE uid='{$uid}'");
		CACHE::save('user_setting_'.$uid, $cached_result);
	}
	return $user_setting[$uid] = $cached_result;
}
function send_mail($address, $subject, $message, $delay = true){
	if($delay){
		DB::insert('mail_queue', array(
			'to' => $address,
			'subject' => $subject,
			'content' => $message,
			));
		saveSetting('mail_queue', 1);
		return true;
	}else{
		$mail = new mail_content();
		$mail->address = $address;
		$mail->subject = $subject;
		$mail->message = $message;
		$sender = new mailsender();
		return $sender->sendMail($mail);
	}
}
function getSetting($k, $force = false){
	if($force) return $setting[$k] = DB::result_first("SELECT v FROM setting WHERE k='{$k}'");
	$cache = CACHE::get('setting');
	return $cache[$k];
}
function saveSetting($k, $v){
	$v = addslashes($v);
	DB::query("REPLACE INTO setting SET v='{$v}', k='{$k}'");
	CACHE::update('setting');
}
// Function link
function get_tbs($uid){
	require_once SYSTEM_ROOT.'./function/sign.php';
	return _get_tbs($uid);
}
function verify_cookie($cookie){
	require_once SYSTEM_ROOT.'./function/sign.php';
	return _verify_cookie($cookie);
}
function get_baidu_userinfo($uid){
	require_once SYSTEM_ROOT.'./function/sign.php';
	return _get_baidu_userinfo($uid);
}
function client_sign($uid, $tieba){
	require_once SYSTEM_ROOT.'./function/sign.php';
	return _client_sign($uid, $tieba);
}
function zhidao_sign($uid){
	require_once SYSTEM_ROOT.'./function/sign.php';
	return _zhidao_sign($uid);
}
function wenku_sign($uid){
	require_once SYSTEM_ROOT.'./function/sign.php';
	return _wenku_sign($uid);
}
function update_liked_tieba($uid, $ignore_error = false, $allow_deletion = true){
	require_once SYSTEM_ROOT.'./function/sign.php';
	return _update_liked_tieba($uid, $ignore_error, $allow_deletion);
}
function get_liked_tieba($cookie){
	require_once SYSTEM_ROOT.'./function/sign.php';
	return _get_liked_tieba($cookie);
}
function do_login($uid){
	require_once SYSTEM_ROOT.'./function/member.php';
	_do_login($uid);
}
function delete_user($uid){
	require_once SYSTEM_ROOT.'./function/member.php';
	_delete_user($uid);
}
