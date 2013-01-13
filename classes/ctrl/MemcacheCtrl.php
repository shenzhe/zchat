<?php

namespace ctrl;

use framework\dispatcher\ShellRequestDispatcher;

class MemcacheCtrl
{

    /**
     *
     * 验证过滤
     * @return boolean
     * @throws common\GameException
     */
    public function beforeFilter()
    {
        if ($this->dispatcher instanceof ShellRequestDispatcher) {
            return true;
        }

        throw new \Exception('forbidden');
    }



    public function start()
    {
        $socket = new \framework\socket\Socket(HOST, PORT);
        $socket->setProtocol(new \socket\Memcache());
        $socket->run();
    }

    public function newstart() {
        $loop = \React\EventLoop\Factory::create();
        $socket = new \React\Socket\Server($loop);

        $conns = new \SplObjectStorage();
        static $dataArray = array();
        $socket->on('connection', function ($conn) use ($conns, &$dataArray) {
            echo $conn->getClientAddress()."connection\n";
            $conns->attach($conn);
            $conn->on('data', function ($data) use ($conns, $conn, &$dataArray) {
                if('quit' == trim($data)) {
                    $conns->detach($conn);
                    $conn->end();
                    return;
                }
                $ca = explode("\r\n", $data);
                $commands = explode(" ", $ca[0]);
                switch($commands[0]) {
                    case 'get':
                        $returnData = isset($dataArray[$commands[1]]) ? $commands[1]." ".$dataArray[$commands[1]] : "$commands[1] 0 0\r\n\r\n";
                        $conn->write("VALUE {$returnData}");
                        $conn->write("END\r\n");
                        break;
                    case 'getMulti':
                        break;
                    case 'set':
                        $dataArray[$commands[1]] = $commands[2]." ".$commands[4]." ".substr($data, strpos($data, "\r\n"));
                        $conn->write("STORED\r\n");
                        break;
                    case 'setMulti':
                        break;
                    case 'add':
                        break;
                    case 'delete':
                        if(isset($dataArray[$commands[1]])) {
                            unset($dataArray[$commands[1]]);
                            $conn->write("DELETED\r\n");
                        } else {
                            $conn->write("NOT_FOUND\r\n");
                        }
                        break;
                }
            });
            $conn->on('end', function () use ($conns, $conn, $dataArray) {
                $conns->detach($conn);
                echo $conn->getClientAddress()."disconnect\n";
            });
        });

        $port = PORT;
        $host = HOST;
        echo "Socket server listening on port {$port}.\n";
        echo "You can connect to it by running: telnet {$host} {$port}\n";

        $socket->listen($port, $host);
        $loop->run();
    }


}
