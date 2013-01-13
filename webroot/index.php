<?php
use framework\core\Context;
$rootPath = realpath('..');
require ($rootPath . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR . "framework" . DIRECTORY_SEPARATOR . "setup.php");
Context::setRootPath($rootPath);
$infPath = Context::getRootPath() . DIRECTORY_SEPARATOR . 'inf' . DIRECTORY_SEPARATOR . 'default';
Context::setInfoPath($infPath);
Context::initialize();  //加载inf相关目录下所有文件
if('cli' === PHP_SAPI) {
	$dispatcher = new \framework\dispatcher\ShellRequestDispatcher();
} else {
	$dispatcher = new \framework\dispatcher\HTTPRequestDispatcher();
}

$dispatcher->dispatch();