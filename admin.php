<?php
define('IN_ADMINCP', true);
require_once './system/common.inc.php';
if(!is_admin($uid)) exit();
$formhash = substr(md5(substr(TIMESTAMP, 0, -7).$username.$uid.SYS_KEY.ROOT.'ADMINCP_ONLY'), 5, 14);

switch($_GET['action']){
	case 'load_userstat':
		$data = array();
		$date = date('Ymd');
		$query = DB::query('SELECT uid, username FROM member ORDER BY uid');
		while($result = DB::fetch($query)){
			$_uid = $result['uid'];
			$data[$_uid] = $result;
			$data[$_uid]['succeed'] = 0;
			$data[$_uid]['skiped'] = 0;
			$data[$_uid]['waiting'] = 0;
			$data[$_uid]['retry'] = 0;
			$data[$_uid]['unsupport'] = 0;
		}
		$query = DB::query("SELECT uid, COUNT(*) FROM `sign_log` WHERE date='{$date}' AND status='2' GROUP BY uid");
		while($result = DB::fetch($query)){
			$_uid = $result['uid'];
			$data[$_uid]['succeed'] = $result['COUNT(*)'];
		}
		$query = DB::query("SELECT uid, COUNT(*) FROM `sign_log` WHERE date='{$date}' AND status='0' GROUP BY uid");
		while($result = DB::fetch($query)){
			$_uid = $result['uid'];
			$data[$_uid]['waiting'] = $result['COUNT(*)'];
		}
		$query = DB::query("SELECT uid, COUNT(*) FROM `sign_log` WHERE date='{$date}' AND status='1' GROUP BY uid");
		while($result = DB::fetch($query)){
			$_uid = $result['uid'];
			$data[$_uid]['retry'] = $result['COUNT(*)'];
		}
		$query = DB::query("SELECT uid, COUNT(*) FROM `sign_log` WHERE date='{$date}' AND status='-1' GROUP BY uid");
		while($result = DB::fetch($query)){
			$_uid = $result['uid'];
			$data[$_uid]['unsupport'] = $result['COUNT(*)'];
		}
		$query = DB::query("SELECT uid, COUNT(*) FROM `sign_log` WHERE date='{$date}' AND status='-2' GROUP BY uid");
		while($result = DB::fetch($query)){
			$_uid = $result['uid'];
			$data[$_uid]['skiped'] = $result['COUNT(*)'];
		}
		exit(json_encode($data));
	case 'load_user':
		$data = array();
		$query = DB::query('SELECT uid, username, email FROM member ORDER BY uid');
		while($result = DB::fetch($query)){
			$data[] = $result;
		}
		exit(json_encode($data));
		break;
	case 'load_setting':
		$data = CACHE::get('setting');
		unset($data['SYS_KEY']);
		exit(json_encode($data));
		break;
	case 'save_setting':
		if($formhash != $_POST['formhash']) showmessage('来源不可信，请重试', 'admin.php#config');
		saveSetting('autoupdate', ($_POST['autoupdate'] ? 1 : 0));
		saveSetting('block_register', ($_POST['block_register'] ? 1 : 0));
		saveSetting('invite_code', daddslashes($_POST['invite_code']));
		showmessage('设置已经保存☆Kira~', 'admin.php#config', 2);
		break;
	case 'deluser':
		$_uid = intval($_GET['uid']);
		if($uid == $_uid) showmessage('删你自己的号是要作死啊？！', 'admin.php#user');
		if($formhash != $_GET['formhash']) showmessage('来源不可信，请重试', 'admin.php#user');
		DB::query("DELETE FROM member WHERE uid='{$_uid}'");
		DB::query("DELETE FROM member_setting WHERE uid='{$_uid}'");
		DB::query("DELETE FROM my_tieba WHERE uid='{$_uid}'");
		DB::query("DELETE FROM sign_log WHERE uid='{$_uid}'");
		showmessage('删除用户成功', 'admin.php#user', 1);
		break;
	case 'update_liked_tieba':
		$_uid = intval($_GET['uid']);
		if($formhash != $_GET['formhash']) showmessage('来源不可信，请重试', 'admin.php#user');
		update_liked_tieba($_uid);
		list($insert, $deleted) = update_liked_tieba($_uid);
		showmessage("喜欢的贴吧列表已经更新,<br>新增{$insert}个贴吧, 删除{$deleted}个贴吧", 'admin.php#user', 1);
		break;
	case 'reset_failure':
		$_uid = intval($_GET['uid']);
		if($formhash != $_GET['formhash']) showmessage('来源不可信，请重试', 'admin.php#stat');
		$date = date('Ymd');
		DB::query("UPDATE sign_log SET status='0', retry='0' WHERE uid='{$_uid}' AND date='{$date}' AND status<0");
		showmessage('已经重置，稍后系统将自动重试', 'admin.php#stat', 1);
		break;
	case 'mail_test':
		$to = DB::result_first("SELECT email FROM member WHERE uid='{$uid}'");
		switch($_GET['method']){
			default:
				showmessage('无效的邮件发送方式', 'admin.php#config');
			case 'default':		$func = 'send_mail';	$method = '默认邮件发送方式';		break;
			case 'kk_mail':		$func = 'kk_mail';		$method = 'SAE 邮件代理';		break;
			case 'saemail':		$func = 'saemail';		$method = 'SAE SMTP 类';			break;
			case 'bcms':		$func = 'bcms_mail';	$method = 'BAE 消息服务';		break;
			case 'mail':		$func = 'mail';			$method = 'PHP mail() 函数';		break;
			case 'smtp':		$func = 'smtp_mail';	$method = '内置 SMTP 类';		break;
		}
		$subject = '[贴吧签到助手] 测试邮件';
		$content = "此封邮件仅用于检测邮件系统是否正常工作。<br>\r\n测试使用的邮件发送方式：{$method}";
		try{
			$result = $func($to, $subject, $content);
		}catch(Exception $e){
			$result = false;
		}
		showmessage(($result ? '邮件发送成功，请查收' : '邮件发送失败'), 'admin.php#config' ,2);
		break;
	default:
		include template('admin');
		break;
}