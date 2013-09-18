<?php
if(!defined('IN_KKFRAME')) exit();
?>
<!DOCTYPE html>
<html>
<head>
<title>登录 - 贴吧签到助手</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="HandheldFriendly" content="true" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta name="author" content="kookxiang" />
<meta name="copyright" content="KK's Laboratory" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<link rel="stylesheet" href="./style/main.css?version=<?php echo VERSION; ?>" type="text/css" />
</head>
<body>
<div class="wrapper" id="page_login">
<div class="center-box">
<h1>登录贴吧签到助手</h1>
<form method="post" action="member.php?action=login">
<div class="login-info">
<p>用户名：<input type="text" name="username" placeholder="用户名" required /></p>
<p>密　码：<input type="password" name="password" placeholder="密　码" required /></p>
</div>
<p class="btns clearfix">
<span class="float-left"><a href="member.php?action=register" class="tip-text">注册</a> &nbsp; <a href="member.php?action=find_password" class="tip-text">找回密码</a></span>
<input type="submit" value="登录" />
</p>
</form>
</div>
<p class="copyright">当前版本：<?php echo VERSION; ?> - <a href="https://me.alipay.com/kookxiang" target="_blank">赞助开发</a><br>Designed by <a href="http://www.ikk.me" target="_blank">kookxiang</a>. 2013 &copy; <a href="http://www.kookxiang.com" target="_blank">KK's Laboratory</a><br>请勿擅自修改程序版权信息或将本程序用于商业用途！</p>
</div>
</body>
</html>