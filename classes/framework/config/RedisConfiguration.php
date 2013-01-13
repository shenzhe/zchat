<?php

namespace framework\config;

/**
 * Redis配置信息
 */
class RedisConfiguration {

    /**
     * Redis服务器host
     *
     * @var string
     */
    public $host;

    /**
     * Redis服务器port
     *
     * @var string
     */
    public $port;

    /**
     * 构造函数
     *
     * @param string $uri
     */
    public function __construct($host, $port) {
        $this->host = $host;
        $this->port = $port;
    }

}