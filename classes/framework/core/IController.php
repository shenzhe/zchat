<?php

namespace framework\core;

/**
 * 控制器接口，控制器直接接收处理来自用户的请求，并且将处理结果交给View进行反馈给用户
 */
interface IController {

    /**
     * 设置请求分发器
     * @param IRequestDispatcher $dispatcher
     */
    function setDispatcher(IRequestDispatcher $dispatcher);

    /**
     * 在执行控制器方法之前执行的过滤方法，该方法返回布尔类型值，当返回结果为true时，继续执行请求的方法，当返回结果为false时，终端请求的执行。
     * 该方法可用于用户认证或者请求加锁等。
     * @return boolean
     */
    function beforeFilter();

    /**
     * 在执行控制器方法之后执行，无论beforeFilter是否返回为true，该方法都会得到执行。
     * 该方法可用于日志记录或者请求解锁等。
     * @see beforeFilter();
     */
    function afterFilter();
}