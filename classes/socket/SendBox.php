<?php

namespace socket;


class SendBox implements ISocket
{

    public function onStart() {
        echo 'sendbox server start';
    }

    public function onConnect() {
        echo "Client：Connect\n";
    }

    public function onReceive(){
        $params = func_get_args();
        $data = trim($params[3]);
        print_r($params);
        if(empty($data)) {
            return;
        }
        if('<policy-file-request/>' == $data) {
            \swoole_server_send($params[0], $params[1], "<cross-domain-policy>
                    <allow-access-from domain='*' to-ports='*' />
                    </cross-domain-policy>\0");
            echo "send data success\n";
        }else{
            echo $data."\n";
        }

    }

    public function onClose() {
        echo "Client：close";

    }

    public function onShutdown() {
    }


}
