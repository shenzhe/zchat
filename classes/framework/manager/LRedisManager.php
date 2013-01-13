<?php

namespace framework\manager;

use framework\config\RedisConfiguration;

/**
 * redis-stroage管理工具，用于管理Redis对象的工具。
 *
 * redis-stroage 是一个支持leveldb做为持久存储的redis增强版本
 * redis-stroage获取：https://github.com/qiye/redis-storage
 * 如要支持redis-stroage，需要使用增强版本phpredis扩展
 * 增强版本phpredis扩展地址：https://github.com/shenzhe/phpredis
 */
class LRedisManager {

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

            if ($pconnect) {
                $redis = \phpiredis_pconnect($config->host, $config->port, 5, $name);
            } else {
                $redis = \phpiredis_connect($config->host, $config->port, 5);
            }
            //$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
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

            \phpiredis_disconnect($redis);
        }

        return true;
    }

}