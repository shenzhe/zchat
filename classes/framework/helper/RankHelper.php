<?php

namespace framework\helper;

/**
 *  排行榜处理工厂
 *
 */
class RankHelper {

    private static $cache = array();

    public static function getInstance($type = "Redis", $name = "rank", $pconnect = false) {
        $cacheType = '\\framework\\helper\\rank\\' . $type . 'Helper';
        if (!isset(self::$cache[$type . $name])) {
            self::$cache[$type . $name] = new $cacheType($name, $pconnect);
        }
        return self::$cache[$type . $name];
    }

}
