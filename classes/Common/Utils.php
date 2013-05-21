<?php

namespace common;

use framework\config;
use framework\manager;
use framework\helper;
use framework\view;

class Utils {

    public static $loginUserId = null;


    public static function mergePath() {
        return \implode(\DIRECTORY_SEPARATOR, \func_get_args());
    }

    public static function initConfig() {
        $pdoConfig = new config\PDOConfiguration(
                            DB_HOST,
                            DB_USER,
                            DB_PASS,
                            DEFAULT_CHARSET
            );
        manager\PDOManager::addConfigration(\CONFIG_COMMON_DB_PREFIX, $pdoConfig);
        manager\RedisManager::addConfigration(\CACHE_PX, new config\RedisConfiguration(\CACHE_HOST, \CACHE_PORT));
    }

    /**
     * 获取客户端IP
     *
     * @return string
     */
    public static function getClientIP() {

        if (isset($_SERVER) && isset($_SERVER["REMOTE_ADDR"])) {
            $realip = $_SERVER["REMOTE_ADDR"];
        } else {
            $realip = \getenv("REMOTE_ADDR");
        }
        return \addslashes($realip);
    }

    public static function setSign($userId) {
        $token = uniqid();
        $cacheHelper = helper\CacheHelper::getInstance(NET_CACHE_TYPE, CACHE_PX, CACHE_PCONNECT);
        $key = "{$userId}_t_".PROJECT_NAME;
        if($cacheHelper->set($key, $token)) {
            return $token;
        }

        throw new \Exception("token set error");

    }
    public static function checkSign($userId, $token) {
        $cacheHelper = helper\CacheHelper::getInstance(NET_CACHE_TYPE, CACHE_PX, CACHE_PCONNECT);
        $key = "{$userId}_t_".PROJECT_NAME;
        $realToken = $cacheHelper->get($key);
        return $realToken === $token;
    }

    public static function safeParams($params) {
        if (\is_array($params)) {
            foreach ($params as $key => $val) {
                $params[$key] = self::safeParams($val);
            }
            return $params;
        } else {
            return \strip_tags(trim(\str_replace(array("\n", "\r"), array("", ""), \urldecode($params))));
        }
    }

    public static function mkDirs($dir, $mode = 0755) {
        if (\is_dir($dir) || \mkdir($dir, $mode, true))
            return true;
        if (!self::mkDirs(\dirname($dir), $mode))
            return false;
        return \mkdir($dir, $mode);
    }



}
