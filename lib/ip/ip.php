<?php
namespace Ex;

require_once __DIR__ . '/qqwry.php';

class IP {
	private static $_instance;
	public static function getInstance() {
		if (!isset(self::$_instance)) {
			self::$_instance = new \qqwry(__DIR__ . '/qqwry.dat');
		}
		return self::$_instance;
	}

	public static function query($ip) {
		return iconv("GB2312", "UTF-8//IGNORE", \Ex\IP::getInstance()->query($ip)[0]);
	}
}

// test
// var_dump(\Ex\IP::query('47.91.170.207'));
// var_dump(\Ex\IP::query('161.117.97.195'));
// var_dump(\Ex\IP::query('115.239.211.112'));
// var_dump(\Ex\IP::query('192.168.1.1'));
// var_dump(\Ex\IP::query('8.8.8.8'));