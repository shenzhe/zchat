<?php

namespace framework\helper\storage;

use framework\manager;

/**
 * redis-stroage 存储处理类
 * redis-stroage 是一个支持leveldb做为持久存储的redis增强版本
 * redis-stroage获取：https://github.com/qiye/redis-storage
 * 如要支持redis-stroage，需要使用增强版本phpredis扩展
 * 增强版本phpredis扩展地址：https://github.com/shenzhe/phpredis
 */
class LRedisHelper implements IStorage {

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

    private function dsKey($ukey, $key) {
        return $ukey . '_' . $key;
    }

    public function getMutilMD($userId, $keys) {
        $uKey = $this->uKey($userId);
        return $this->redis->hMGet($uKey, $keys);
    }

    public function getMD($userId, $key, $slaveName = "") {
        $uKey = $this->uKey($userId);
        $data = $this->redis->hGet($uKey, $key);
        if (false === $data) {
            if ($this->redis->hExists($uKey, $key)) {
                throw new \Exception("{$key} exist");
            }
            //从leveldb取数据
            $dsKey = $this->dsKey($uKey, $key);
            $data = $this->redis->dsGet($dsKey);
            if (!empty($data)) { //取到数据，存回redis
                $this->redis->hSet($uKey, $key, $data);
            }
        }
        return $data;
    }

    public function getSD($userId, $key, $slaveName = "") {
        $uKey = $this->uKey($userId);
        $dsKey = $this->dsKey($uKey, $key);
        return $this->redis->dsGet($dsKey);
    }

    public function getDS($userId, $key) {
        $uKey = $this->uKey($userId);
        $dsKey = $this->dsKey($uKey, $key);
        return $this->redis->dsGet($dsKey);
    }

    public function setDS($userId, $key, $data) {
        $uKey = $this->uKey($userId);
        $dsKey = $this->dsKey($uKey, $key);
        return $this->redis->dsSet($dsKey, $data);
    }

    public function setMD($userId, $key, $data, $cas = false) {
        if ($cas) {
            return $this->setMDCAS($userId, $key, $data);
        }
        $uKey = $this->uKey($userId);
        $dsKey = $this->dsKey($uKey, $key);
        if ($this->redis->dsSet($dsKey, $data)) {
            return $this->redis->hSet($uKey, $key, $data);
        }

        return false;
    }

    public function addMD($userId, $key, $data) {
        $uKey = $this->uKey($userId);
        $dsKey = $this->dsKey($uKey, $key);
        if ($this->redis->dsGet($dsKey)) {
            throw new \Exception("{$dsKey} exist");
        }
        if ($this->redis->dsSet($dsKey, $data)) {
            return $this->redis->hSetNx($uKey, $key, $data);
        }
        return false;
    }

    public function setMDCAS($userId, $key, $data) {
        $uKey = $this->uKey($userId);
        $this->redis->watch($uKey);
        $result = $this->redis->multi()->hSet($uKey, $key, $data)->exec();
        if (false === $result) {
            throw new \Exception('cas error');
        }
        $dsKey = $this->dsKey($uKey, $key);
        if ($this->redis->dsSet($dsKey, $data)) {
            return true;
        }

        throw new \Exception('dsSet error');
    }

    public function del($userId, $key) {

        $uKey = $this->uKey($userId);
        $dsKey = $this->dsKey($uKey, $key);
        if ($this->redis->dsDel($dsKey)) {
            return $this->redis->hDel($uKey, $key);
        }

        return false;
    }

    public function setMultiMD($userId, $keys) {
        $uKey = $this->uKey($userId);
        $dsKeys = [];
        foreach ($keys as $key => $val) {
            $dsKey = $this->dsKey($uKey, $key);
            $dsKeys[$dsKey] = $val;
        }
        if ($this->redis->dsMSet($dsKeys)) {
            return $this->redis->hMSet($uKey, $keys);
        }

        return false;
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
        $uKey = $this->uKey($userId);
        return $this->redis->setTimeout($uKey, $time);
    }

}
