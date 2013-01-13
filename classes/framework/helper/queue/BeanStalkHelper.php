<?php

namespace framework\helper\queue;

use framework\manager;

/**
 * beanstalk 队列处理类
 */
class BeanStalkHelper implements IQueue {

    private static $beanstalk;

    public function __construct($name = "", $pconnect = false) {
        if (empty(self::$beanstalk)) {
            self::$beanstalk = manager\BeanStalkManager::getInstance($name, $pconnect);
        }
    }

    public function addQueue($key, $data) {
        return self::$beanstalk->put($key, $data);
    }

    public function getQueue($key) {
        $job =  self::$beanstalk->reserve($key);
        self::$beanstalk->delete($job['id'], $key);
        return $job;
    }

}
