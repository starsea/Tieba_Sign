<?php
if(!defined('IN_KKFRAME')) exit();

function normal_sign($uid, $tieba){
	$setting = get_setting($uid);
	$url = "http://tieba.baidu.com/mo/m?kw={$tieba[unicode_name]}";
	$get_url = curl_get($url, $uid);
	if(!$get_url) return array(1, '无法打开贴吧首页', 0);
	$get_url = wrap_text($get_url);
	preg_match('/<ahref="([^"]*?)">签到<\/a>/', $get_url, $matches);
	if (isset($matches[1])){
		$s = str_replace('&amp;', '&', $matches[1]);
		$sign_url = 'http://tieba.baidu.com'.$s;
		$get_sign = curl_get($sign_url, $uid, $setting['use_bdbowser']);
		$done++;
		if(!$get_sign) return array(1, '签到错误，可能已经签到成功，稍后重试', 0);
      	$get_sign = wrap_text($get_sign);
      	preg_match('/<spanclass="light">签到成功，经验值上升<spanclass="light">(\d+)<\/span>/', $get_sign, $matches);
      	if ($matches[1]){
			return array(2, "签到成功，经验值+{$matches[1]}", $matches[1]);
        }else{
			return array(1, '签到错误，可能已经签到成功，稍后重试', 0);
        }
	}else{
      	preg_match('/<span>已签到<\/span>/', $get_url, $matches);
      	if ($matches[0]){
			return array(2, '此前已成功签到', 0);
        } else {
			return array(-1, '找不到签到链接，稍后重试', 0);
        }
    }
}

function mobile_sign($uid, $tieba){
	$sign_url = 'http://tieba.baidu.com/mo/q/sign?tbs='.get_tbs($uid).'&kw='.$tieba['unicode_name'].'&is_like=1&fid='.$tieba['fid'];
	$c_sign = curl_init($sign_url);
	$refer = 'Referer: http://tieba.baidu.com/f?kw='.$tieba['unicode_name'];
	curl_setopt($c_sign, CURLOPT_HTTPHEADER, array('User-Agent: Mozilla/5.0 (Linux; U; Android 4.1.2; zh-cn; MB526 Build/JZO54K) AppleWebKit/530.17 (KHTML, like Gecko) FlyFlow/2.4 Version/4.0 Mobile Safari/530.17 baidubrowser/042_1.8.4.2_diordna_458_084/alorotoM_61_2.1.4_625BM/1200a/39668C8F77034455D4DED02169F3F7C7%7C132773740707453/1', 'Connection: Keep-Alive', $refer, 'Host: tieba.baidu.com', 'Origin: http://tieba.baidu.com'));
	curl_setopt($c_sign, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($c_sign, CURLOPT_COOKIE, get_cookie($uid));
	$sign_json = curl_exec($c_sign);
	curl_close($c_sign);
	$res = @json_decode($sign_json, true);
	if(!$res) return array(1, 'JSON 解析错误', 0);
	if(strexists($sign_json, '"is_sign_in":1')){
		$exp = $res['data']['msg'];
		return array(2, "签到成功，经验值上升 {$exp}", $exp);
	}else{
		switch($res['no']){
			case '1101':		// 已经签过
				return array(2, $res['data']['msg'], 0);
			//case '1012':		// 贴吧目录出问题?
			case '1002':		// 不支持
				return array(-1, "ERROR-{$res[no]}: ".$res['data']['msg'], 0);
			case '1100':		// 零点 稍后再试
			case '1102':		// 太快了
			case '11000':		// 稍候重试
				return array(1, "ERROR-{$res[no]}: ".$res['data']['msg'], 0);
			default:
				return array(-1, "ERROR-{$res[no]}: ".$res['data']['msg'], 0);
		}
	}
}