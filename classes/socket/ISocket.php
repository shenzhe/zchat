<?php
namespace socket;


interface ISocket {
    public function onStart();
    public function onConnect();
    public function onReceive();
    public function onClose();
    public function onShutdown();
}