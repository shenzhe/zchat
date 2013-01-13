<?php

namespace framework\socket;

/**
 *  socket 类
 *  本socket需要pcntl 和 libevent支持
 */
class Socket {
    public $protocol;
    public $host = '0.0.0.0';
    public $port;
    public $timeout;
    public $buffer_size = 8192;
    public $write_buffer_size = 2097152;
    public $server_block = 0; //0 block,1 noblock
    public $client_block = 0; //0 block,1 noblock
    public $base_event;
    public $server_event;
    public $server_sock;
    //最大连接数
    public $max_connect = 3000;
    //客户端socket列表
    public $client_sock = array();
    //客户端数量
    public $client_num = 0;

    public function __construct($host, $port, $timeout = 30) {
        if (!\extension_loaded('pcntl')) {
            throw new \Exception("Require pcntl extension!");
        }
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    private function init() {
        $this->base_event = \event_base_new();
        $this->server_event = \event_new();
    }

    public function setProtocol($protocol) {
        $this->protocol = $protocol;
        $this->protocol->server = $this;
    }

    private function create($uri, $block = 0) {
        $socket = \stream_socket_server($uri, $errno, $errstr);

        if (!$socket) {
            throw new \Exception($errno . $errstr);
        }
        //设置socket为非堵塞或者阻塞
        \stream_set_blocking($socket, $block);
        return $socket;
    }

    public function accept() {
        $client_socket = \stream_socket_accept($this->server_sock);
        $client_socket_id = (int) $client_socket;
        \stream_set_blocking($client_socket, $this->client_block);
        $this->client_sock[$client_socket_id] = $client_socket;
        $this->client_num++;
        if ($this->client_num > $this->max_connect) {
            $this->_closeSocket($client_socket);
            return false;
        } else {
            //设置写缓冲区
            \stream_set_write_buffer($client_socket, $this->write_buffer_size);
            return $client_socket_id;
        }
    }

    /**
     * 运行服务器程序
     * @return unknown_type
     */
    public function run($num = 1) {
        $this->init();
        //建立服务器端Socket
        $this->server_sock = $this->create("tcp://{$this->host}:{$this->port}");

        //设置事件监听，监听到服务器端socket可读，则有连接请求
        \event_set($this->server_event, $this->server_sock, EV_READ | EV_PERSIST, '\\framework\\socket\\Socket::server_handle_connect', $this);
        \event_base_set($this->server_event, $this->base_event);
        \event_add($this->server_event);
        if ($num > 1) {
            for ($i = 1; $i < $num; $i++) {
                $pid = \pcntl_fork();
                if ($pid) {
                    
                } else {
                    break;
                }
            }
        }
        $this->protocol->onStart();
        \event_base_loop($this->base_event);
    }

    /**
     * 向client发送数据
     * @param $client_id
     * @param $data
     * @return unknown_type
     */
    public function _send($client_id, $data) {
        $length = \strlen($data);
        for ($written = 0; $written < $length; $written += $fwrite) {
            $fwrite = \stream_socket_sendto($client_id, substr($data, $written));
            if ($fwrite <= 0 or $fwrite === false) {
                return $written;
            }
        }
        return $written;
    }

    public function send($cilent_id, $data) {
        if (isset($this->client_sock[$cilent_id])) {
            return $this->_send($this->client_sock[$cilent_id], $data);
        }
    }

    /**
     * 向所有client发送数据
     * @return unknown_type
     */
    public function sendAll($client_id, $data) {
        foreach ($this->client_sock as $k => $sock) {
            if ($client_id and $k == $client_id) {
                continue;
            }
            $this->_send($sock, $data);
        }

        return TRUE;
    }

    /**
     * 关闭服务器程序
     * @return unknown_type
     */
    public function shutdown() {
        //关闭所有客户端
        foreach ($this->client_sock as $k => $sock) {
            $this->_closeSocket($sock, $this->client_event[$k]);
        }
        //关闭服务器端
        $this->_closeSocket($this->server_sock, $this->server_event);
        //关闭事件循环
        \event_base_loopexit($this->base_event);
        $this->protocol->onShutdown();
    }

    private function _closeSocket($socket, $event = null) {
        if ($event) {
            \event_del($event);
            \event_free($event);
        }
        \stream_socket_shutdown($socket, STREAM_SHUT_RDWR);
        \fclose($socket);
    }

    /**
     * 关闭某个客户端
     * @return unknown_type
     */
    public function close($client_id) {
        $this->_closeSocket($this->client_sock[$client_id], $this->client_event[$client_id]);
        unset($this->client_sock[$client_id], $this->client_event[$client_id]);
        $this->protocol->onClose($client_id);
        $this->client_num--;
    }

    public static function server_handle_connect($server_socket, $events, $server) {
        if ($client_id = $server->accept()) {
            $client_socket = $server->client_sock[$client_id];
            //新的事件监听，监听客户端发生的事件
            $client_event = event_new();
            event_set($client_event, $client_socket, EV_READ | EV_PERSIST, "\\framework\\socket\\Socket::server_handle_receive", array($server, $client_id));
            //设置基本时间系统
            event_base_set($client_event, $server->base_event);
            //加入事件监听组
            event_add($client_event);
            $server->client_event[$client_id] = $client_event;
            $server->protocol->onConnect($client_id);
        }
    }

    /**
     * 接收到数据后进行处理
     * @param $client_socket
     * @param $events
     * @param $arg
     * @return unknown_type
     */
    public static function server_handle_receive($client_socket, $events, $arg) {
        $server = $arg[0];
        $client_id = $arg[1];
        $data = self::fread_stream($client_socket, $server->buffer_size);

        if ($data !== false) {
            $server->protocol->onRecive($client_id, $data);
        } else {
            $server->close($client_id);
        }
    }

    private static function fread_stream($fp, $length) {

        //return stream_socket_recvfrom($fp, $length);
        $data = false;
        while ($buf = stream_socket_recvfrom($fp, $length)) {
            $data .= $buf;
            if (strlen($buf) < $length)
                break;
        }
        return $data;
    }

}