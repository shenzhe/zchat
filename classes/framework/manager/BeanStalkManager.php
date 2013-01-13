<?php

namespace framework\manager;

use \Beanstalk;
use framework\config\BeanStalkConfiguration;

/**
 * beanstalk管理类
 * 需要 beanstalk支持
 * beanstalk: https://github.com/kr/beanstalkd
 * php扩展：https://github.com/nil-zhang/php-beanstalk/
 */
class BeanStalkManager {

    private static $configs;

    private static $instance;
    public static function addConfigration($name, BeanStalkConfiguration $config) {
        self::$configs[$name][] = $config;
    }

    public static function getInstance($name = "", $pconnect=false) {

        if (empty(self::$instance[$name])) {
            if (empty(self::$configs[$name])) {
                return null;
            }

            $beanstalk = new Beanstalk();

            foreach (self::$configs[$name] as $config) {
                $beanstalk->addServer($config->host, $config->port);
            }

            self::$instance = $beanstalk;
        }

        return self::$instance;
    }

}