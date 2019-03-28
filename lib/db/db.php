<?php
namespace Ex;

require_once __DIR__ . '/../../vendor/autoload.php';

class DB {
	private static  $_instance;
	public static function getInstance() {
		if (!isset(self::$_instance)) {
			self::$_instance = new \Workerman\MySQL\Connection(DB_HOST, DB_PORT, DB_USER, DB_PASS, DB_SCHEMA);
		}
		return self::$_instance;
	}

	public static function insert($table) {
		return \Ex\DB::getInstance()->insert($table);
	}

	public static function update($table) {
		return \Ex\DB::getInstance()->update($table);
	}

	public static function delete($table) {
		return \Ex\DB::getInstance()->delete($table);
	}

	public static function select($select) {
		return \Ex\DB::getInstance()->select($select);
	}

	public static function query($sql) {
		return \Ex\DB::getInstance()->query($sql);
	}

	public static function begin() {
		\Ex\DB::getInstance()->beginTrans();
	}

	public static function commit() {
		\Ex\DB::getInstance()->commitTrans();
	}

	public static function rollback() {
		\Ex\DB::getInstance()->rollBackTrans();
	}
}


// test
// define('DB_HOST', "localhost");
// define('DB_PORT', 3306);
// define('DB_USER', 'test');
// define('DB_PASS', 'test');
// define('DB_SCHEMA', 'test');

// try {
// 	\Ex\DB::begin();
// 	\Ex\DB::insert('users')->cols(['name'=>'test1'])->query();
// 	\Ex\DB::insert('users')->cols(['name'=>'test2'])->query();
// 	\Ex\DB::insert('users')->cols(['name'=>'test3'])->query();
// 	\Ex\DB::update('users')->cols(['name'=>'test222'])->where('id = :id')->bindValues(['id'=>2])->query();
// 	\Ex\DB::delete('users')->where('name="test3"')->query();
// 	\Ex\DB::commit();
// } catch(Exception $e) {
// 	\Ex\DB::rollback();
// 	echo $e->getMessage();
// }

// var_dump(\Ex\DB::query('select * from users'));

// var_dump(\Ex\DB::select('*')->from('users')->where('id = :id')->
// 	bindValues(['id'=>1])->row());