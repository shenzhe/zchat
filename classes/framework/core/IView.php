<?php

namespace framework\core;

/**
 * 视图接口，视图用于在Controller方法执行之后，反馈或展示处理结果给用户。
 */
interface IView {

    /**
     * 展示视图
     */
    function display();

    /**
     * 获取数据
     */
    function getModel();

    /**
     * 设置数据
     *
     * @param mixed $model
     */
    function setModel($model);
}