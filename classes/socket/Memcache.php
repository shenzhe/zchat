<?php
namespace socket;
class Memcache
{
    
    private $dataArray;
    private $stats = array(
        'uptime'=>time(),
        'cmd_get'=>0,
        'cmd_set'=>0,
        'get_hits'=>0,
        'get_misses'=>0,
    );

    public function log($msg) {
        echo $msg."\r\n";
    }


    public function onRecive($client_id, $data)
    {
        $ca = explode("\r\n", $data);
        $commands = explode(" ", $ca[0]);
        $cmd = $commands[0];
        switch($cmd) {
            case 'get':
                $this->stats['cmd_get'] ++;
                $returnData = isset($this->dataArray[$commands[1]]) ? $commands[1]." ".$this->dataArray[$commands[1]] : "$commands[1] 0 0\r\n\r\n";
                
                $this->server->send($client_id, "VALUE {$returnData}");
                $this->server->send($client_id, "END\r\n");
                break;
            case 'getMulti':
                break;
            case 'set':
                $this->stats['cmd_set'] ++;
                $this->dataArray[$commands[1]] = $commands[2]." ".$commands[4]." ".substr($data, strpos($data, "\r\n"));
                $this->server->send($client_id, "STORED\r\n");
                break;
            case 'setMulti':
                break;
            case 'add':
                break;
            case 'delete':
                $this->stats['delete'] ++;
                if(isset($this->dataArray[$commands[1]])) {
                    unset($this->dataArray[$commands[1]]);
                    $this->server->send($client_id, "DELETED\r\n");
                } else {
                    $this->server->send($client_id, "NOT_FOUND\r\n");
                }
                break;
            case 'stats':
                $sendData = "";
                foreach($this->stats as $key=>$val) {
                    if('uptime' == $key) {
                        $sendData.= "STAT {$key} ".(time() - $val)."\r\n";
                    } elseif('time' == $key) {
                        $sendData.= "STAT {$key} ".time()."\r\n";
                    } else {
                        $sendData.= "STAT {$key} {$val}\r\n";
                    }
                }
                $this->server->send($client_id, $sendData);
                $this->server->send($client_id, "END\r\n");
                break;
        }
    }

    public function onStart()
    {
        $this->log('chat server start');
    }

    public function onShutdown()
    {
        $this->log('chat server stop');
    }

    public function onClose($client_id)
    {
        $this->log('client '.$client_id.': logout!');
    }

    function onConnect($client_id)
    {
        $this->log($client_id.'connected');
    }
}
