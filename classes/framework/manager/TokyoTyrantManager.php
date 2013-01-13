<?php

namespace framework\manager;

use framework\config\TokyoTyrantConfiguration;

/**
 * TokyoTyrant管理工具，用于管理TokyoTyrant对象的工具。
 */
class TokyoTyrantManager {

    /**
     * TokyoTyrant配置
     *
     * @var <TokyoTyrantConfiguration>array
     */
    private static $configs;

    /**
     * TokyoTyrant实例
     *
     * @var <\TokyoTyrant>array
     */
    private static $instances;

    /**
     * 添加TokyoTyrant配置
     *
     * @param string $name
     * @param TokyoTyrantConfiguration $config
     */
    public static function addConfigration($name, TokyoTyrantConfiguration $config) {
        self::$configs[$name] = $config;
    }

    /**
     * 获取TokyoTyrant实例
     *
     * @param string $name
     * @return \TokyoTyrant
     */
    public static function getInstance($name) {
        if (empty(self::$instances[$name])) {
            if (empty(self::$configs[$name])) {
                return null;
            }

            $config = self::$configs[$name];

            $tokyoTyrant = new \Memcached();
            $tokyoTyrant->addServer($config->host, $config->port);

            self::$instances[$name] = $tokyoTyrant;
        }

        return self::$instances[$name];
    }

}