<?php

namespace framework\view;

use framework\core\IView;

/**
 * 字符串视图，向用户输出字符串
 */
class StringView implements IView {

    private $string;

    public function __construct($string) {
        $this->string = $string;
    }

    /**
     * 获取数据
     *
     * @return mixed
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * 设置数据
     *
     * @param mixed $model
     */
    public function setModel($model) {
        return $this->model = $model;
    }

    /**
     * 展示视图
     *
     */
    public function display() {
        header("Content-Type:text/plain; charset=utf-8");
        echo $this->string;
    }

}