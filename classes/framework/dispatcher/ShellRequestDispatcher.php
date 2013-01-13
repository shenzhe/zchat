<?php


namespace framework\dispatcher;
use framework\util\Daemon;

/**
 * 用于执行控制台脚本的请求转发器。
 */
class ShellRequestDispatcher extends RequestDispatcherBase {

    private $ctrlClassName;
    private $ctrlMethodName;

    public function __construct() {
        $this->defaultAction = 'Shell.main';
        if (isset($_SERVER['argv'])) {
            if(isset($_SERVER['argv'][1])) {
                $act = $_SERVER['argv'][1];
            }

            if("-d" == end($_SERVER['argv'])) {
                $this->daemon = true;
            }

        } else {
            $act = $this->defaultAction;
        }

        if (\preg_match('/^([a-z_]+)\.([a-z_]+)$/i', $act, $arr)) {
            $this->ctrlClassName = $arr[1] . 'Ctrl';
            $this->ctrlMethodName = $arr[2];
        }
    }

    public function dispatch() {
        if($this->daemon) {
            $deamon = new Daemon($GLOBALS['DAEMON_CONFIG']);
            $deamon->start();
        }
        parent::dispatch();
    }

    public function getCtrlClassName() {
        return $this->ctrlClassName;
    }

    public function getCtrlMethodName() {
        return $this->ctrlMethodName;
    }

}