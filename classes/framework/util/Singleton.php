<?php

// -*-coding:utf-8; mode:php-mode;-*-

namespace framework\util;

/**
 * 单例管理类，用于通过类名获取该类的一个实例，并且保证在一次PHP的运行周期里只创建一次该实例
 */
class Singleton {

    private static $instances = array();

    /**
     * 根据类名获取该类的单例
     * @param String $className 类名
     * @return Object $className类的单独实例
     */
    public static function get($className) {
        if (isset(self::$instances[$className])) {
            return self::$instances[$className];
        }

        $instance = new $className();
        self::$instances[$className] = $instance;

        return $instance;
    }

}