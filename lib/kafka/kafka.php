<?php

namespace Ex;

class Kafka {
	private static $brokers = KAFKA_BROKERS;
	private static $_producer;

	public static function getProducer() {
		if (!isset(self::$_producer)) {
			$conf = new \RdKafka\Conf();
			$conf->set('queue.buffering.max.kbytes', 2000000);
			$conf->set('queue.buffering.max.messages', 1000000);
			$rk = new \RdKafka\Producer($conf);
			$rk->setLogLevel(LOG_DEBUG);
			$rk->addBrokers(\Ex\Kafka::$brokers);
			self::$_producer = $rk;
		}
		return self::$_producer;
	}

	public static function getConsumer($group_id) {
		$conf = new \RdKafka\Conf();
		$conf->set('group.id', $group_id);
		$conf->set('offset.store.method', 'broker');
		$rk = new \RdKafka\Producer($conf);
		$rk = new \RdKafka\Consumer($conf);
		$rk->setLogLevel(LOG_DEBUG);
		$rk->addBrokers(\Ex\Kafka::$brokers);
		return $rk;
	}

	public static function produce($topic, $message) {
		if (!defined('KAFKA_BROKERS')) {
			throw new Exception("KAFKA_BROKERS not defined");
		}
		$rk = \Ex\Kafka::getProducer();
		$topic = $rk->newTopic($topic);
		$topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
	}

}

// test
// define('KAFKA_BROKERS', 'localhost');
// \Ex\Kafka::produce("test", "123");
// \Ex\Kafka::produce("test", "456");

// $rk = \Ex\Kafka::getConsumer("group1");
// $topic = $rk->newTopic("test");
// $topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);
// while (true) {
//     $msg = $topic->consume(0, 1000);
//     if ($msg->err) {
//         break;
//     } else {
//         echo $msg->offset, ": ", $msg->payload, "\n";
//     }
// }

// $rk = \Ex\Kafka::getConsumer("group2");
// $topic = $rk->newTopic("test");
// $topic->consumeStart(0, RD_KAFKA_OFFSET_STORED);
// while (true) {
//     $msg = $topic->consume(0, 1000);
//     if ($msg->err) {
//         break;
//     } else {
//         echo $msg->offset, ": ", $msg->payload, "\n";
//     }
// }