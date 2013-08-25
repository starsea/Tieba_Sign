<?php
if(!defined('IN_ADMINCP')) exit();
?>
<!DOCTYPE html>
<html>
<head>
<title>管理中心 - 贴吧签到助手</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="HandheldFriendly" content="true" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta name="author" content="kookxiang" />
<meta name="copyright" content="KK's Laboratory" />
<link rel="shortcut icon" href="/favicon.ico" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<link rel="stylesheet" href="./style/main.css" type="text/css" />
</head>
<body>
<div class="wrapper" id="page_index">
<div id="append_parent"></div>
<div class="main-box clearfix">
<h1>贴吧签到助手 - 管理中心</h1>
<div class="loading-icon"><img src="style/loading.gif" /> 载入中...</div>
<div class="menubtn">&nbsp;</div>
<div class="sidebar">
<ul class="menu">
<li id="menu_user"><a href="#user">用户管理</a></li>
<li id="menu_stat"><a href="#stat">用户签到统计</a></li>
<li id="menu_config"><a href="#config">系统设置</a></li>
<li id="menu_updater"><a href="http://update.kookxiang.com/gateway.php?id=tieba_sign&version=<?php echo VERSION; ?>" target="_blank">检查更新</a></li>
<li><a href="./">返回前台</a></li>
</ul>
</div>
<div class="main-content">
<div id="content-user" class="hidden">
<h2>用户管理</h2>
<table>
<thead><tr><td style="width: 40px">UID</td><td>用户名</td><td>邮箱</td><td>操作</td></tr></thead>
<tbody></tbody>
</table>
</div>
<div id="content-stat" class="hidden">
<h2>用户签到统计</h2>
<table>
<thead><tr><td style="width: 40px">UID</td><td>用户名</td><td>已成功</td><td>待签到</td><td>待重试</td><td>不支持</td></tr></thead>
<tbody></tbody>
</table>
</div>
<div id="content-config" class="hidden">
<h2>系统设置</h2>
<form method="post" action="admin.php?action=save_setting">
<p>功能增强</p>
<input type="hidden" name="formhash" value="<?php echo $formhash; ?>">
<p><label><input type="checkbox" id="block_register" name="block_register" /> 关闭新用户注册功能</label></p>
<p><input type="text" name="invite_code" id="invite_code" placeholder="邀请码 (留空为不需要)" /></p>
<p><label><input type="checkbox" id="autoupdate" name="autoupdate" /> 每天自动更新用户喜欢的贴吧 (Beta, 稍占服务器资源)</label></p>
<p><input type="submit" value="保存设置" /></p>
</form>
<br>
<p>邮件发送测试:</p>
<p>当前发送方式：<?php echo $_config['mail']['type']; ?></p>
<p><a href="admin.php?action=mail_test&method=default" class="btn">使用当前配置发送测试邮件</a></p>
<br>
<p>测试其它邮件发送方式 (调试用):</p>
<p>使用 KK 提供的 SAE 邮件代理发送测试邮件 (发送者显示 KK-Open-Mail-System &lt;open_mail_api@ikk.me&gt;)</p>
<p><a href="admin.php?action=mail_test&method=kk_mail" class="btn" onclick="return msg_win_action(this.href)">发送测试邮件</a></p>
<p>使用 BAE 提供的 BCMS 消息服务发送测试邮件 (发送者显示 *******@duapp.com，一般进垃圾箱)</p>
<p><a href="admin.php?action=mail_test&method=bcms" class="btn" onclick="return msg_win_action(this.href)">发送测试邮件</a></p>
<p>使用 SAE 提供的 SMTP 类发送测试邮件 (仅 SAE 用户可用, 发送者为您的邮箱)</p>
<p><a href="admin.php?action=mail_test&method=saemail" class="btn" onclick="return msg_win_action(this.href)">发送测试邮件</a></p>
<p>使用 内置的 SMTP 类 (需防火墙开放25端口)</p>
<p><a href="admin.php?action=mail_test&method=smtp" class="btn" onclick="return msg_win_action(this.href)">发送测试邮件</a></p>
<p>使用 PHP 的 mail 函数发送测试邮件 (成功率较低，定制性差)</p>
<p><a href="admin.php?action=mail_test&method=mail" class="btn" onclick="return msg_win_action(this.href)">发送测试邮件</a></p>
</div>
</div>
</div>
<p class="copyright">Design by kookxiang. 2013 &copy; KK's Laboratory</p>
</div>
<script src="system/js/jquery.min.js"></script>
<script type="text/javascript">
var mobile = <?php echo IN_MOBILE ? '1' : '0'; ?>;
var formhash = '<?php echo $formhash; ?>';
</script>
<script src="system/js/admin.js"></script>
<script src="system/js/fwin.js"></script>
</body>
</html>