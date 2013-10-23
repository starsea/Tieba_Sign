<?php
if(!defined('IN_KKFRAME')) exit();
require_once SYSTEM_ROOT.'./function/sign.php';
$date = date('Ymd', TIMESTAMP + 900);
$count = DB::result_first("SELECT COUNT(*) FROM `sign_log` WHERE status IN (0, 1) AND date='{$date}'");
if($count){
	$offset = rand(1, $count) - 1;
	$tid = DB::result_first("SELECT tid FROM `sign_log` WHERE status IN (0, 1) AND date='{$date}' LIMIT {$offset},1");
	if(!$tid) exit();
	$tieba = DB::fetch_first("SELECT * FROM my_tieba WHERE tid='{$tid}'");
	if($tieba['skiped']){
		DB::query("UPDATE sign_log set status='-2' WHERE tid='{$tieba[tid]}' AND date='{$date}'");
		continue;
	}
	$uid = $tieba['uid'];
	$setting = get_setting($uid);
	list($status, $result, $exp) = client_sign($uid, $tieba);
	if($status == 2){
		if($exp){
			DB::query("UPDATE sign_log SET status='2', exp='{$exp}' WHERE tid='{$tieba[tid]}' AND date='{$date}'");
		}else{
			DB::query("UPDATE sign_log SET status='2' WHERE tid='{$tieba[tid]}' AND date='{$date}' AND status<2");
		}
		$num++;
		$time = 2;
	}else{
		$retry = DB::result_first("SELECT retry FROM sign_log WHERE tid='{$tieba[tid]}' AND date='{$date}' AND status<2");
		if($retry >= 100){
			DB::query("UPDATE sign_log SET status='-1' WHERE tid='{$tieba[tid]}' AND date='{$date}' AND status<2");
		}elseif($status == 1){
			DB::query("UPDATE sign_log SET status='1', retry=retry+1 WHERE tid='{$tieba[tid]}' AND date='{$date}' AND status<2");
		}else{
			DB::query("UPDATE sign_log SET status='1', retry=retry+15 WHERE tid='{$tieba[tid]}' AND date='{$date}' AND status<2");
		}
		$time = 1;
	}
}else{
	$nexttime = TIMESTAMP + 1800;
	DB::query("UPDATE cron SET nextrun='{$nexttime}' WHERE id='sign_retry'");
}
