<?php

namespace Ex;

class Redis {
	private static  $_instance;
	public static function getInstance() {
		if (!isset(self::$_instance)) {
			self::$_instance = new \Redis();
			self::$_instance->connect(REDIS_HOST, REDIS_PORT);
		}
		return self::$_instance;
	}

	public static function get($k) {
		if (!defined('REDIS_HOST') || !defined('REDIS_PORT')) {
			throw new \Exception("REDIS_HOST/PORT not defined");
		}
		return \Ex\Redis::getInstance()->get($k);
	}

	public static function set($k, $v) {
		if (!defined('REDIS_HOST') || !defined('REDIS_PORT')) {
			throw new \Exception("REDIS_HOST/PORT not defined");
		}
		\Ex\Redis::getInstance()->set($k, $v);
	}

	public static function setTimeout($k, $ttl) {
		if (!defined('REDIS_HOST') || !defined('REDIS_PORT')) {
			throw new \Exception("REDIS_HOST/PORT not defined");
		}
		return \Ex\Redis::getInstance()->setTimeout($k, $ttl);
	}

	public static function del($k) {
		if (!defined('REDIS_HOST') || !defined('REDIS_PORT')) {
			throw new \Exception("REDIS_HOST/PORT not defined");
		}
		return \Ex\Redis::getInstance()->del($k);
	}
}

// test
// define('REDIS_HOST', '127.0.0.1');
// define('REDIS_PORT', 6379);
// \Ex\Redis::set('test', '123');
// var_dump(\Ex\Redis::get('test'));