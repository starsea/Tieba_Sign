<?php
if(!defined('IN_KKFRAME')) exit();

$query = DB::query("SELECT * FROM plugin");
while($result = DB::fetch($query)){
	$cache[ $result['id'] ] = $result;
}
