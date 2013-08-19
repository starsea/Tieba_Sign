<?php
if(!defined('IN_KKFRAME')) exit('Access Denied');
DB::query("CREATE TABLE IF NOT EXISTS `member_setting` (
  `uid` int(10) unsigned NOT NULL,
  `use_bdbowser` tinyint(1) NOT NULL DEFAULT '1',
  `sign_method` tinyint(1) NOT NULL DEFAULT '1',
  `error_mail` tinyint(1) NOT NULL DEFAULT '1',
  `send_mail` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
DB::query("INSERT INTO member_setting (uid, use_bdbowser, sign_method, error_mail, send_mail) SELECT uid, use_bdbowser, sign_method, error_mail, send_mail FROM member");
DB::query('ALTER TABLE member DROP use_bdbowser');
DB::query('ALTER TABLE member DROP sign_method');
DB::query('ALTER TABLE member DROP error_mail');
DB::query('ALTER TABLE member DROP send_mail');

saveSetting('version', '1.13.8.19');
showmessage('成功更新到 1.13.8.19！', './', 1);
?>