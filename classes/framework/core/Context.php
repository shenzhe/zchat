<?php

namespace framework\core;

use framework\util;

/**
 * 框架的上下文，司整个框架的配置信息及初始化信息
 */
class Context {

    private static $rootPath;
    private static $defaultInfoPath = "default";
    private static $infoPath;
    private static $classesRoot;
    private static $ctrlNamespace = "ctrl";

    /**
     * 取得项目的配置信息路径
     * @return String
     * @see setInfozPath()
     */
    public static function getInfoPath() {
        if (empty(self::$infoPath)) {
            self::$infoPath = self::getRootPath() . DIRECTORY_SEPARATOR . "inf";
        } else if (!is_dir(self::$infoPath)) {
            self::$infoPath = self::getRootPath() . DIRECTORY_SEPARATOR . "inf" . DIRECTORY_SEPARATOR . self::$defaultInfoPath;
        }

        return Context::$infoPath;
    }

    public static function setDefaultInfoPath($defaultPath) {
        self::$defaultInfoPath = $defaultPath;
    }

    /**
     * 设置项目的配置信息路径，所有的配置信息都可以存在在该目录下，框架会自动将该目录及其子目录包含在运行环境中。
     * @param String $infoPath 项目的配置信息路径
     *
     */
    public static function setInfoPath($infoPath) {
        self::$infoPath = $infoPath;
    }

    /**
     * 取得项目的根路径
     * @return String
     * @see setRootPath()
     */
    public static function getRootPath() {
        if (empty(self::$rootPath)) {
            try {
                throw new \Exception("please set root path with Context::setRootPath");
            } catch (\Exception $e) {
                echo $e;
            }
        }

        return self::$rootPath;
    }

    /**
     * 设置项目的根路径
     * @param $rootPath
     * @return String
     *
     */
    public static function setRootPath($rootPath) {
        self::$rootPath = $rootPath;
    }

    /**
     * 取得项目的类定义路径
     * @return String
     * @see setClassesRoot()
     */
    public static function getClassesRoot($classDir = 'classes') {
        if (empty(self::$classesRoot)) {
            self::$classesRoot = self::getRootPath() . DIRECTORY_SEPARATOR . $classDir;
        }

        return self::$classesRoot;
    }

    /**
     * 设置项目的类定义路径，该路径用于存放所有的PHP类的定义。
     * @param $classesRoot
     *
     */
    public static function setClassesRoot($classesRoot) {
        self::$classesRoot = $classesRoot;
    }

    /**
     * 取得Controller的名称空间
     * @return String
     * @see setCtrlNamespace()
     */
    public static function getCtrlNamespace() {
        return self::$ctrlNamespace;
    }

    /**
     * 设置Controller的名称空间，该设定主要用于RequestDispatcher对控制器类的定位
     * @param String $ctrlNamespace
     */
    public static function setCtrlNamespace($ctrlNamespace) {
        self::$ctrlNamespace = $ctrlNamespace;
    }

    /**
     * 初始化上下文
     */
    public static function initialize($fileArray = array()) {
        if (!\defined('ROOT_PATH'))
            \define('ROOT_PATH', self::getRootPath());
        $infoPath = self::getInfoPath();
        if (empty($fileArray)) {

            $infoFiles = util\FileUtil::treeDirectory($infoPath, "/.php$/");

            foreach ($infoFiles as $infoFile) {
                include($infoFile);
            }
        } else {
            foreach ($fileArray as $file) {
                include($infoPath . DIRECTORY_SEPARATOR . $file);
            }
        }
    }

}