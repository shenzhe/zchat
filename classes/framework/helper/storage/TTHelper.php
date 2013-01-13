<?php

namespace framework\helper\storage;

use framework\manager;

/**
 *  ttserver 存储处理类
 */
class TTHelper implements IStorage {

    private $tokyoTyrant;
    private $sTokyoTyrant = null;
    private $suffix = "";

    public function __construct($name, $pconnect = false) {
        if (!empty($this->tokyoTyrant)) {
            $this->tokyoTyrant = manager\MemcachedManager::getInstance($name, $pconnect);
        }
    }

    public function setSlave($name) {
        if (empty($this->sTokyoTyrant)) {
            $this->sTokyoTyrant = manager\MemcachedManager::getInstance($name);
        }
    }

    public function get($userId, $key) {
        $key = $this->uKey($userId, $key);
        return $this->tokyoTyrant->get($key);
    }

    public function set($userId, $key, $data) {
        $key = $this->uKey($userId, $key);
        return $this->tokyoTyrant->set($key, $data);
    }

    public function del($userId, $key) {
        $key = $this->uKey($userId, $key);
        return $this->tokyoTyrant->delete($key);
    }

    public function delMulti($userId, $keys) {
        foreach ($keys as $key) {
            $this->del($userId, $key);
        }
        return true;
    }

    public function getMutilMD($userId, $keys) {
        $newKeys = array();
        foreach ($keys as $key) {
            $newKeys[] = $this->uKey($userId, $key);
        }
        return $this->tokyoTyrant->getMulti($newKeys);
    }

    public function getMD($userId, $key, $slaveName = "") {
        $key = $this->uKey($userId, $key);
        $data = $this->tokyoTyrant->get($key);

        if (false === $data) {
            $code = $this->tokyoTyrant->getResultCode();
            if ($code == \Memcached::RES_NOTFOUND) {
                $this->setSlave($slaveName);
                $data = $this->sTokyoTyrant->get($key);
                if (false === $data) {
                    $code = $this->sTokyoTyrant->getResultCode();
                    if ($code == \Memcached::RES_NOTFOUND) {
                        return false;
                    } else {
                        throw new \Exception("null data: {$userId}, {$key}, {$code}");
                    }
                }
            } else {
                throw new \Exception("error data: {$userId}, {$key}, {$code}");
            }
        }
        return $data;
    }

    public function getSD($userId, $key, $slaveName = "") {
        $key = $this->uKey($userId, $key);
        $data = $this->sTokyoTyrant->get($key);
        if (false === $data) {
            $code = $this->sTokyoTyrant->getResultCode();
            if ($code == \Memcached::RES_NOTFOUND) {
                return false;
            } else {
                throw new \Exception("null data: {$userId}, {$key}, {$code}");
            }
        }

        return $data;
    }

    public function setMD($userId, $key, $data) {
        $key = $this->uKey($userId, $key);
        return $this->tokyoTyrant->set($key, $data);
    }

    public function setMDCAS($userId, $key, $data) {
        $key = $this->uKey($userId, $key);
        return $this->tokyoTyrant->set($key, $data);
    }

    public function setMultiMD($userId, $keys) {
        foreach ($keys as $key => $value) {
            $newKey = $this->uKey($userId, $key);
            $keys[$newKey] = $value;
            unset($key);
        }
        return $this->tokyoTyrant->setMulti($keys);
    }

    public function setKeySuffix($suffix) {
        $this->suffix = $suffix;
    }

    private function uKey($userId, $key) {
        return $userId . "_" . $this->suffix . "__" . $key;
    }

    public function close() {
        return true;
    }

    public function setExpire($userId, $key, $time) {
        return;
    }

}
