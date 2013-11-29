<?php
if(!defined('IN_KKFRAME')) exit('Access Denied');
$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `cache` (
  `k` varchar(32) NOT NULL,
  `v` text NOT NULL,
  PRIMARY KEY (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cron` (
  `id` varchar(16) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `nextrun` int(10) unsigned NOT NULL,
  `order` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `cron` (`id`, `enabled`, `nextrun`, `order`) VALUES
('daily', 0, 0, 0),
('ext_sign', 1, 0, 50),
('mail', 1, 0, 100),
('sign', 1, 0, 20),
('sign_retry', 1, 0, 110),
('update_tieba', 1, 0, 10);

CREATE TABLE IF NOT EXISTS `mail_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `member` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(24) DEFAULT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(32) NOT NULL,
  `cookie` text NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `member_bind` (
  `uid` int(10) unsigned NOT NULL,
  `_uid` int(10) unsigned NOT NULL,
  `username` varchar(12) NOT NULL,
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `member_setting` (
  `uid` int(10) unsigned NOT NULL,
  `error_mail` tinyint(1) NOT NULL DEFAULT '1',
  `send_mail` tinyint(1) NOT NULL DEFAULT '0',
  `zhidao_sign` tinyint(1) NOT NULL DEFAULT '0',
  `wenku_sign` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `my_tieba` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `name` varchar(127) NOT NULL,
  `unicode_name` varchar(512) NOT NULL,
  `skiped` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `module` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `plugin` (`id`, `name`, `module`) VALUES
(1, 'debug_info', ''),
(2, 'update_log', '');

CREATE TABLE IF NOT EXISTS `setting` (
  `k` varchar(32) NOT NULL,
  `v` varchar(64) NOT NULL,
  PRIMARY KEY (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sign_log` (
  `tid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `exp` tinyint(4) NOT NULL DEFAULT '0',
  `retry` tinyint(3) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `tid` (`tid`,`date`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOF;

$sql_array = explode(';', $sql);
foreach ($sql_array as $sql){
	$sql = trim($sql);
	if($sql) DB::query($sql);
}

saveSetting('version', '1.13.11.9');
showmessage('1.13.11.9 安装成功！', './', 1);
?>