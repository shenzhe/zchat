<?php

namespace framework\helper;

/**
 *  cache处理工厂
 *
 */
class CacheHelper {

    private static $cache = array();

    public static function getInstance($cacheType = 'Apc', $name = "cache", $pconnect = false) {
        $cacheType = '\\framework\\helper\\cache\\' . $cacheType . 'Helper';
        if (!isset(self::$cache[$cacheType])) {
            self::$cache[$cacheType] = new $cacheType($name, $pconnect);
        }
        return self::$cache[$cacheType];
    }

}
