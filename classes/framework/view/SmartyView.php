<?php

namespace framework\view;

use framework\core\IView;
use framework\config\SmartyConfiguration;

/**
 * 用于用Smarty展示数据
 */
class SmartyView implements IView {

    /**
     * Smarty实例
     *
     * @var \Smarty
     */
    private static $smarty;

    /**
     * Smarty配置实例
     *
     * @var SmartyConfiguration
     */
    private static $configuration;

    /**
     * 模板名称
     *
     * @var string
     */
    private $fileName;

    /**
     * 模板数据
     *
     * @var mixed
     */
    private $model;

    /**
     * 构造函数
     *
     * @param String $fileName Smarty模版文件名
     * @param mixed $model 用于展示的数据
     */
    public function __construct($fileName, $model = null) {
        $this->fileName = $fileName;
        $this->model = $model;
    }

    /**
     * 设置Smarty的配置信息
     *
     * @param SmartyConfiguration $config
     */
    public static function setConfiguration(SmartyConfiguration $config) {
        self::$configuration = $config;
    }

    /**
     * 取得Smarty的配置信息
     *
     * @return SmartyConfiguration
     */
    public static function getConfiguration() {
        return self::$configuration;
    }

    /**
     * 获取Smarty实例
     *
     * @return \Smarty
     */
    private static function getSmarty() {
        if (!self::$smarty) {
            if (empty(self::$configuration)) {
                throw new \Exception("please set smarty configuration with  SmartyView::setConfiguration()");
            }

            include(self::$configuration->smartyPath . DIRECTORY_SEPARATOR . 'Smarty.class.php');

            $smarty = new \Smarty();
            $smarty->cache_dir = self::$configuration->cacheDir;
            $smarty->compile_dir = self::$configuration->compileDir;
            $smarty->template_dir = self::$configuration->templateDir;
            $smarty->config_dir = self::$configuration->configDir;
            $smarty->left_delimiter = '<{';
            $smarty->right_delimiter = '}>';

            self::$smarty = $smarty;
        }

        return self::$smarty;
    }

    /**
     * 获取数据
     *
     * @return mixed
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * 设置数据
     *
     * @param mixed $model
     */
    public function setModel($model) {
        return $this->model = $model;
    }

    /**
     * 展示视图
     *
     */
    public function display() {
        header("Content-Type: text/html; charset=utf-8");

        $this->output();
    }

    /**
     * 输出
     *
     */
    public function output() {
        $smarty = self::getSmarty();
        $smarty->assign($this->model);
        $smarty->display($this->fileName);
    }

}