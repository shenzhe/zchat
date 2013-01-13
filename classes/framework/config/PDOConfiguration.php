<?php

namespace framework\config;

/**
 * 数据库配置信息
 */
class PDOConfiguration {

    /**
     * 数据库dsn链接
     *
     * @var String
     */
    public $uri;

    /**
     * 数据库用户名
     *
     * @var String
     */
    public $user;

    /**
     * 数据库密码
     *
     * @var String
     */
    public $pass;

    /**
     * 默认编码
     *
     * @var String
     */
    public $charset;

    /**
     * 构造函数
     *
     * @param string $uri
     * @param string $charset
     */
    public function __construct($uri, $user, $pass, $charset) {
        $this->uri = $uri;
        $this->user = $user;
        $this->pass = $pass;
        $this->charset = $charset;
    }

}