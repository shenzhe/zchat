<?php

namespace common;

use framework\util\Singleton;

/**
 * 获取class实例的工具类
 *
 * @package service
 *
 */
class ClassLocator {

    public static function getService($service) {
        return Singleton::get("service\\{$service}Service");
    }

    public static function getDao($dao) {
        return Singleton::get("dao\\{$dao}Dao");
    }

}
