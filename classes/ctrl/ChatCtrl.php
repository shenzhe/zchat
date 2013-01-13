<?php

namespace ctrl;

use common,
    React;

/**
 * 聊天服务
 *
 * @package       UserCtrl
 * @subpackage    CtrlBase
 */
class ChatCtrl extends CtrlBase
{

    private $names = [];

    public function start() {
        $loop = \React\EventLoop\Factory::create();
        $socket = new \React\Socket\Server($loop);

        $conns = new \SplObjectStorage();

        $that = $this;

        $socket->on('connection', function ($conn) use ($conns, $that) {
            echo $conn->getClientAddress()."connected".PHP_EOL;
            $conns->attach($conn);
            $conn->on('data', function ($datas) use ($conns, $conn, $that) {
                $datas = \trim($datas);
                echo $datas.PHP_EOL;
                if(empty($datas)) {
                    return ;
                }
                if('<policy-file-request/>' == $datas) {
                    echo 'send policy file'.PHP_EOL;
                    $conn->write("<cross-domain-policy>
                    <allow-access-from domain='*.yilekongjian.com' to-ports='8991' />
                    </cross-domain-policy>\0");
                    $conn->end();
                    return;
                }

                list($cmd, $data) = \explode("||", $datas);

                switch ($cmd) {
                    case 'quit':
                        $conns->detach($conn);
                        $conn->end();
                        $msg = [
                            'type'=>'userloginout',
                            'from'=>$conn->getClientAddress(),
                            'name'=>$this->getName($conn)
                        ];
                        $conn->write("loginout\0");
                        $that->boardcast($conns, $conn, \json_encode($msg));
                        $that->removeUser($conn);
                        break;
                    case 'setname':
                        $that->setname($conn, $data);
                        $msg = [
                            'type'=>'newuser',
                            'from'=>$conn->getClientAddress(),
                            'name'=>$this->getName($conn)
                        ];
                        $conn->write("loginsuccess\0");
                        $that->boardcast($conns, $conn, \json_encode($msg));
                        break;
                    case 'getusers':
                        $that->getList($conn);
                        break;
                    case 'to':
                        break;
                    default:
                        $msg = [
                            'type'=>'msg',
                            'from'=>$conn->getClientAddress(),
                            'to'=>0,
                            'msg' =>$cmd,
                        ];
                        $that->boardcast($conns, $conn, \json_encode($msg));
                }
            });

            $conn->on('end', function () use ($conns, $conn, $that) {
                if(!isset($that->names[$conn->getClientAddress()])) {
                    return ;
                }
                $conns->detach($conn);
                $msg = [
                    'type'=>'userloginout',
                    'from'=>$conn->getClientAddress(),
                    'name'=>$this->getName($conn)
                ];
                $conn->write("loginout\0");
                $that->boardcast($conns, $conn, \json_encode($msg));
                $that->removeUser($conn);
            });
        });

        $port = PORT;
        $host = HOST;
        echo "Socket server listening on port {$port}.\n";
        echo "You can connect to it by running: telnet {$host} {$port}\n";

        $socket->listen($port, $host);
        $loop->run();
    }

    private function setName($conn, $name) {
        $this->names[$conn->getClientAddress()] = $name;
    }

    private function getName($conn) {
        return $this->names[$conn->getClientAddress()];
    }

    private function boardcast($conns, $conn, $data) {
        echo "boardcast: {$data}".PHP_EOL;
        if(empty($data)) {
            return ;
        }
        $username = $this->getName($conn);
        if(empty($username)) {
            $conn->write("请设置用户名.(setname||你的名字)\0");
            return ;
        }
        foreach ($conns as $current) {
            if($conn === $current) {
                continue;
            }
            $current->write("{$data}\0");
        }
    }

    private function getList($conn) {
        $names = [
            'type'=>'userlist',
            'userlist' =>$this->names,
        ];
        $names = \json_encode($names);
        echo "getlist: {$names}".PHP_EOL;

        $conn->write($names."\0");
    }

    private function sendTo($conn, $to, $data) {
        $to->write($this->getName($conn)."对你说:".$data."\0");
    }

    private function removeUser( $conn) {
        unset($this->names[$conn->getClientAddress()]);
    }

    public function stop() {
        $deamon = new \framework\util\Daemon($GLOBALS['DAEMON_CONFIG']);
        $deamon->stop();
    }

}