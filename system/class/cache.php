<?php
if(!defined('IN_KKFRAME')) exit();

class CACHE{
	public static function get($key){
		static $_cache = array();
		if(isset($_cache[$key])) return $_cache[$key];
		$_cache[$key] = unserialize(DB::result_first("SELECT v FROM cache WHERE k='{$key}'"));
		if(!$_cache[$key]){
			return $_cache[$key] = self::update($key);
		}
		return $_cache[$key];
	}
	public static function save($key, $value){
		$value = addslashes(serialize($value));
		DB::query("REPLACE INTO cache SET k='{$key}', v='{$value}'");
	}
	public static function update($key){
		$builder_file = SYSTEM_ROOT."./function/cache/cache_{$key}.php";
		if(file_exists($builder_file)){
			$cache = array();
			include $builder_file;
			self::save($key, $cache);
			return $cache;
		}
	}
	public static function clear(){
		DB::query("TRUNCATE TABLE cache");
	}
}

?>