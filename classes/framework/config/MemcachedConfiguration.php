<?php

namespace framework\config;

/**
 * Memcached配置
 */
class MemcachedConfiguration {

    /**
     * Memcached服务器地址
     *
     * @var string
     */
    public $host;

    /**
     * Memcached服务器端口
     *
     * @var int
     */
    public $port;

    /**
     * 构造函数
     *
     * @param string $host
     * @param int $port
     */
    public function __construct($host, $port) {
        $this->host = $host;
        $this->port = $port;
    }

}