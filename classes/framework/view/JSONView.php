<?php


namespace framework\view;

use framework\core\IView;

/**
 * 用于生成JSON数据
 */
class JSONView implements IView {

    private $model;

    /**
     * 数据模型，即需要展示的数据
     *
     * @param mixed $model
     */
    public function __construct($model) {
        $this->model = $model;
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
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->model);
    }

}