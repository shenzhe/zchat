<?php

namespace framework\helper\cache;

/**
 *  cache 接口，申明了必需的一些方法
 */
interface ICacheHelper {

    function enable();

    function add($key, $value);

    function set($key, $value);

    function get($key);

    function delete($key);

    function increment($key, $step = 1);

    function decrement($key, $step = 1);
}