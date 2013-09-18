<?php
chdir('../');
require_once './system/common.inc.php';
require_once SYSTEM_ROOT.'./function/sign.php';
$date = date('Ymd', TIMESTAMP+900);
$_date = getSetting('date');
if($date != $_date){
	if(getSetting('autoupdate')){
		$num = 0;
		$_uid = getSetting('autoupdate_uid') ? getSetting('autoupdate_uid') : 1;
		while($_uid){
			if(++$num > 20){
				saveSetting('autoupdate_uid', $_uid);
				exit('等待二次刷新喜欢贴吧列表');
			}
			@set_time_limit(3);
			update_liked_tieba($_uid, true);
			$_uid = DB::result_first("SELECT uid FROM member WHERE uid>'{$_uid}' ORDER BY uid ASC LIMIT 0,1");
		}
	}
	DB::query("ALTER TABLE sign_log CHANGE `date` `date` INT NOT NULL DEFAULT '{$date}'");
	DB::query("INSERT IGNORE INTO sign_log (tid, uid) SELECT tid, uid FROM my_tieba");
	$delete_date = date('Ymd', TIMESTAMP - 86400*30);
	DB::query("DELETE FROM sign_log WHERE date<'{$delete_date}'");
	saveSetting('date', $date);
	saveSetting('extsigned', 0);
	saveSetting('extsign_uid', 0);
	saveSetting('autoupdate_uid', 0);
	CACHE::clear();
}

$time = date('Hi');
if($time < 3 || $time > 2357) exit('wait for retry');
@set_time_limit(90);
$tid = DB::result_first("SELECT tid FROM sign_log WHERE status IN (0, 1) AND date='{$date}' ORDER BY RAND() LIMIT 0,1");
if(!$tid){
	if(!getSetting('extsigned')){
		$num = 0;
		$_uid = getSetting('extsign_uid') ? getSetting('extsign_uid') : 1;
		while($_uid){
			if(++$num > 20){
				saveSetting('extsign_uid', $_uid);
				exit('等待继续进行拓展签到');
			}
			$setting = get_setting($_uid);
			if($setting['zhidao_sign']) zhidao_sign($_uid);
			if($setting['wenku_sign']) wenku_sign($_uid);
			$_uid = DB::result_first("SELECT uid FROM member WHERE uid>'{$_uid}' ORDER BY uid ASC LIMIT 0,1");
		}
		saveSetting('extsigned', 1);
	}
	exit('所有贴吧都已经签到完成了~');
}
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
$first = true;
$done = 0;
while($tid){
	$tieba = DB::fetch_first("SELECT * FROM my_tieba WHERE tid='{$tid}'");
	if($done > 20) break;
	if($tieba['skiped']){
		DB::query("UPDATE sign_log set status='-2' WHERE tid='{$tieba[tid]}' AND date='{$date}'");
		continue;
	}
	$uid = $tieba['uid'];
	$setting = get_setting($uid);
	if($setting['sign_method'] == 2){
		list($status, $result, $exp) = mobile_sign($uid, $tieba);
	}elseif($setting['sign_method'] == 3){
		list($status, $result, $exp) = client_sign($uid, $tieba);
	}else{
		list($status, $result, $exp) = normal_sign($uid, $tieba);
	}
	echo '<tr><td>'.get_username($uid).'</td><td>'.$tieba['name']."</td><td>{$result}</td></tr>\r\n";
	if($status == 2){
		if($exp){
			DB::query("UPDATE sign_log set status='2', exp='{$exp}' WHERE tid='{$tieba[tid]}' AND date='{$date}'");
		}else{
			DB::query("UPDATE sign_log set status='2' WHERE tid='{$tieba[tid]}' AND date='{$date}' AND status<2");
		}
		$done += 0.7;
	}else{
		$retry = DB::result_first("SELECT retry FROM sign_log WHERE tid='{$tieba[tid]}' AND date='{$date}' AND status<2");
		if($retry >= 100){
			DB::query("UPDATE sign_log set status='-1' WHERE tid='{$tieba[tid]}' AND date='{$date}' AND status<2");
		}elseif($status == 1){
			DB::query("UPDATE sign_log set status='1', retry=retry+1 WHERE tid='{$tieba[tid]}' AND date='{$date}' AND status<2");
		}else{
			DB::query("UPDATE sign_log set status='1', retry=retry+15 WHERE tid='{$tieba[tid]}' AND date='{$date}' AND status<2");
		}
	}
	sleep(1);
	$tid = DB::result_first("SELECT tid FROM sign_log WHERE status IN (0, 1) AND date='{$date}' ORDER BY RAND() LIMIT 0,1");
}
echo '</table><meta http-equiv="refresh" content="5" />';