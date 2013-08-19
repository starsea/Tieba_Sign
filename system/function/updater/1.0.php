<?php
if(!defined('IN_KKFRAME')) exit('Access Denied');
DB::query("ALTER TABLE `member` ADD `sign_method` TINYINT(1) NOT NULL DEFAULT '1' AFTER `use_bdbowser`");
DB::query("ALTER TABLE `my_tieba` ADD `fid` int(10) unsigned NOT NULL AFTER `uid`");
DB::query('CREATE TABLE IF NOT EXISTS `setting` (
  `k` varchar(32) NOT NULL,
  `v` varchar(64) NOT NULL,
  PRIMARY KEY (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8');

saveSetting('version', '1.13.8.18');
showmessage('成功更新到 1.13.8.18！', './', 1);
?>