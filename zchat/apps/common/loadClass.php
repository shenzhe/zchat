<?php

namespace common;

use ZPHP\Core\Factory,
    ZPHP\Core\Config as ZConfig,
    ZPHP\Rank\Factory as RFactory,
    service\Pay\PayService;

/**
 * 获取class实例的工具类
 *
 * @package service
 *
 */
class loadClass
{

    public static function getService($service)
    {
        return Factory::getInstance("service\\{$service}");
    }

    public static function getDao($dao)
    {
        return Factory::getInstance("dao\\{$dao}");
    }
}
