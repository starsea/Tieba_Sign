<?php
if(!defined('IN_KKFRAME')) exit();
?>
<!DOCTYPE html>
<html>
<head>
<title>登录</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta name="HandheldFriendly" content="true" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta name="author" content="kookxiang" />
<meta name="copyright" content="KK's Laboratory" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<link rel="stylesheet" href="./style/main.css" type="text/css" />
</head>
<body>
<div class="wrapper" id="page_login">
<div class="center-box">
<h1>登录</h1>
<form method="post" action="member.php?action=login">
<div class="login-info">
<p><input type="text" name="username" placeholder="用户名" required /></p>
<p><input type="password" name="password" placeholder="密　码" required /></p>
</div>
<p class="btns clearfix">
<span class="float-left"><a href="member.php?action=register" class="tip-text">注册</a></span>
<input type="submit" value="登录" />
</p>
</form>
</div>
</div>
<script src="system/js/placeholder.fix.js"></script>
</body>
</html>