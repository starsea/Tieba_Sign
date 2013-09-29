<?php
if(!defined('IN_KKFRAME')) exit();
/*
 * 显示调试信息，删除本文件即可关闭此功能
 */
function output_debug(){
	echo DEBUG::output();
}
HOOK::register('page_footer', 'output_debug');
?>