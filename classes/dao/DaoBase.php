<?php

namespace dao;

abstract class DaoBase {

    /**
     * 实例名称
     *
     * @var string
     */
    protected $entity;

    /**
     * 缓存基础键名
     *
     * @var string
     */
    protected $baseKey;

    /**
     * 获取基础键名
     *
     * @return string
     */
    private function getBaseKey() {
        if (empty($this->baseKey)) {
            $items = \explode('\\', $this->entity);
            $this->baseKey = \array_pop($items);
        }

        return $this->baseKey;
    }

    /**
     * 获取缓存键名
     *
     * @param multiMixed
     * @return string
     */
    protected function getInfoKey() {
        $args = \func_get_args();
        $argNum = \func_num_args();
        $cacheKey = $this->getBaseKey();

        if ($argNum == 1 && \is_array($args[0])) {
            foreach ($args[0] as $key => $value) {
                if (\is_array($value)) {
                    $cacheKey .= \KEY_SEPARATOR . $key . \KEY_SEPARATOR . \implode(\KEY_SEPARATOR, $value);
                } else {
                    $cacheKey .= \KEY_SEPARATOR . $key . \KEY_SEPARATOR . $value;
                }
            }
        } elseif ($argNum == 2 && \is_object($args[0]) && \is_array($args[1])) {
            foreach ($args[1] as $key) {
                $cacheKey .= \KEY_SEPARATOR . $key . \KEY_SEPARATOR . $args[0]->$key;
            }
        } elseif ($argNum == 1 && !\is_object($args[0]) && !\is_array($args[0])) {
            $cacheKey .= \KEY_SEPARATOR . $args[0];
        }

        return $cacheKey;
    }

    /**
     * 获取实体的所有属性名称
     *
     * @return array
     */
    protected function getEntityAttribs() {
        return \array_keys(\get_class_vars($this->entity));
    }

}