<?php

namespace socket;


class Client
{
    private $sockfp = null;

    public function __construct($host, $port)
    {
        if (!function_exists('fsockopen'))
        {
            die('<fsockopen> does not support' . PHP_EOL);
        }

        $this->sockfp = fsockopen($host, $port);

        return;
    }

    public function __destruct()
    {
        if($this->sockfp) {
            fclose($this->sockfp);
            $this->sockfp = null;
        }
    }

    public function disconnect()
    {
        if($this->sockfp) {
            fclose($this->sockfp);
            $this->sockfp = null;
        }
    }

    public function send($data) {
        if($this->sockfp) {
            return fwrite($this->sockfp, $data);
        }
        return false;
    }

    public function read() {
        if($this->sockfp) {
            $ret = '';
            while (!feof($this->sockfp))
            {
                $ret .= fread($this->sockfp, 8192);
            }
            return $ret;
        }
        return false;
    }


}
