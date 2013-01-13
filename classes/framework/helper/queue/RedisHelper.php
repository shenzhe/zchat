<?php

namespace framework\helper\queue;

use framework\manager;

/**
 * redis 队列处理类
 */
class RedisHelper implements IQueue {

    private static $redis;

    public function __construct($name = "", $pconnect = false) {
        if (empty(self::$redis)) {
            self::$redis = manager\RedisManager::getInstance($name, $pconnect);
        }
    }

    public function addQueue($key, $data) {
        return self::$redis->rPush($key, $data);
    }

    public function getQueue($key) {
        return self::$redis->lPop($key);
    }

}
