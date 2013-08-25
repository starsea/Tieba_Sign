<?php
if(!defined('IN_KKFRAME')) exit();
$_config = array();

// ------------------ 系统设定 ------------------
define('SYS_KEY', '123456789023456789asfasfasfasfbdnmcvbng');		// 加密密钥，用于加密密码等信息，乱打就行
$_config['adminid'] = '1';											// 超级管理员 UID，如有多个使用英文逗号分隔
$_config['cronkey'] = '';											// 任务执行 KEY，防止他人恶意刷邮件任务，留空为不需要。设置后需要在邮件计划任务脚本后加上 ?key= (此处设定的 key)
// -------------- END 系统设定 ------------------

// ------------------ BAE 数据库设定 ------------------
$_config['db']['server'] = getenv('HTTP_BAE_ENV_ADDR_SQL_IP');
$_config['db']['port'] = getenv('HTTP_BAE_ENV_ADDR_SQL_PORT');
$_config['db']['username'] = getenv('HTTP_BAE_ENV_AK');
$_config['db']['password'] = getenv('HTTP_BAE_ENV_SK');
$_config['db']['name'] = 'GXQLIvEDHyaq';	// 修改成你的数据库名
// -------------- END BAE 数据库设定 ------------------

// ------------------ SAE 数据库设定 ------------------
if(defined('SAE_MYSQL_DB')){						// 已自动设置好，无需干预
	$_config['db']['server'] = SAE_MYSQL_HOST_M;
	$_config['db']['port'] = SAE_MYSQL_PORT;
	$_config['db']['username'] = SAE_MYSQL_USER;
	$_config['db']['password'] = SAE_MYSQL_PASS;
	$_config['db']['name'] = SAE_MYSQL_DB;
}elseif(!getenv('HTTP_BAE_ENV_ADDR_SQL_IP')){
// -------------- END SAE 数据库设定 ------------------

// ------------------ 非BAE、SAE 数据库设定 ------------------
	$_config['db']['server'] = 'localhost';			// 数据库服务器地址
	$_config['db']['port'] = '3306';				// 数据库端口
	$_config['db']['username'] = 'root';			// 数据库用户名
	$_config['db']['password'] = 'root';			// 数据库密码
	$_config['db']['name'] = 'kk_sign';				// 数据库名
}
// -------------- END 非BAE、SAE 数据库设定 ------------------

// ------------------ 邮件系统设定 ------------------
/*
 * 注：选择相应的发送方式后，请填写对应的设置
 *
 * 邮件发送方式：
 * none			不发送邮件
 * kk_mail		由 KK 提供，经 SAE 发送邮件
 * bcms			BAE 用户可用，通过 BCMS 发送邮件（注：易被当作垃圾邮件拦截）
 * saemail		SAE 用户可用，通过 SAE 的 SMTP 类发送邮件
 * mail			其他服务器用户可用，调用 PHP 的 Mail 函数发邮件（成功率较低）
 * smtp			其他服务器用户可用，通过 SMTP 服务器发邮件
 */
$_config['mail']['type'] = 'none';		// 邮件发送方式

// kk_mail，由 KK 提供，经 SAE 发送邮件，API 和 key 已经给出，一般不需要修改
$_config['mail']['kk_mail'] = array();
$_config['mail']['kk_mail']['api_path'] = 'http://miota.sinaapp.com/api/bae_mail_helper_open.php';
$_config['mail']['kk_mail']['api_key'] = '4b23f88a7fdd393b976b';

// bcms，BAE 用户可用，通过 BCMS 发送邮件（注：易被当作垃圾邮件拦截）
$_config['mail']['bcms'] = array();
$_config['mail']['bcms']['queue'] = '123457890';		// 百度消息队列

// saemail，SAE 用户可用，通过 SAE 的 SMTP 类发送邮件，请按给出的例子修改
$_config['mail']['saemail'] = array();
$_config['mail']['saemail']['smtp_server'] = 'smtp.exmail.qq.com';	// SMTP 服务器地址
$_config['mail']['saemail']['address'] = 'system@ikk.me';			// 发送者邮箱地址
$_config['mail']['saemail']['smtp_name'] = 'system@ikk.me';			// SMTP 用户名
$_config['mail']['saemail']['smtp_pass'] = 'password';				// SMTP 密码

// smtp 通过 SMTP 服务器发邮件，请按给出的例子修改
$_config['mail']['smtp'] = array();
$_config['mail']['smtp']['smtp_server'] = 'smtp.exmail.qq.com';	// SMTP 服务器地址
$_config['mail']['smtp']['address'] = 'system@ikk.me';			// 发送者邮箱地址
$_config['mail']['smtp']['smtp_name'] = 'system@ikk.me';		// SMTP 用户名
$_config['mail']['smtp']['smtp_pass'] = 'password';				// SMTP 密码

?>