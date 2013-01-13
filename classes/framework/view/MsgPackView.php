<?php


namespace framework\view;

use framework\core\IView;

/**
 * 用于生成msgpack数据
 */
class MsgpackView implements IView {

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
        header("Content-Type: application/octet-stream; charset=utf-8");
        echo \msgpack_pack($this->model);
    }

}