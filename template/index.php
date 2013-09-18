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
<link rel="stylesheet" href="./style/main.css?version=<?php echo VERSION; ?>" type="text/css" />
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
<br>
<p>= 多帐号管理 =</p>
<ul class="menu">
<?php
foreach ($users as $_uid => $username){
	echo '<li class="menu_switch_user"><span class="del" href="member.php?action=unbind_user&uid='.$_uid.'&formhash='.$formhash.'">x</span><a href="member.php?action=switch&uid='.$_uid.'&formhash='.$formhash.'">切换至: '.$username.'</a></li>';
}
?>
<li id="menu_adduser"><a href="#user-new">绑定新用户</a></li>
</ul>
<?php if(is_admin($uid)) echo '<br><p>= 管理菜单 =</p><ul class="menu"><li id="menu_admincp"><a href="admin.php">管理面板</a></li><li id="menu_updater"><a href="http://update.kookxiang.com/gateway.php?id=tieba_sign&version='.VERSION.'" target="_blank" onclick="return show_updater_win(this.href)">检查更新</a></li></ul>'; ?>
</div>
<div class="main-content">
<div id="content-loved-tb" class="hidden">
<h2>我喜欢的贴吧</h2>
<p>如果此处显示的贴吧有缺失，请<a href="index.php?action=refresh_liked_tieba" onclick="return msg_redirect_action(this.href+'&formhash='+formhash)">点此刷新喜欢的贴吧</a>.</p>
<table>
<thead><tr><td style="width: 40px">序号</td><td>贴吧</td><td style="width: 65px">忽略签到</td></tr></thead>
<tbody></tbody>
</table>
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
<form method="post" action="index.php?action=update_setting" id="setting_form" onsubmit="return post_win(this.action, this.id)">
<input type="hidden" name="formhash" value="<?php echo $formhash; ?>">
<p>签到增强：</p>
<p><label><input type="checkbox" checked disabled name="bdbowser" id="bdbowser" value="1" /> 模拟百度手机浏览器签到，额外经验+2</label></p>
<p>签到方式：</p>
<p><label><input type="radio" name="sign_method" id="sign_method_1" value="1" /> 签到方式 1 (传统)</label></p>
<p><label><input type="radio" name="sign_method" id="sign_method_2" value="2" /> 签到方式 2 (推荐, 新)</label></p>
<p><label><input type="radio" name="sign_method" id="sign_method_3" value="3" /> 签到方式 3 (测试版, 模拟客户端签到，+8经验)</label></p>
<p>附加签到：</p>
<p><label><input type="checkbox" disabled name="zhidao_sign" id="zhidao_sign" value="1" /> 自动签到百度知道 (测试版)</label></p>
<p><label><input type="checkbox" disabled name="wenku_sign" id="wenku_sign" value="1" /> 自动签到百度文库 (测试版)</label></p>
<p>报告设置：</p>
<p><label><input type="checkbox" checked disabled name="error_mail" id="error_mail" value="1" /> 当天有无法签到的贴吧时给我发送邮件</label></p>
<p><label><input type="checkbox" disabled name="send_mail" id="send_mail" value="1" /> 每日发送一封签到报告邮件</label></p>
<p><input type="submit" value="保存设置" /></p>
</form>
<br>
<p>签到测试：</p>
<p>随机选取一个贴吧，进行一次签到测试，检查你的设置有没有问题</p>
<p><a href="index.php?action=test_sign&formhash=<?php echo $formhash; ?>" class="btn" onclick="return msg_redirect_action(this.href)">测试签到</a></p>
<br>
<p>更改密码：</p>
<form method="post" action="index.php?action=change_password" id="password_form" onsubmit="return post_win(this.action, this.id)">
<input type="hidden" name="formhash" value="<?php echo $formhash; ?>">
<p><input type="password" name="old_password" id="old_password" placeholder="旧密码" /></p>
<p><input type="password" name="new_password" id="new_password" placeholder="新密码" /></p>
<p><input type="password" name="new_password2" id="new_password2" placeholder="请重复输入新密码" /></p>
<p><input type="submit" value="修改密码" /></p>
</form>
<br>
<p>自动获取 Cookie:</p>
<p>将本链接拖到收藏栏，在新页面点击收藏栏中的链接（推荐使用 Chrome 隐身窗口模式），按提示登陆wapp.baidu.com，登陆成功后，在该页面再次点击收藏栏中的链接即可复制cookies信息。</p>
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
<p class="copyright">当前版本：<?php echo VERSION; ?> - <a href="https://me.alipay.com/kookxiang" target="_blank">赞助开发</a><br>Designed by <a href="http://www.ikk.me" target="_blank">kookxiang</a>. 2013 &copy; <a href="http://www.kookxiang.com" target="_blank">KK's Laboratory</a><br>请勿擅自修改程序版权信息或将本程序用于商业用途！</p>
</div>
<script src="//libs.baidu.com/jquery/1.10.2/jquery.min.js"></script>
<script type="text/javascript">
var mobile = <?php echo IN_MOBILE ? '1' : '0'; ?>;
var formhash = '<?php echo $formhash; ?>';
</script>
<script src="system/js/main.js?version=<?php echo VERSION; ?>"></script>
<script src="system/js/fwin.js?version=<?php echo VERSION; ?>"></script>
</body>
</html>