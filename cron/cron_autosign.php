<?php
chdir('../');
require_once './system/common.inc.php';
$date = date('Ymd', TIMESTAMP+900);
$_date = getSetting('date');
if($date != $_date){
	if(getSetting('autoupdate')){
		$num = 0;
		$_uid = getSetting('autoupdate_uid') ? getSetting('autoupdate_uid') : 1;
		while($_uid){
			@set_time_limit(3);
			update_liked_tieba($_uid, true);
			$_uid = DB::result_first("SELECT uid FROM member WHERE uid>'{$_uid}' ORDER BY uid ASC LIMIT 0,1");
			if(++$num > 20){
				saveSetting('autoupdate_uid', $_uid);
				exit('等待二次刷新喜欢贴吧列表');
			}
		}
	}
	DB::query("ALTER TABLE sign_log CHANGE `date` `date` INT NOT NULL DEFAULT '{$date}'");
	DB::query("INSERT IGNORE INTO sign_log (tid, uid) SELECT tid, uid FROM my_tieba");
	saveSetting('date', $date);
	saveSetting('autoupdate_uid', 0);
}

$time = date('Hi');
if($time < 3 || $time > 2357) exit('wait for retry');
@set_time_limit(90);
$tids = array();
// 加载待签到的贴吧列表
$query = DB::query("SELECT tid FROM sign_log WHERE status IN (0, 1) AND date='{$date}' ORDER BY RAND() LIMIT 0,40");
while($result = DB::fetch($query)){
	$tids[] = $result['tid'];
}
if(!$tids) exit('所有贴吧都已经签到完成了~');
echo <<<EOF
<style type="text/css">
* { font-size: 12px; }
table { width: 80%; margin: .48em auto; border-collapse: collapse; border-spacing: 0; }
table td { padding: 8px 5px; text-align: center; border: 1px solid #dedede; }
table tr { background: #d5d5d5; }
table tbody tr { background: #efefef; }
table tbody tr:nth-child(odd) { background: #fafafa; }
</style>
<table border="0">
<thead><tr><td>用户</td><td>贴吧</td><td>状态</td></tr></thead>
EOF;
$tid = implode(', ', $tids);
$first = true;
$done = 0;
$query = DB::query("SELECT * FROM my_tieba WHERE tid IN ({$tid})");
while($tieba = DB::fetch($query)){
	if($done > 20) break;
	$uid = $tieba['uid'];
	$setting = get_setting($uid);
	if($setting['sign_method'] == 2){
		$result = mobile_sign($uid, $tieba);
		echo '<tr><td>'.get_username($uid).'</td><td>'.$tieba['name']."</td><td>{$result}</td></tr>\r\n";
		sleep(1);
		$done += 0.7;
		continue;
	}
	$url = "http://wapp.baidu.com/f?kw={$tieba[unicode_name]}";
	$get_url = curl_get($url, $uid);
	if(!$get_url){
		echo '<tr><td>'.get_username($uid).'</td><td>'.$tieba['name']."</td><td>服务器错误，签到失败，稍后会重试</td></tr>\r\n";
		DB::query("UPDATE sign_log set status='1' WHERE tid='{$tieba[tid]}' AND date='{$date}' AND status<2");
		continue;
	}
	$get_url = wrap_text($get_url);
	preg_match('/<ahref="([^"]*?)">签到<\/a>/', $get_url, $matches);
	if (isset($matches[1])){
		if($first){ $first = false; sleep(2); }
		$s = str_replace('&amp;', '&', $matches[1]);
		$sign_url = 'http://tieba.baidu.com'.$s;
		$get_sign = curl_get($sign_url, $uid, $setting['use_bdbowser']);
		$done++;
		if(!$get_sign){
			echo '<tr><td>'.get_username($uid).'</td><td>'.$tieba['name']."</td><td>签到时服务器错误，可能已成功，稍后会重试</td></tr>\r\n";
			DB::query("UPDATE sign_log set status='1' WHERE tid='{$tieba[tid]}' AND date='{$date}' AND status<2");
			continue;
      	}
      	$get_sign = wrap_text($get_sign);
      	preg_match('/<spanclass="light">签到成功，经验值上升<spanclass="light">(\d+)<\/span>/', $get_sign, $matches);
      	if ($matches[1]){
          	echo '<tr><td>'.get_username($uid).'</td><td>'.$tieba['name']."</td><td>签到成功，经验值+{$matches[1]}</td></tr>\r\n";
			DB::query("UPDATE sign_log set status='2', exp='{$matches[1]}' WHERE tid='{$tieba[tid]}' AND date='{$date}'");
			continue;
        }else{
			echo '<tr><td>'.get_username($uid).'</td><td>'.$tieba['name']."</td><td>签到错误，可能已成功，稍后会重试</td></tr>\r\n";
			DB::query("UPDATE sign_log set status='1' WHERE tid='{$tieba[tid]}' AND date='{$date}' AND status<2");
			if($_GET['debug']) exit($sign_url.$get_sign);
			continue;
        }
	}else{
      	preg_match('/<span>已签到<\/span>/', $get_url, $matches);
      	if ($matches[0]){
			echo '<tr><td>'.get_username($uid).'</td><td>'.$tieba['name']."</td><td>此前已成功签到</td></tr>\r\n";
			DB::query("UPDATE sign_log set status='2' WHERE tid='{$tieba[tid]}' AND date='{$date}' AND status<2");
			continue;
        } else {
			echo '<tr><td>'.get_username($uid).'</td><td>'.$tieba['name']."</td><td>找不到签到链接，稍后重试</td></tr>\r\n";
			$retry = DB::result_first("SELECT retry FROM sign_log WHERE tid='{$tieba[tid]}' AND date='{$date}' AND status<2");
			if($retry >= 3){
				DB::query("UPDATE sign_log set status='-1' WHERE tid='{$tieba[tid]}' AND date='{$date}' AND status<2");
			}else{
				DB::query("UPDATE sign_log set status='1', retry=retry+1 WHERE tid='{$tieba[tid]}' AND date='{$date}' AND status<2");
			}
			if($_GET['debug']) exit($get_url);
			continue;
        }
    }
}
echo '</table><meta http-equiv="refresh" content="5" />';