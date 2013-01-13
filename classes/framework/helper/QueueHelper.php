<?php

namespace framework\helper;

use framework\manager;

/**
 * 队列处理工厂
 *
 */
class QueueHelper {

    private static $cache = array();

    public static function getInstance($type = "Redis", $name = "queue", $pconnect) {
        $cacheType = '\\framework\\helper\\queue\\' . $type . 'Helper';
        if (!isset(self::$cache[$cacheType])) {
            self::$cache[$cacheType] = new $cacheType($name, $pconnect);
        }
        return self::$cache[$cacheType];
    }

}
