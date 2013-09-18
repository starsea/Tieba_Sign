<?php
if(!defined('IN_KKFRAME')) exit();

$query = DB::query('SELECT * FROM setting');
while($result = DB::fetch($query)){
	$cache[ $result['k'] ] = $result['v'];
}
