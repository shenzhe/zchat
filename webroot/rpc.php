<?php
use common\Utils;
use framework\core\Context;
use framework\dispatcher\RpcRequestDispatcher;
$rootPath = \realpath('..');
require ($rootPath . DIRECTORY_SEPARATOR . "framework" . DIRECTORY_SEPARATOR . "setup.php");
Context::setRootPath($rootPath);
$infPath = Context::getRootPath() . DS . 'inf' . DS . $_SERVER['HTTP_HOST'];
Context::setInfoPath($infPath);
Context::initialize();  //加载inf相关目录下所有文件
$_REQUEST = Utils::safeParams($_REQUEST);
Utils::initConfig();    //加载配置
$service = new \Yar_Server(new RpcRequestDispatcher());
$service->handle();