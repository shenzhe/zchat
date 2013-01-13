<?php

namespace framework\config;

/**
 * beanstalkd的队列配置
 */
class BeanStalkConfiguration {
    public $host;
    public $port;
    public function __construct($host, $port) {
        $this->host = $host;
        $this->port = $port;
    }

}