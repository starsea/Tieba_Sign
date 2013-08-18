<?php
require_once './system/common.inc.php';

if(!$uid){
	header('Location: member.php?action=login');
	exit();
}elseif($_GET['action']){
	switch($_GET['action']){
		case 'update_cookie':
			if(!$_POST['cookie']) break;
			$cookie = daddslashes($_POST['cookie']);
			if(!preg_match('/BDUSS=(.+?)/', $cookie)) showmessage('Cookie 信息不完整，请尝试重新获取', './#setting', 1);
			$cookie = daddslashes($cookie);
			DB::query("UPDATE member SET cookie='{$cookie}' WHERE uid='{$uid}'");
			showmessage('您的 Cookie 信息已经更新<script type="text/javascript" src="?action=refresh_liked_tieba&formhash='.$formhash.'"></script>', './#setting', 1);
			break;
		case 'update_setting':
			if($_POST['formhash'] != $formhash) break;
			DB::update('member', array(
				'use_bdbowser' => $_POST['bdbowser'] ? 1 : 0,
				'error_mail' => $_POST['error_mail'] ? 1 : 0,
				'send_mail' => $_POST['send_mail'] ? 1 : 0,
				'sign_method' => intval($_POST['sign_method']),
				), "uid='{$uid}'");
			showmessage('设置已经保存', './#setting', 1);
			break;
		case 'reset_failure':
			if($formhash != $_GET['formhash']) showmessage('请稍候...', '?action=reset_failure&formhash='.$formhash, 0);
			DB::query("UPDATE sign_log SET status='0', retry='0' WHERE uid='{$uid}' AND status<0");
			showmessage('已经重置失败状态，稍后系统将自动重试', './#signlog', 1);
			break;
		case 'refresh_liked_tieba':
			if($formhash != $_GET['formhash']) showmessage('刷新中，请稍候...', '?action=refresh_liked_tieba&formhash='.$formhash, 0);
			list($insert, $deleted) = update_liked_tieba($uid);
			showmessage("喜欢的贴吧列表已经更新,<br>新增{$insert}个贴吧, 删除{$deleted}个贴吧", './#loved', 1);
			break;
	}
	header('Location: ./');
	exit();
}

include template('index');