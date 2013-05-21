<?php

namespace socket;

use framework\util\Formater;
use common\Utils;
use framework\socket\CFactory;
use framework\fcgi\Client as fcgiClient;

class Swoole implements ISocket
{

    const LOGIN = 1;            //登录
    const LOGIN_SUCC = 2;       //登录成功
    const NEED_LOGIN = 3;       //需要登录

    private $connection = null;

    private function getConnection() {
        if(empty($this->connection)) {
            $obj = manager\RedisManager::getInstance(CACHE_PX, true);
            $this->connection = CFactory::getInstance($obj);
        }

        return $this->connection;
    }

    public function onStart() {
        echo 'server start';
    }

    public function onConnect() {
        $params = func_get_args();
        $fd = $params[1];
        echo "Client {$fd}：Connect\n";
        $this->getConnection()->addFd($fd);
    }

    public function onReceive(){
        $params = func_get_args();
        $data = trim($params[3]);
        $serv = $params[0];
        $fd = $params[1];
        echo "get data {$data} from $fd\n";
        if(empty($data)) {
            return;
        }
        $data = json_decode($data, true);
        if(1 == $data[0]) {
            $uinfo = $this->getConnection()->get($data[1]['uid']);
            if(!empty($uinfo)) {
                $this->sendOne($serv, $uinfo['fd'], [5, []]);
                $this->getConnection()->delete($uinfo['fd'], $data[1]['uid']);
            }
            if(Utils::checkSign($data[1]['uid'], $data[1]['token'])) {
                $this->getConnection()->add($data[1]['uid'], $fd);
                $result = $this->rpc([
                    'act'=>'Index.getUser',
                    'uid'=>$data[1]['uid'],
                    'token'=>$data[1]['token'],
                    'fd'=>$fd,
                    'cmd'=>$data[0]
                ]);
                if(empty($result['code'])) {
                    $this->sendOne($serv, $fd, [2, $result['data']]);  //
                    $this->getConnection()->addFd($fd, $data[1]['uid']);
                } else {
                    $this->sendOne($serv, $fd, [-1, ['code'=>$result['code'], 'msg'=>$result['message']]]);  //
                }
            } else {
                $this->sendOne($serv, $fd, [3, []]);
            }
        } else {
            $pdata = Utils::safeParams($data[1]);
            if(isset($pdata['token']) && $pdata['token'] != ADMIN_TOKEN) {
                $uid = $this->getConnection()->getUid($fd);
                if(empty($uid)) {  //需要登录
                    $this->sendOne($serv, $fd, [4, []]);
                    return;
                }
            }

            switch($data[0]) {
                case 6: //聊天
                    $this->sendToChannel($serv, [6, $pdata]);
                    break;
                case 7:  //heartbeat
                    if(!isset($uid)) {
                        $uid = $this->getConnection()->getUid($fd);
                    }
                    if($this->getConnection()->uphb($uid)) {
                        $this->sendOne($serv, $fd, [7, []]);
                    }
                    break;
                default:
                    $this->sendToChannel($serv, [$data[0], $pdata]);
            }
        }

    }

    public function onClose() {
        $params = func_get_args();
        $fd = $params[1];
        $uid = $this->getConnection()->getUid($fd);
        $this->getConnection()->delete($fd, $uid);
        echo "Client {$fd}: {$uid}: close";
    }

    public function onShutdown() {
        $this->getConnection()->clear();
    }

    public function sendOne($serv, $fd, $data) {
        if(empty($serv) || empty($fd) || empty($data)) {
            return ;
        }
        $data = json_encode($data);
        echo "send {$fd} data={$data}\n";
        return \swoole_server_send($serv, $fd, $data."\0");
    }

    public function sendToChannel($serv, $data, $channel='ALL') {
        $list = $this->getConnection()->getChannel($channel);
        if(empty($list)) {
            return ;
        }

        foreach($list as $fd) {
            $this->sendOne($serv, $fd, $data);
        }
    }

    public function heartbeat() {

    }

    public function hbcheck($serv) {
        $list = $this->getConnection()->getChannel();
        if(empty($list)) {
            return ;
        }

        foreach($list as $uid=>$fd) {
            if(!$this->getConnection()->heartbeat($uid)) {
                $this->sendOne($serv, $fd, [8, []]);
//               $this->getConnection()->delete($fd, $uid);
//                \swoole_server_close($serv, $fd);
            }
        }
    }

    public function onTimer() {
        $params = func_get_args();
        $serv = $params[0];
        $interval = $params[1];
        switch ($interval) {
            case 66:                //heartbeat check
                $this->hbcheck($serv);
                break;
        }

    }

    public function rpc($params) {
        $fcgiClient = new fcgiClient();
        $response = $fcgiClient->request(['query'=>http_build_query($params)]);
        return $response['content'];
    }
}
