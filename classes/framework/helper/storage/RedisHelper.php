<?php

namespace framework\helper\storage;

use framework\manager;

/**
 * redis 存储处理类
 */
class RedisHelper implements IStorage {

    private $redis;
    private $sRedis = null;
    private $suffix = "";
    private $pconnect = false;

    public function __construct($name, $pconnect = false) {
        if (empty($this->redis)) {
            $this->redis = manager\RedisManager::getInstance($name, $pconnect);
            $this->pconnect = $pconnect;
        }
    }

    public function setSlave($name, $pconnect = false) {
        if (empty($this->sRedis)) {
            $this->sRedis = manager\RedisManager::getInstance($name, $pconnect);
        }
    }

    public function setKeySuffix($suffix) {
        $this->suffix = $suffix;
    }

    private function uKey($userId) {
        return $userId . '_' . $this->suffix;
    }

    public function getMutilMD($userId, $keys) {
        $uKey = $this->uKey($userId);
        return $this->redis->hMGet($uKey, $keys);
    }

    public function getMD($userId, $key, $slaveName = "") {
        $uKey = $this->uKey($userId);
        $data = $this->redis->hGet($uKey, $key);
        /**
          if(false === $data) {
          if( $this->redis->hExists($uKey, $key) ) {
          throw new \Exception("{$key} exist");
          }
          }
         */
        return $data;
    }

    public function getSD($userId, $key, $slaveName = "") {
        $uKey = $this->uKey($userId);
        $this->setSlave($slaveName);
        $data = $this->sRedis->hGet($uKey, $key);
        /**
          if(false === $data) {
          if( $this->sRedis->hExists($uKey, $key) ) {
          throw new \Exception("{$key} exist");
          }
          }
         *
         */
        return $data;
    }

    public function setMD($userId, $key, $data, $cas = false) {
        if ($cas) {
            return $this->setMDCAS($userId, $key, $data);
        }
        $uKey = $this->uKey($userId);
        return $this->redis->hSet($uKey, $key, $data);
    }

    public function addMD($userId, $key, $data) {
        $uKey = $this->uKey($userId);
        return $this->redis->hSetNx($uKey, $key, $data);
    }

    public function setMDCAS($userId, $key, $data) {
        $uKey = $this->uKey($userId);
        $this->redis->watch($uKey);
        $result = $this->redis->multi()->hSet($uKey, $key, $data)->exec();
        if (false === $result) {
            throw new \Exception('cas error');
        }
        return $result;
    }

    public function del($userId, $key) {
        $uKey = $this->uKey($userId);
        return $this->redis->hDel($uKey, $key);
    }

    public function setMultiMD($userId, $keys) {
        $uKey = $this->uKey($userId);
        return $this->redis->hMSet($uKey, $keys);
    }

    public function close() {
        if ($this->pconnect) {
            return true;
        }

        $this->redis->close();

        if (!empty($this->sRedis)) {
            $this->sRedis->close();
        }

        return true;
    }

    public function getMulti($cmds) {
        $this->redis->multi(\Redis::PIPELINE);
        foreach ($cmds as $userId => $key) {
            $uKey = $this->uKey($userId);
            $this->redis->hGet($uKey, $key);
        }

        return $this->redis->exec();
    }

    public function setExpire($userId, $key, $time) {
        return;
    }

}
