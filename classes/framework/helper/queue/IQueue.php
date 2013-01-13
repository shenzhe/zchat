<?php

namespace framework\helper\queue;

/**
 * queue 接口
 */
interface IQueue {

    public function addQueue($key, $data);
    public function getQueue($key);
}
