<?php
namespace Ex;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../session/session.php';

class Http extends \Workerman\Worker
{
	private $map_get = [];
	private $map_post = [];
	private $map_html = [];
	private $map_before = [];
	private $map_after = [];

	public function get($path, callable $callback){
		$this->map_get[$path] = $callback;
	}

	public function post($path, callable $callback){
		$this->map_post[$path] = $callback;
	}

	public function html($path, callable $callback){
		$this->map_html[$path] = $callback;
	}

	public function before($path, callable $callback){
		$this->map_before[$path] = $callback;
	}

	public function after($path, callable $callback){
		$this->map_after[$path] = $callback;
	}

	public function onClientMessage($connection, $data)
	{
		\Workerman\Protocols\Http::header("Content-Type: application/json");

		// CORS
		$allow_origins = [
			'https://xx.com',
			'http://localhost:8080',
			'http://127.0.0.1:8080',
		];
		if (defined('COOKIE_DOMAIN')) {
			$allow_origins[] = "https://".COOKIE_DOMAIN;
		}
		if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allow_origins)) {
			\Workerman\Protocols\Http::header("Access-Control-Allow-Origin: ".$_SERVER['HTTP_ORIGIN']);
		} else {
			if ($_SERVER['REQUEST_METHOD'] == 'GET') {
				\Workerman\Protocols\Http::header("Access-Control-Allow-Origin: *");
			} else {
				$connection->send('no origin');
				return;
			}
		}
		\Workerman\Protocols\Http::header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
		\Workerman\Protocols\Http::header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, token, from, lang");
		\Workerman\Protocols\Http::Header("Access-Control-Allow-Credentials: true");

		$req = new \stdClass();

		// cookies
		$req->cookies = new \stdClass();
		if ($_COOKIE) {
			foreach ($_COOKIE as $key => $value) {
				$req->cookies->$key = $value;
			}
		}
		// from
		if (isset($_SERVER['HTTP_FROM'])) {
			$req->_from = $_SERVER['HTTP_FROM'];
		}
		// lang
		if (isset($_SERVER['HTTP_LANG'])) {
			$req->cookies->lang = $_SERVER['HTTP_LANG'];
		}
		// ip
		$ip = $_SERVER['REMOTE_ADDR'];
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		$req->_from_ip = $ip;
		// refer
		$req->_refer = 'REF=';
		if (isset($_SERVER['HTTP_REFERER'])) {
			$req->_refer = $req->_refer . $_SERVER['HTTP_REFERER'];
		}

		// session
		if (defined('SESSION_NAME') and defined('SESSION_TTL') and defined('COOKIE_DOMAIN')) {
			if (isset($_COOKIE[SESSION_NAME]) and \Ex\Session::get($_COOKIE[SESSION_NAME])) {
				$req->session = \Ex\Session::get($_COOKIE[SESSION_NAME]);
				$req->session->session_id = $_COOKIE[SESSION_NAME];
				\Ex\Session::refresh($req->session->session_id, SESSION_TTL);
			} else {
				$session_id = \Ex\Session::new_session_id();
				$session = new \stdClass();
				$req->session = $session;
				$req->session->session_id = $session_id;
				\Ex\Session::set($session_id, $session, SESSION_TTL);
				\Workerman\Protocols\Http::header("Set-Cookie: ".SESSION_NAME."=".$session_id.";Path=/;Max-Age=".SESSION_TTL.";Domain=".COOKIE_DOMAIN);
			}
		}

		// params
		$req->method = $_SERVER['REQUEST_METHOD'];
		$req->uri = $_SERVER['REQUEST_URI'];
		$req->path = $req->uri;
		$pos = stripos($req->path,'?');
		if ($pos !== false) {
			$req->path = substr($req->path,0,$pos);
		}
		if ($req->method == 'GET') {
			$req->params = $_GET;
		} elseif ($req->method == 'POST') {
			$req->params = $_POST;
		} else {
			$req->params = [];
		}
		$ps = [];
		foreach ($req->params as $key => $value) {
			$ps[$key] = $value;
			if (strpos($key, 'password') !== false) {
				$ps[$key] = '****';
			}
		}

		echo "[".date('Y-m-d H:i:s')."] ".$req->method." ".$req->path." ".json_encode($ps, JSON_UNESCAPED_UNICODE)." ".$req->_from_ip." ".$req->_refer."\n";

		// before
		foreach ($this->map_before as $path => $cb) {
			$pos = stripos($req->path,$path);
			if ($pos !== false) {
				$data = call_user_func($cb, $req);
				if (!$data[0]) {
					$connection->send(json_encode($data[1], JSON_UNESCAPED_UNICODE));
					return;
				}
			}
		}

		// callback
		if ($req->method == 'GET') {
			if (isset($this->map_get[$req->path])) {
				$cb = $this->map_get[$req->path];
				$data = call_user_func($cb, $req);
				$connection->send(json_encode($data, JSON_UNESCAPED_UNICODE));
			} elseif (isset($this->map_html[$req->path])) {
				$cb = $this->map_html[$req->path];
				$data = call_user_func($cb, $req);
				\Workerman\Protocols\Http::header("Content-Type: text/html");
				$connection->send($data);
			} else {
				$connection->send(json_encode(['code'=>404, 'message'=>'Not Found']));
			}
		} elseif ($req->method == 'POST') {
			if (isset($this->map_post[$req->path])) {
				$cb = $this->map_post[$req->path];
				$data = call_user_func($cb, $req);
				$connection->send(json_encode($data, JSON_UNESCAPED_UNICODE));
			} else {
				$connection->send(json_encode(['code'=>404, 'message'=>'Not Found']));
			}
		} else {
			$connection->send('ok');
		}

		// after
		foreach ($this->map_after as $path => $cb) {
			$pos = stripos($req->path,$path);
			if ($pos !== false) {
				$data = call_user_func($cb, $req);
			}
		}
	}

	public function run()
	{
		$this->reusePort = true;
		$this->onMessage = [$this, 'onClientMessage'];
		parent::run();
	}
}

// test
// define('SESSION_NAME', 'xx_session_id');
// define('SESSION_TTL', 36000);
// define('COOKIE_DOMAIN', '0.0.0.0');
// define('REDIS_HOST', '127.0.0.1');
// define('REDIS_PORT', 6379);

// $api = new \Ex\Http("http://0.0.0.0:8888");
// $api->count = 2;
// $api->name = 'http';
// $api->onWorkerStart = function($worker) {
// 	echo "[".date('Y-m-d H:i:s')."] Worker start[".$worker->id."]\n";
// };
// $api->get('/', function($req) {
// 	var_dump($req);
// 	return 'OK';
// });
// \Ex\Http::runAll();