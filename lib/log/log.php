<?php
namespace Ex;

class LOG {
	public static function debug($content) {
		\Ex\LOG::echo("DEBUG", $content);
	}

	public static function info($content) {
		\Ex\LOG::echo("INFO", $content);
	}

	public static function error($content) {
		\Ex\LOG::echo("ERROR", $content);
	}

	public static function echo($level, $content) {
		echo "[".date('Y-m-d H:i:s')."][$level] $content\n";
	}
}


// test
// \Ex\LOG::debug("111");
// \Ex\LOG::info("222");
// \Ex\LOG::error("333");