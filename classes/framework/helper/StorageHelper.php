<?php

namespace framework\helper;

/**
 *  存储处理工厂
 *
 */
class StorageHelper {

    private static $cache = array();

    public static function getInstance($type = "Redis", $name = "storage", $pconnect = false) {
        $cacheType = '\\framework\\helper\\storage\\' . $type . 'Helper';
        if (!isset(self::$cache[$type . $name])) {
            self::$cache[$type . $name] = new $cacheType($name, $pconnect);
        }
        return self::$cache[$type . $name];
    }

}
