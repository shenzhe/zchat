<?php

namespace framework\manager;

use \Memcached;
use framework\config\MemcachedConfiguration;

/**
 * Memcached管理工具，用于管理Memcached对象的工具。
 */
class MemcachedManager {

    /**
     * Memcached配置
     *
     * @var <MemcachedConfiguration>array
     */
    private static $configs;

    /**
     * Memcached实例
     *
     * @var \Memcached
     */
    private static $instance;

    /**
     * 添加Memcached配置
     *
     * @param MemcachedConfiguration $config
     */
    public static function addConfigration($name, MemcachedConfiguration $config) {
        self::$configs[$name][] = $config;
    }

    /**
     * 获取Memcached实例
     *
     * @return \Memcached
     */
    public static function getInstance($name = "", $pconnect = false) {

        if (empty(self::$instance[$name])) {
            if (empty(self::$configs[$name])) {
                return null;
            }

            if ($pconnect) {
                $memcached = new Memcached($name);
            } else {
                $memcached = new Memcached();
            }

            foreach (self::$configs[$name] as $config) {
                $memcached->addServer($config->host, $config->port);
            }

            self::$instance = $memcached;
        }

        return self::$instance;
    }

}