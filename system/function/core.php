<?php
if(!defined('IN_KKFRAME')) exit();
function is_admin($uid){
	global $_config;
	return in_array($uid, explode(',', $_config['adminid']));
}
function do_login($uid){
	global $cookiever;
	$user = DB::fetch_first("SELECT * FROM member WHERE uid='{$uid}'");
	$password_hash = substr(md5($user['password']), 8, 8);
	$login_exp = TIMESTAMP + 900;
	dsetcookie('token', authcode("{$cookiever}\t{$uid}\t{$user[username]}\t{$login_exp}\t{$password_hash}", 'ENCODE'));
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
	if(IN_API){
		exit($msg);
	}elseif($_GET['format'] == 'json'){
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
function update_liked_tieba($uid, $ignore_error = false){
	$date = date('Ymd', TIMESTAMP + 900);
	$cookie = get_cookie($uid);
	if(!$cookie){
		if($ignore_error) return;
		showmessage('请先填写 Cookie 信息再更新', './#setting');
	}
	$liked_tieba = get_liked_tieba($cookie);
	$insert = $deleted = 0;
	if(!$liked_tieba){
		if($ignore_error) return;
		showmessage('无法获取喜欢的贴吧，请更新 Cookie 信息', './#setting');
	}
	$my_tieba = array();
	$query = DB::query("SELECT name, fid, tid FROM my_tieba WHERE uid='{$uid}'");
	while($r = DB::fetch($query)) {
		$my_tieba[$r['name']] = $r;
	}
	foreach($liked_tieba as $tieba){
		if($my_tieba[$tieba['name']]){
			unset($my_tieba[$tieba['name']]);
			if(!$my_tieba[$tieba['name']]['fid']) DB::update('my_tieba', array(
				'fid' => $tieba['fid'],
				), array(
					'uid' => $uid,
					'name' => $tieba['name'],
				), true);
			continue;
		}else{
			DB::insert('my_tieba', array(
				'uid' => $uid,
				'fid' => $tieba['fid'],
				'name' => $tieba['name'],
				'unicode_name' => $tieba['uname'],
				), false, true, true);
			$insert++;
		}
	}
	DB::query("INSERT IGNORE INTO sign_log (tid, uid) SELECT tid, uid FROM my_tieba");
	if($my_tieba){
		$tieba_ids = array();
		foreach($my_tieba as $tieba){
			$tieba_ids[] = $tieba['tid'];
		}
		$str = "'".implode("', '", $tieba_ids)."'";
		$deleted = count($my_tieba);
		DB::query("DELETE FROM my_tieba WHERE uid='{$uid}' AND tid IN ({$str})");
		DB::query("DELETE FROM sign_log WHERE uid='{$uid}' AND tid IN ({$str})");
	}
	return array($insert, $deleted);
}
function get_liked_tieba($cookie){
	$pn = 0;
	$kw_name = array();
	while (true){
		$pn++;
		$mylikeurl = "http://tieba.baidu.com/f/like/mylike?&pn=$pn";
		$ch = curl_init($mylikeurl);
		curl_setopt($ch, CURLOPT_URL, $mylikeurl);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		$result = curl_exec($ch);
		curl_close($ch);
		$result = wrap_text($result);
		$pre_reg = '/<tr><td>.*?<ahref="\/f\?kw=.*?"title="(.*?)"/';
		preg_match_all($pre_reg, $result, $matches);
		$count = 0;
		foreach ($matches[1] as $key => $value) {
			$uname = urlencode($value);
			$_uname = preg_quote($value);
			preg_match('/ForumManager\.undo_like\(\'([0-9]+)\',\''.preg_quote($uname).'\'/i', $result, $fid);
			$kw_name[] = array(
				'name' => mb_convert_encoding($value, 'utf-8', 'gbk'),
				'uname' => $uname,
				'fid' => $fid[1],
			);
			$count++;
		}
		if($count==0) break;
	}
	return $kw_name;
}
function wrap_text($str) {
	$str = trim($str);
	$str = str_replace("\t", '', $str);
	$str = str_replace("\r", '', $str);
	$str = str_replace("\n", '', $str);
	$str = str_replace(' ', '', $str);
    return trim($str);
}
function curl_get($url, $uid, $mobile_ua = false, $postdata = ''){
	$ch = curl_init($url);
	if ($mobile_ua){
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Linux; U; Android 4.1.2; zh-cn; MB526 Build/JZO54K) AppleWebKit/530.17 (KHTML, like Gecko) FlyFlow/2.4 Version/4.0 Mobile Safari/530.17 baidubrowser/042_1.8.4.2_diordna_458_084/alorotoM_61_2.1.4_625BM/1200a/39668C8F77034455D4DED02169F3F7C7%7C132773740707453/1'));
    } else {
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent:Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36', 'Connection:keep-alive', 'Referer:http://wapp.baidu.com/'));
    }
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIE, get_cookie($uid));
	if($postdata) curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	$get_url = curl_exec($ch);
	if($get_url !== false){
		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($statusCode >= 500) return false;
  	}
    curl_close($ch);
    return $get_url;
}
function get_cookie($uid){
	static $cookie = array();
	if($cookie[$uid]) return $cookie[$uid];
	return $cookie[$uid] = DB::result_first("SELECT cookie FROM member WHERE uid='{$uid}'");
}
function get_username($uid){
	static $username = array();
	if($username[$uid]) return $username[$uid];
	return $username[$uid] = DB::result_first("SELECT username FROM member WHERE uid='{$uid}'");
}
function get_setting($uid){
	static $user_setting = array();
	if($user_setting[$uid]) return $user_setting[$uid];
	return $user_setting[$uid] = DB::fetch_first("SELECT * FROM member_setting WHERE uid='{$uid}'");
}
function send_mail($address, $subject, $message){
	global $_config;
	switch($_config['mail']['type']){
		case 'kk_mail': return kk_mail($address, $subject, $message);
		case 'bcms':	return bcms_mail($address, $subject, $message);
		case 'saemail': return saemail($address, $subject, $message);
		case 'mail':	return php_mail($address, $subject, $message);
		case 'smtp':	return smtp_mail($address, $subject, $message);
		default: return false;
	}
}
function php_mail($address, $subject, $message){
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-Type: text/html;charset=utf-8' . "\r\n";
$headers .= "Content-Transfer-Encoding: quoted-printable\r\n";
$headers .= "To: You <$address>" . "\r\n";
$headers .= 'From: 贴吧签到助手' . "\r\n";
$message=quoted_printable_encode ( $message );
return mail($address,"=?UTF-8?B?".base64_encode($subject)."?=",$message,$headers);

}
function bcms_mail($address, $subject, $message){
	global $_config;
	require_once SYSTEM_ROOT.'./class/bcms.php';
	$bcms = new Bcms();
    $ret = $bcms->mail($_config['mail']['bcms']['queue'], '<!--HTML-->'.$message, array($address), array(Bcms::MAIL_SUBJECT => $subject));
    if (false === $ret) {
        return false;
    } else {
        return true;
    }
}
function smtp_mail($address, $subject, $message){
	global $_config;
	require_once SYSTEM_ROOT.'./class/smtp.php';
	$smtp = new smtp();
    return $smtp->send($address, $subject, $message);
}
function kk_mail($address, $subject, $message){
	global $_config;
	$data = array(
		'to' => $address,
		'title' => $subject,
		'content' => $message,
		'ver' => VERSION,
	);
	$path = authcode(serialize($data), 'ENCODE', $_config['mail']['kk_mail']['api_key']);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $_config['mail']['kk_mail']['api_path']);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, 'data='.urlencode($path));
	$result = curl_exec($ch);
	curl_close($ch);
	return $result == 'ok';
}
function saemail($address, $subject, $message){
	global $_config;
	$mail = new SaeMail();
	$mail->setOpt(array(
		'from' => 'Mail-System <'.$_config['mail']['saemail']['address'].'>',
		'to' => $address,
		'smtp_host' => $_config['mail']['saemail']['smtp_server'],
		'smtp_username' => $_config['mail']['saemail']['smtp_name'],
		'smtp_password' => $_config['mail']['saemail']['smtp_pass'],
		'subject' => $subject,
		'content' => $message,
		'content_type' => 'HTML',
	));
	$mail->send();
	return true;
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
function get_tbs($uid){
	static $tbs = array();
	if($tbs[$uid]) return $tbs[$uid];
	$tbs_url = 'http://tieba.baidu.com/dc/common/tbs';
	$ch = curl_init($tbs_url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Linux; U; Android 4.1.2; zh-cn; MB526 Build/JZO54K) AppleWebKit/530.17 (KHTML, like Gecko) FlyFlow/2.4 Version/4.0 Mobile Safari/530.17 baidubrowser/042_1.8.4.2_diordna_458_084/alorotoM_61_2.1.4_625BM/1200a/39668C8F77034455D4DED02169F3F7C7%7C132773740707453/1','Referer: http://tieba.baidu.com/'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIE, get_cookie($uid));
	$tbs_json = curl_exec($ch);
	curl_close($ch);
	$tbs = json_decode($tbs_json, 1);
	return $tbs[$uid] = $tbs['tbs'];
}
