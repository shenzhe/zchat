<?php

namespace framework\helper\cache;

use framework\manager;

/**
 * Redis cache处理类
 */
class RedisHelper implements ICacheHelper {

    private static $redis;

    public function __construct($name = "", $pconnect = false) {
        if (empty(self::$redis)) {
            self::$redis = manager\RedisManager::getInstance($name, $pconnect);
        }
    }

    public function enable() {
        return true;
    }

    public function add($key, $value, $expiration = 0) {
        $result = self::$redis->setNx($key, $value);
        if ($result && $expiration > 0) {
            self::$redis->setTimeout($key, $expiration);
        }
        return $result;
    }

    public function set($key, $value, $expiration = 0) {
        if ($expiration) {
            return $result = self::$redis->setex($key, $expiration, $value);
        } else {
            return $result = self::$redis->set($key, $value);
        }
    }

    public function addToCache($key, $value, $expiration = 0) {
        $value = \igbinary_serialize($value);
        return $this->set($key, $value, $expiration);
    }

    public function get($key) {
        return self::$redis->get($key);
    }

    public function getCache($key) {
        return \igbinary_unserialize($this->get($key));
    }

    public function delete($key) {
        return self::$redis->delete($key);
    }

    public function increment($key, $offset = 1) {
        return self::$redis->incrBy($key, $offset);
    }

    /**
     * 减少整数数据的值
     *
     * @param string $key
     * @param int $offset
     * @return bool
     */
    public function decrement($key, $offset = 1) {
        return self::$redis->decBy($key, $offset);
    }

}
