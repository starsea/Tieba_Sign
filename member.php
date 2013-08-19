<?php
require_once './system/common.inc.php';
$invite_code = '';
if($_GET['action'] == 'logout' && $_GET['hash']==$formhash){
	dsetcookie('token', '');
	$_COOKIE['token'] = '';
	showmessage('您已经退出登录了！', dreferer(), 1);
}elseif($uid){
	showmessage('您已经登录了~', dreferer(), 1);
}elseif($_GET['action'] == 'register'){
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
			$password = md5(SYS_KEY.md5($_POST['password']).SYS_KEY);
			if(!$username || !$password || !$email) showmessage('您输入的信息不完整', 'member.php?action=register');
			if(preg_match('/[<>\'\\"]/i', $username)) showmessage('您输入的用户名不合法，用户名仅允许字母和数字！', 'member.php?action=register');
			if(strlen($username) < 6) showmessage('用户名至少要6位，请修改', dreferer(), 5);
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
		$password = md5(SYS_KEY.md5($_POST['password']).SYS_KEY);
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