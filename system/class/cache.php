<?php
if(!defined('IN_KKFRAME')) exit();

class CACHE{
	public static function get($key){
		static $_cache = array();
		if(isset($_cache[$key])) return $_cache[$key];
		if(MEMCACHE::isAvailable()){
			$_cache[$key] = MEMCACHE::get($key);
		}else{
			$query = DB::query("SELECT v FROM cache WHERE k='{$key}'", 'SILENT');
			$result = DB::fetch($query);
			$arr = @unserialize($result['v']);
			$_cache[$key] = $arr ? $arr : $result['v'];
		}
		if(!$_cache[$key]){
			return $_cache[$key] = self::update($key);
		}
		return $_cache[$key];
	}
	public static function save($key, $value){
		if(MEMCACHE::isAvailable()){
			MEMCACHE::save($key, $value);
		}else{
			if(is_array($value)) $value = serialize($value);
			$value = addslashes($value);
			DB::query("REPLACE INTO cache SET k='{$key}', v='{$value}'", 'SILENT');
		}
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
		MEMCACHE::clear();
		DB::query("TRUNCATE TABLE cache", 'SILENT');
	}
}

class MEMCACHE{
	public function isAvailable(){
		$object = MEMCACHE::object();
		if(!$object) return false;
		if($object->get('test')) return true;
		$object->set('test', '1');
		return $object->get('test');
	}
	public function object(){
		static $obj;
		if(defined('MEMCACHE_INITED')) return $obj;
		$obj = null;
		if($_SERVER['HTTP_APPVERSION']){
			$obj = memcache_init();
		}elseif($_SERVER['USER'] == 'bae'){
			require_once 'BaeMemcache.class.php';
			$obj = new BaeMemcache();
		}
		define('MEMCACHE_INITED', 'true');
		return $obj;
	}
	function clear(){
		$obj = MEMCACHE::object();
		if(!$obj) return;
		return $obj->clear();
	}
	function get($key){
		$obj = MEMCACHE::object();
		if(!$obj) return;
		return $obj->get($key);
	}
	function save($key, $value, $exp = 3600){
		$obj = MEMCACHE::object();
		if(!$obj) return;
		return $obj->set($key, $value, $exp);
	}
}

?>