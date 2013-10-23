<?php
if(!defined('IN_KKFRAME')) exit();

function client_sign($uid, $tieba){
	$cookie = get_cookie($uid);
	preg_match('/BDUSS=([^ ;]+);/i', $cookie, $matches);
	$BDUSS = trim($matches[1]);
	if(!$BDUSS) return array(-1, '找不到 BDUSS Cookie', 0);
	$ch = curl_init('http://c.tieba.baidu.com/c/c/forum/sign');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIE, get_cookie($uid));
	curl_setopt($ch, CURLOPT_POST, 1);
	$array = array(
		'BDUSS' => $BDUSS,
		'_client_id' => 'wappc_136'.random(10, true).'_'.random(3, true),
		'_client_type' => '2',
		'_client_version' => '4.5.3',
		'_phone_imei' => md5(random(16)),
		'cuid' => md5(random(16)).'|'.random(15, true),
		'fid' => $tieba['fid'],
		'from' => 'tieba',
		'kw' => urldecode($tieba['unicode_name']),
		'model' => 'MiOne',
		'net_type' => '3',
		'stErrorNums' => '0',
		'stMethod' => '1',
		'stMode' => '1',
		'stSize' => random(5, true),
		'stTime' => random(3, true),
		'stTimesNum' => '0',
		'tbs' => get_tbs($uid),
		'timestamp' => time().rand(1000, 9999),
	);
	$sign_str = '';
	foreach($array as $k=>$v) $sign_str .= $k.'='.$v;
	$sign = strtoupper(md5($sign_str.'tiebaclient!!!'));
	$array['sign'] = $sign;
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($array));
	$sign_json = curl_exec($ch);
	curl_close($ch);
	$res = @json_decode($sign_json, true);
	if(!$res) return array(1, 'JSON 解析错误', 0);
	if($res['user_info']){
		$exp = $res['user_info']['sign_bonus_point'];
		return array(2, "签到成功，经验值上升 {$exp}", $exp);
	}else{
		switch($res['error_code']){
			case '160002':		// 已经签过
				return array(2, $res['error_msg'], 0);
			case '1':			// 未登录
				return array(-1, "ERROR-{$res[error_code]}: ".$res['error_msg'].' （Cookie 过期或不正确）', 0);
			case '160004':		// 不支持
				return array(-1, "ERROR-{$res[error_code]}: ".$res['error_msg'], 0);
			case '160003':		// 零点 稍后再试
			case '160008':		// 太快了
				return array(1, "ERROR-{$res[error_code]}: ".$res['error_msg'], 0);
			default:
				return array(1, "ERROR-{$res[error_code]}: ".$res['error_msg'], 0);
		}
	}
}

function zhidao_sign($uid){
	$ch = curl_init('http://zhidao.baidu.com/submit/user');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIE, get_cookie($uid));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, 'cm=100509&t='.TIMESTAMP);
	$result = curl_exec($ch);
	curl_close($ch);
	return @json_decode($result);
}

function wenku_sign($uid){
	$ch = curl_init('http://wenku.baidu.com/task/submit/signin');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 BIDUBrowser/2.x Safari/537.31'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIE, get_cookie($uid));
	$result = curl_exec($ch);
	curl_close($ch);
	return @json_decode($result);
}
