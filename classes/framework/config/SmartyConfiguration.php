<?php

namespace framework\config;

/**
 * Smarty配置信息
 */
class SmartyConfiguration {

    /**
     * Smarty框架的路径
     *
     * @var String
     */
    public $smartyPath;

    /**
     * Smarty缓存目录
     *
     * @var String
     */
    public $cacheDir;

    /**
     * Smarty编译目录
     *
     * @var String
     */
    public $compileDir;

    /**
     * Smarty模板目录
     *
     * @var String
     */
    public $templateDir;

    /**
     * Smarty配置目录
     *
     * @var String
     */
    public $configDir;

    /**
     * 构造函数
     *
     * @param string $smartyPath
     * @param string $cacheDir
     * @param string $compileDir
     * @param string $templateDir
     * @param string $configDir
     */
    public function __construct($smartyPath, $cacheDir, $compileDir, $templateDir, $configDir) {
        $this->smartyPath = $smartyPath;
        $this->cacheDir = $cacheDir;
        $this->compileDir = $compileDir;
        $this->templateDir = $templateDir;
        $this->configDir = $configDir;
    }

}