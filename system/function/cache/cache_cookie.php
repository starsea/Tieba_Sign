<?php
if(!defined('IN_KKFRAME')) exit();

$query = DB::query("SELECT uid, cookie FROM member WHERE uid='{$uid}'");
while($result = DB::fetch($query)){
	$cache[ $result['uid'] ] = $result['cookie'];
}
