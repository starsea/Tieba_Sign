<?php
if(!defined('IN_KKFRAME')) exit();
?>
<!DOCTYPE html>
<html>
<head>
<title>贴吧签到助手</title>
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
<h1>贴吧签到助手</h1>
<div class="loading-icon"><img src="style/loading.gif" /> 载入中...</div>
<div class="reload"><img src="style/reload.png" /></div>
<div class="menubtn">&nbsp;</div>
<div class="sidebar">
<ul class="menu">
<li id="menu_sign_log"><a href="#signlog">签到记录</a></li>
<li id="menu_loved_tb"><a href="#loved">我喜欢的贴吧</a></li>
<li id="menu_config"><a href="#setting">设置</a></li>
<li id="menu_logout"><a href="member.php?action=logout&hash=<?php echo $formhash; ?>">退出登录</a></li>
</ul>
<?php if(is_admin($uid)) echo '<br><p>= 管理菜单 =</p><ul class="menu"><li id="menu_admincp"><a href="admin.php">管理面板</a></li><li id="menu_updater"><a href="http://update.kookxiang.com/gateway.php?id=tieba_sign&version='.VERSION.'" target="_blank">检查更新</a></li></ul>'; ?>
</div>
<div class="main-content">
<div id="content-loved-tb" class="hidden">
<h2>我喜欢的贴吧</h2>
<table>
<thead><tr><td style="width: 40px">序号</td><td>贴吧</td></tr></thead>
<tbody></tbody>
</table>
<p>如果此处显示的贴吧有缺失，请<a href="index.php?action=refresh_liked_tieba" onclick="return msg_redirect_action(this.href+'&formhash='+formhash)">点此刷新喜欢的贴吧</a>.</p>
</div>
<div id="content-sign-log" class="hidden">
<h2>签到记录</h2>
<span id="page-flip" class="float-right"></span>
<p id="sign-stat"></p>
<table>
<?php
if(IN_MOBILE){
	echo '<thead><tr><td>序号</td><td>贴吧</td><td>状态</td><td>经验</td></tr></thead>';
}else{
	echo '<thead><tr><td style="width: 40px">序号</td><td>贴吧</td><td style="width: 75px">状态</td><td style="width: 75px">经验</td></tr></thead>';
}
?>
<tbody></tbody>
</table>
</div>
<div id="content-config" class="hidden">
<h2>设置</h2>
<form method="post" action="index.php?action=update_setting">
<input type="hidden" name="formhash" value="<?php echo $formhash; ?>">
<p>签到增强：</p>
<p><label><input type="checkbox" checked disabled name="bdbowser" id="bdbowser" value="1" /> 模拟百度手机浏览器签到，额外经验+2</label></p>
<p>签到方式：</p>
<p><label><input type="radio" name="sign_method" id="sign_method_1" value="1" /> 签到方式 1 (推荐, 传统)</label></p>
<p><label><input type="radio" name="sign_method" id="sign_method_2" value="2" /> 签到方式 2 (新)</label></p>
<p>报告设置：</p>
<p><label><input type="checkbox" checked disabled name="error_mail" id="error_mail" value="1" /> 当天有无法签到的贴吧时给我发送邮件</label></p>
<p><label><input type="checkbox" disabled name="send_mail" id="send_mail" value="1" /> 每日发送一封签到报告邮件</label></p>
<p><input type="submit" value="保存设置" /></p>
</form>
<p>更改密码：</p>
<form method="post" action="index.php?action=change_password">
<input type="hidden" name="formhash" value="<?php echo $formhash; ?>">
<p><input type="password" name="old_password" id="old_password" placeholder="旧密码" /></p>
<p><input type="password" name="new_password" id="new_password" placeholder="新密码" /></p>
<p><input type="password" name="new_password2" id="new_password2" placeholder="请重复输入新密码" /></p>
<p><input type="submit" value="修改密码" /></p>
</form>
<p>自动获取 Cookie:</p>
<p>请将下面的链接拖动到收藏夹，然后再点击该链接并按照提示登录（推荐使用 Chrome 隐身窗口模式），登陆成功后再次点击便可复制。</p>
<p><a href="javascript:(function(){if(document.cookie.indexOf('BDUSS')<0){alert('找不到BDUSS Cookie\n请先登陆 http://wapp.baidu.com/');location.href='http://wappass.baidu.com/passport/?login&u=http%3A%2F%2Fwapp.baidu.com%2F&ssid=&from=&uid=wapp_1375936328496_692&pu=&auth=&originid=2&mo_device=1&bd_page_type=1&tn=bdIndex&regtype=1&tpl=tb';}else{prompt('您的 Cookie 信息如下:', document.cookie);}})();" onclick="alert('请拖动到收藏夹');return false;" class="btn">获取手机百度贴吧 Cookie</a></p>
<br>
<p>手动更新 Cookie:</p>
<form method="post" action="index.php?action=update_cookie">
<p>
<input type="text" name="cookie" style="width: 60%" placeholder="请在此粘贴百度贴吧的 cookie" />
<input type="submit" value="更新" />
</p>
</form>
</div>
</div>
</div>
<p class="copyright">当前版本：<?php echo VERSION; ?> - <a href="http://update.kookxiang.com/gateway.php?id=tieba_sign&version=<?php echo VERSION; ?>" target="_blank">检查更新</a><br>Design by kookxiang. 2013 &copy; KK's Laboratory</p>
</div>
<script src="system/js/jquery.min.js"></script>
<script type="text/javascript">
var mobile = <?php echo IN_MOBILE ? '1' : '0'; ?>;
var formhash = '<?php echo $formhash; ?>';
</script>
<script src="system/js/main.js"></script>
<script src="system/js/fwin.js"></script>
</body>
</html>