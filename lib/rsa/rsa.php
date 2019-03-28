<?php
namespace Ex;

class RSA {
	public static function sign($raw, $pri_key) {
		$res = openssl_get_privatekey($pri_key);
		$sign = '';
		openssl_sign($raw, $sign, $res, OPENSSL_ALGO_SHA1);
		openssl_free_key($res);
		return base64_encode($sign);
	}

	public static function verify($raw, $sign, $pub_key) {
		$res = openssl_get_publickey($pub_key);
		$rsa_result = (bool)openssl_verify($raw, base64_decode($sign), $res, OPENSSL_ALGO_SHA1);
		openssl_free_key($res);
		return $rsa_result;
	}
}


// test
// $pub_key = "-----BEGIN PUBLIC KEY-----
// MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCd/zyRR3YYwiVZ4YK3ghfiC6R/
// y6mj33IeWuhtaq7o0rPKrW3btIGaoRsQc2lxFeW2CokFzpv27YoXcKTre+8N0qbr
// odosKHbwivHI8VAvmkDYZX1n5herk3CedcKpEIJ5hAjvcXrUeUIeMyKqjToZCqEW
// EAnZZ6gylj8MxXT3swIDAQAB
// -----END PUBLIC KEY-----";
// $pri_key = "-----BEGIN PRIVATE KEY-----
// MIICdQIBADANBgkqhkiG9w0BAQEFAASCAl8wggJbAgEAAoGBAJ3/PJFHdhjCJVnh
// greCF+ILpH/LqaPfch5a6G1qrujSs8qtbdu0gZqhGxBzaXEV5bYKiQXOm/btihdw
// pOt77w3Spuuh2iwodvCK8cjxUC+aQNhlfWfmF6uTcJ51wqkQgnmECO9xetR5Qh4z
// IqqNOhkKoRYQCdlnqDKWPwzFdPezAgMBAAECgYBxGb7/uIofmwsl1jq+po/2LqNp
// IB+lwVRtymHLwazH2dz+XzvfJM4KJP28vwFiBGzV7aC82XPoRY6uzOIh+CvdNz/0
// CWF/K/cOhsdiH8c4DfciH3yeaFVfRdN7ysw+CxLu0xXgIu4I37O0OATsKA1wiQJm
// UsQnJXKg0i0Lo+R7kQJBAM5jB2oJAnc6Gm0Sk1qd44c4tpnYatpCCR1HclIvyyBO
// WXa4IITPSOBoh6gQ/tb1nXoq6gKojJcXr3RIp9WvvD0CQQDD+lDjZF+K+/lpgOk4
// TvZt0L69ikL8eClx90REcfrO92cOQuXRGfhUoD+eN1Uj3SvkhhwL7QyL+U2TR2rx
// 4xKvAkBH4gHxEpZDRH9zweaExz06GayvxMrineFiy0GsEm1jISbTzKm22CN/1ah6
// BwFbiUyCAnRi3KWq8lrv22ZpbL3ZAkAqTeeWHn2tX9UoHCa7+/egHvZ9rdHl8/5m
// Vo0LBVuxv6AkaPZ5G9UKV4lEGweq9TxbWUPGo0YZFRjU2Q7R5kWLAkAM4+P0+GxG
// nPMRG376cUJn0HmQAXF6N6nXGYfQRxOT3bMlDY01G5/FyyGtNL23uE0jINiuGxYw
// eRUMijNaudTR
// -----END PRIVATE KEY-----";
// $raw = "1234567";
// $sign = \Ex\RSA::sign($raw, $pri_key);
// var_dump($sign);
// var_dump(\Ex\RSA::verify($raw, $sign, $pub_key));