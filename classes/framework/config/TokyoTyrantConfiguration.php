<?php

namespace framework\config;

/**
 * TokyoTyrant配置信息
 */
class TokyoTyrantConfiguration {

    /**
     * TokyoTyrant服务器dsn链接
     *
     * @var string
     */
    public $host;
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