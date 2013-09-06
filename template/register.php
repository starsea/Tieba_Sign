<?php
if(!defined('IN_KKFRAME')) exit();
?>
<!DOCTYPE html>
<html>
<head>
<title>注册 - 贴吧签到助手</title>
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
<h1>注册账号</h1>
<form method="post" action="member.php?action=register">
<div class="login-info">
<p><input type="text" name="username" placeholder="用户名" required /></p>
<p><input type="password" name="password" placeholder="密　码" required /></p>
<p><input type="email" name="email" placeholder="邮　箱" required /></p>
<?php
if($invite_code) echo '<p><input type="text" name="invite_code" placeholder="邀请码" required /></p>';
?>
</div>
<p class="btns clearfix">
<span class="float-left"><a href="member.php?action=login" class="tip-text">登录</a></span>
<input type="submit" value="注册" />
</p>
</form>
</div>
</div>
<script src="system/js/placeholder.fix.js?version=<?php echo VERSION; ?>"></script>
</body>
</html>