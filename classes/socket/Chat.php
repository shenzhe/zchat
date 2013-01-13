<?php
namespace socket;
class Chat
{
    public $chat_unames;
    public $chat_client;
    private $helpMsg = "
    =======================\r\n
    欢迎来到泽泽聊天室\r\n
    设置呢称：/setname 呢称\r\n
    退出聊天室：/quit\r\n
    获取在线列表：/getuser\r\n
    获取帮助：/help
    ====================\r\n
    ";

    public function log($msg) {
        echo $msg."\r\n";
    }


    public function sendMsg($msg, $from, $to=0, $client_id=null)
    {
		$msg=$this->chat_unames[$client_id]." 说: ".$msg."\r\n";
        $data['from'] = $from;
        $data['to'] = $to;
        $data['msg'] = $msg;
        $data['type'] = 'msg';
        $send = json_encode($data);
        if($to==0) $this->server->sendAll($client_id, $msg);
        else $this->server->send($client_id, $msg);
    }
    public function sysNotice($msg, $client_id = null)
    {
		$msg="系统信息\r\n".$msg."\r\n";
        $data['msg'] = $msg;
        $data['type'] = 'sys';
        $send = json_encode($data);
        if($client_id == null) {
            $this->server->sendAll($client_id,$msg);
        } else {
            $this->server->send($client_id, $msg);
        }
    }

    public function onRecive($client_id, $data)
    {
        $data = trim($data);
        $this->log($client_id.$data);
        $msg = explode(' ',$data,3);
		if($msg[0]=='/setname')
        {
            $uname = $msg[1];
            foreach($this->chat_unames as $_uname) {
                if($_uname == $uname) {
                    $this->sysNotice('此名字已存在', $client_id);
                    return ;
                }
            }
            if(isset($this->chat_unames[$client_id]))
            {
                $this->sysNotice($this->chat_unames[$client_id]."名字变更为：{$uname}");
            }
            else
            {
                $this->sysNotice("{$uname}来到了聊天室");
            }
            $this->chat_unames[$client_id] = $uname;

            return;
        } elseif($msg[0]=='/quit')
        {
            $this->server->close($client_id);
        }

        if(empty($this->chat_unames[$client_id])) {
            $this->sysNotice("请设置用户名", $client_id);
            return;
        }

        if($msg[0]=='/sendto')
        {
            $to = (int)$msg[1];
            $from = array_search($client_id,$this->chat_client);
            $content = ($msg[2]);
            if(isset($this->chat_client[$to])) $this->sendMsg($content,$from,$to,$client_id);
            else $this->server->send($client_id,"user is exists\r\n");
        }
        elseif($msg[0]=='/getuser')
        {
            $userList = "";
            foreach($this->chat_unames as $uname) {
                $userList.=$uname."\r\n";
            }
            $this->sysNotice($userList, $client_id);
        }
        elseif($msg[0]=='/help')
        {
            $this->sysNotice($this->helpMsg, $client_id);
        }
        else
        {
            $from = array_search($client_id,$this->chat_client);
            $content = trim($data);
            if(!empty($content)) {
                $this->sendMsg($content, $from, 0, $client_id);
            }
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
        $uid = array_search($client_id,$this->chat_client);
        $this->log('client '.$client_id.': logout!');
        if(isset($this->chat_unames[$client_id])) {
            $this->sysNotice($this->chat_unames[$client_id]."退出了聊天室");
            unset($this->chat_unames[$client_id]);
        }
    }

    function onConnect($client_id)
    {
        $this->sysNotice($this->helpMsg, $client_id);
        $this->log($client_id.'connected');
    }
}
