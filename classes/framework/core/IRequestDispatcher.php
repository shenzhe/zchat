<?php

// -*-coding:utf-8; mode:php-mode;-*-

namespace framework\core;

/**
 * 请求分发器，用于将用户的请求分配给Controller类及其方法进行执行处理，
 * 实现该接口主要是实现Controller的类和方法的查找规则。
 */
interface IRequestDispatcher {

    /**
     * 分发请求,该方法实现具体的Controller的执行。
     */
    function dispatch();
}