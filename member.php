<?php
require_once './system/common.inc.php';
$invite_code = getSetting('invite_code');
if($_GET['action'] == 'logout' && $_GET['hash']==$formhash){
	dsetcookie('token', '');
	$_COOKIE['token'] = '';
	showmessage('您已经退出登录了！', dreferer(), 1);
}elseif($uid && $_GET['action'] == 'unbind_user'){
	if($_GET['formhash'] != $formhash) showmessage('来源不可信，请重试', './');
	$_uid = intval($_GET['uid']);
	$user = DB::fetch_first("SELECT * FROM member_bind WHERE uid='{$uid}' AND _uid='{$_uid}'");
	if(!$user) showmessage('你并没有绑定该账号', './');
	DB::query("DELETE FROM member_bind WHERE uid='{$uid}' AND _uid='{$_uid}'");
	DB::query("DELETE FROM member_bind WHERE uid='{$_uid}' AND _uid='{$uid}'");
	showmessage("成功解除与 {$user['username']} 的绑定！", './');
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
		$exists = DB::result_first("SELECT uid FROM member_bind WHERE _uid='{$uid}' AND uid='{$userid}'");
		if(!$exists){
			$username = DB::result_first("SELECT username FROM member WHERE uid='{$uid}'");
			DB::insert('member_bind', array(
				'uid' => $userid,
				'_uid' => $uid,
				'username' => $username,
			));
		}
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
}elseif($_GET['action'] == 'find_password'){
	if($_GET['token']){
		$str = authcode($_GET['token'], 'DECODE');
		if(!$str) showmessage('链接有误，请重新获取', './');
		list($uid, $exptime, $password, $random) = explode("\t", $str);
		if($exptime < TIMESTAMP) showmessage('链接已过期，请重新获取', './');
		$user = DB::fetch_first("SELECT * FROM member WHERE uid='{$uid}' AND password='{$password}'");
		if(!$user) showmessage('链接已经失效，请重新获取', './');
		$new_password = random(10);
		$newpassword = md5(ENCRYPT_KEY.md5($new_password).ENCRYPT_KEY);
		DB::update('member', array('password' => $newpassword), "uid='{$uid}'");
		showmessage("您的密码已经重置为：<br>{$new_password}<br><br>请使用新密码登录并修改密码。");
	}elseif($_POST['username'] && $_POST['email']){
		$username = daddslashes($_POST['username']);
		$email = daddslashes($_POST['email']);
		$user = DB::fetch_first("SELECT * FROM member WHERE username='{$username}' AND email='{$email}'");
		$info = array(
			$user['uid'],			// UID
			TIMESTAMP + 3600,		// Token 过期时间
			$user['password'],		// 当前密码
			random(32),				// 随机字符
		);
		$token = urlencode(authcode(implode("\t", $info), 'ENCODE'));
		$link = "{$siteurl}member.php?action=find_password&token={$token}";
		$message = <<<EOF
<p>我们已经收到您的找回密码申请，请您点击下方的链接重新设置密码：</p>
<blockquote><a href="{$link}">{$link}</a></blockquote>
<p>（注：请在一小时内点击上面的链接，我们将向您提供新的密码）</p>
<br>
<p>如果您没有要求重置密码却收到本邮件，请及时删除此邮件以确保账户安全。</p>
EOF;
		$res = send_mail($user['email'], "贴吧签到助手 - 密码找回", $message);
		showmessage($res ? '邮件发送成功，请到邮箱查收' : '邮件发送失败，请检查config中的设置', './');
	}
	include template('lost_password');
	exit();
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
			do_login($uid);
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
			do_login($user['uid']);
			showmessage("欢迎回来，{$username}！", dreferer(), 1);
		}else{
			showmessage('对不起，您的用户名或密码错误，无法登录.', 'member.php?action=login', 3);
		}
	}
}
include template('login');