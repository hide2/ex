<?php
namespace Ex;

require_once __DIR__ . '/../redis/redis.php';

class Session {

	// 随机session id
	public static function new_session_id() {
		mt_srand();
		return bin2hex(pack('d', microtime(true)) . pack('N',mt_rand(0, 2147483647)));
	}

	// 读session
	public static function get($session_id) {
		$s = \Ex\Redis::get($session_id);
		if ($s) {
			return json_decode($s);
		}
		return null;
	}

	// 写session
	public static function set($session_id, $session, $ttl=null) {
		$s = \Ex\Redis::set($session_id, json_encode($session), $ttl);
	}

	// 刷新session时间
	public static function refresh($session_id, $ttl=null) {
		$s = \Ex\Redis::setTimeout($session_id, $ttl);
	}

	// 清session
	public static function clear($session_id) {
		$s = \Ex\Redis::del($session_id);
	}
}

// test
// define('REDIS_HOST', '127.0.0.1');
// define('REDIS_PORT', 6379);
// $sid = \Ex\Session::new_session_id();
// var_dump($sid);
// \Ex\Session::set($sid, ['user_id'=>123,'name'=>'test']);
// var_dump(\Ex\Session::get($sid));
// \Ex\Session::refresh($sid, 1);
// var_dump(\Ex\Session::get($sid));
// sleep(2);
// var_dump(\Ex\Session::get($sid));
// \Ex\Session::clear($sid);
// var_dump(\Ex\Session::get($sid));