<?php
require_once './system/common.inc.php';
$invite_code = getSetting('invite_code');
if($_GET['action'] == 'logout' && $_GET['hash']==$formhash){
	dsetcookie('token', '');
	$_COOKIE['token'] = '';
	showmessage('您已经退出登录了！', dreferer(), 1);
}elseif($uid && $_GET['action'] == 'bind_user'){
	if($_POST['formhash'] != $formhash) showmessage('来源不可信，请重试', './');
	if(!$_POST['username']){
		showmessage('请输入用户名', './#');
	}elseif(!$_POST['password']){
		showmessage('请输入密码', './#');
	}
	$_username = daddslashes($_POST['username']);
	if($_username == $username) showmessage('请输入其他账户的信息', './#');
	$email = daddslashes($_POST['email']);
	$password = md5(ENCRYPT_KEY.md5($_POST['password']).ENCRYPT_KEY);
	if(strlen($_username) > 12) showmessage('用户名过长，请修改', dreferer(), 5);
	$user = DB::fetch_first("SELECT * FROM member WHERE username='{$_username}' AND password='{$password}'");
	$userid = $user['uid'];
	if($user){
		$exists = DB::result_first("SELECT _uid FROM member_bind WHERE uid='{$uid}' AND _uid='{$userid}'");
		if($exists) showmessage('您此前已经绑定过此帐号', './#');
		DB::insert('member_bind', array(
			'uid' => $uid,
			'_uid' => $userid,
			'username' => $user['username'],
		));
		showmessage("您已经成功绑定用户“{$user[username]}”", './');
	}else{
		showmessage('用户名/密码不正确！', './#');
	}
}elseif($uid && $_GET['action'] == 'switch'){
	if($_GET['formhash'] != $formhash) showmessage('来源不可信，请重试', './');
	$target_uid = intval($_GET['uid']);
	$uid = DB::result_first("SELECT _uid FROM member_bind WHERE uid='{$uid}' AND _uid='{$target_uid}'");
	if(!$uid) showmessage('您尚未绑定该账号，无法进行切换', './');
	$username = get_username($uid);
	dsetcookie('token', authcode("{$cookiever}\t{$uid}\t{$username}\t0", 'ENCODE'));
	showmessage("您已经成功切换至 {$username}！", dreferer(), 1);
}elseif($uid){
	showmessage('您已经登录了~', dreferer(), 1);
}elseif($_GET['action'] == 'register'){
	if(getSetting('block_register')) showmessage('抱歉，当前站点禁止新用户注册', 'member.php?action=login');
	if($_POST){
		if(!$_POST['username']){
			showmessage('请输入用户名', 'member.php?action=register');
		}elseif(!$_POST['password']){
			showmessage('请输入密码', 'member.php?action=register');
		}elseif(!$_POST['email']){
			showmessage('请输入您的邮箱', 'member.php?action=register');
		}else{
			if($invite_code && $_POST['invite_code'] != $invite_code) showmessage('邀请码有误', 'member.php?action=register');
			$username = daddslashes($_POST['username']);
			$email = daddslashes($_POST['email']);
			$password = md5(ENCRYPT_KEY.md5($_POST['password']).ENCRYPT_KEY);
			if(!$username || !$password || !$email) showmessage('您输入的信息不完整', 'member.php?action=register');
			if(preg_match('/[<>\'\\"]/i', $username)) showmessage('用户名中有被禁止使用的关键字', 'member.php?action=register');
			if(strlen($username) < 6) showmessage('用户名至少要6个字符(即2个中文 或 6个英文)，请修改', dreferer(), 5);
			if(strlen($username) > 12) showmessage('用户名过长，请修改', dreferer(), 5);
			$un = strtolower($username);
			if(strexists($un, 'admin') || strexists($un, 'guanli')) showmessage('用户名不和谐，请修改', dreferer(), 5);
			$user = DB::fetch_first("SELECT * FROM member WHERE username='{$username}'");
			if($user) showmessage('用户名已经存在', 'member.php?action=register');
			$uid = DB::insert('member', array(
				'username' => $username,
				'password' => $password,
				'email' => $email,
			));
			DB::insert('member_setting', array('uid' => $uid));
			dsetcookie('token', authcode("{$cookiever}\t{$uid}\t{$username}\t0", 'ENCODE'));
			showmessage("注册成功，您的用户名是 <b>{$username}</b> 记住了哦~！", dreferer(), 3);
		}
	}
	include template('register');
	exit();
}elseif($_POST){
	if($_POST['username'] && $_POST['password']){
		$username = daddslashes($_POST['username']);
		$password = md5(ENCRYPT_KEY.md5($_POST['password']).ENCRYPT_KEY);
		$un = strtolower($username);
		if(strlen($username) > 12) showmessage('用户名过长，请修改', dreferer(), 5);
		$user = DB::fetch_first("SELECT * FROM member WHERE username='{$username}' AND password='{$password}'");
		$username = $user['username'];
		if($user) {
			$login_exp = TIMESTAMP + 3600;
			$uid = $user['uid'];
			dsetcookie('token', authcode("{$cookiever}\t{$uid}\t{$username}\t0", 'ENCODE'));
			showmessage("欢迎回来，{$username}！", dreferer(), 1);
		}else{
			showmessage('对不起，您的用户名或密码错误，无法登录.', 'member.php?action=login', 3);
		}
	}
}
include template('login');