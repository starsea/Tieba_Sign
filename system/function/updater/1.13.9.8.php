<?php
if(!defined('IN_KKFRAME')) exit('Access Denied');
DB::query('CREATE TABLE IF NOT EXISTS `cache` (
  `k` varchar(32) NOT NULL,
  `v` varchar(1024) NOT NULL,
  PRIMARY KEY (`k`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8');
saveSetting('version', '1.13.9.19');
showmessage('成功更新到 1.13.9.19！', './', 1);
?>