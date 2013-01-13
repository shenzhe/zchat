<?php

namespace framework\manager;

use framework\config\RedisConfiguration;
use \Redis;

/**
 * Redis管理工具，用于管理Redis对象的工具。
 */
class RedisManager {

    /**
     * Redis配置
     *
     * @var <RedisConfiguration>array
     */
    private static $configs;

    /**
     * Redis实例
     *
     * @var <\Redis>array
     */
    private static $instances;

    /**
     * 添加Redis配置
     *
     * @param string $name
     * @param RedisConfiguration $config
     */
    public static function addConfigration($name, RedisConfiguration $config) {
        self::$configs[$name] = $config;
    }

    /**
     * 获取Redis实例
     *
     * @param string $name
     * @return \Redis
     */
    public static function getInstance($name, $pconnect) {
        if (empty(self::$instances[$name])) {
            if (empty(self::$configs[$name])) {
                return null;
            }

            $config = self::$configs[$name];

            $redis = new Redis();
            if ($pconnect) {
                $redis->pconnect($config->host, $config->port, 5, $name);
            } else {
                $redis->connect($config->host, $config->port, 5);
            }
            $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
            self::$instances[$name] = $redis;
        }

        return self::$instances[$name];
    }

    /**
     *
     * 手动关闭链接
     * @return boolean
     */
    public static function closeInstance() {
        if (empty(self::$instances)) {
            return true;
        }

        if (\defined('CACHE_PCONNECT') && \CACHE_PCONNECT) {
            return true;
        }

        foreach (self::$instances as $redis) {

            $redis->close();
        }

        return true;
    }

}