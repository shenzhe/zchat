<?php

// -*-coding:utf-8; mode:php-mode;-*-

namespace framework\dispatcher;

/**
 * HTTP请求转发器，IRequestDispacher的一个实现，用于分发HTTP的请求。
 * 当GET或者POST信息包含类似于act=CtrlName.methodName时，将执行CtrlName类的methodName方法。
 * 也可以指定  a=CtrlName&m=methodName ,如果没有指定m参数，将默认为 main
 */
class HTTPRequestDispatcher extends RequestDispatcherBase {

    private $ctrlClassName;
    private $ctrlMethodName;

    public function __construct() {
        if (isset($_REQUEST['act'])) {
            $act = $_REQUEST['act'];
            if (\preg_match('/^([a-z_]+)\.([a-z_]+)$/i', $act, $arr)) {
                $this->ctrlClassName = \ucfirst($arr[1]) . 'Ctrl';
                $this->ctrlMethodName = $arr[2];
            }
        } else {
            $this->ctrlClassName = isset($_REQUEST['c']) ? \str_replace('/', '\\', $_REQUEST['c']) : 'IndexCtrl';
            $this->ctrlMethodName = isset($_REQUEST['m']) ? $_REQUEST['m'] : 'main';
        }
    }

    public function getCtrlClassName() {
        return $this->ctrlClassName;
    }

    public function getCtrlMethodName() {
        return $this->ctrlMethodName;
    }

}