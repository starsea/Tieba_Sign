<?php
if(!defined('IN_KKFRAME')) exit('Access Denied');
DB::query("CREATE TABLE IF NOT EXISTS `member_bind` (
  `uid` int(10) unsigned NOT NULL,
  `_uid` int(10) unsigned NOT NULL,
  `username` varchar(12) NOT NULL,
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

saveSetting('version', '1.13.8.26');
showmessage('成功更新到 1.13.8.26！', './', 1);
?>