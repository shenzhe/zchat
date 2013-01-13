<?php

namespace framework\helper\storage;

/**
 *  存储接口
 */
interface IStorage {

    //设置从库
    public function setSlave($name);

    public function getMutilMD($userId, $keys);

    public function getMD($userId, $key, $slaveName = "");

    public function getSD($userId, $key, $slaveName = "");

    public function setMD($userId, $key, $data);

    public function setMultiMD($userId, $keys);

    public function del($userId, $key);

    public function setExpire($userId, $key, $time);

    public function close();
}
