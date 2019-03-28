<?php
namespace Ex;

require_once __DIR__ . '/../redis/redis.php';

class Auth {

	public static function is_login($req) {
		if (!defined('SESSION_NAME') and !defined('SESSION_TTL')) {
			return true;
		}
		if (isset($req->session->id) and isset($req->session->verified)) {
			return true;
		}
		return false;
	}

	public static function verify_api($req) {
		$key = $_SERVER['HTTP_KEY'];
		$sign = $_SERVER['HTTP_SIGN'];
		if ($req->_from_ip != '127.0.0.1') {
			// verify params
			if (!isset($key) || !isset($sign) || !isset($req->params['nonce'])) {
				return [false, ['code'=>1001, 'message'=>'missing key/sign/nonce']];
			}
			// verify sign
			$secret = \Ex\Redis::get('api:'.$key);
			$post_data = http_build_query($req->params, '', '&');
			$_sign = hash('sha256', urldecode($post_data).$secret);
			if ($sign !== $_sign) {
				return [false, ['code'=>1002, 'message'=>'wrong sign']];
			}
		}
		$req->_key = $key;
		return [true];
	}

}

// test
// $req = new \stdClass();
// $req->session = new \stdClass();
// $req->session->id = 123;
// var_dump(\Ex\Auth::is_login($req));

// define('REDIS_HOST', '127.0.0.1');
// define('REDIS_PORT', 6379);
// $key = '111';
// $secret = '222';
// $sign = hash('sha256', urldecode("nonce=1553769093333").$secret);
// $_SERVER['HTTP_KEY'] = $key;
// $_SERVER['HTTP_SIGN'] = $sign;
// \Ex\Redis::set('api:'.$key, $secret);
// $req = new \stdClass();
// $req->_from_ip = '8.8.8.8';
// $req->params = ['nonce'=>1553769093333];
// var_dump(\Ex\Auth::verify_api($req));