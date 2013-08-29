<?php
if(!defined('IN_KKFRAME')) exit('Access Denied');
DB::query("ALTER TABLE `my_tieba` ADD `skiped` TINYINT(1) NOT NULL DEFAULT '0'");

saveSetting('version', '1.13.8.29');
showmessage('成功更新到 1.13.8.29！', './', 1);
?>