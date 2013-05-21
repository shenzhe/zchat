<?php

namespace ctrl;

use framework\config;
use framework\core\IController;
use framework\core\IRequestDispatcher;
use common;

class CtrlBase implements IController {
    public $dispatcher;
    protected $params = array();
    protected $loginInfo;

    public function setDispatcher(IRequestDispatcher $dispatcher) {
        $this->dispatcher = $dispatcher;

        $this->params = $dispatcher->getParams();
        if (isset($this->params['data'])) {
            $this->params = $this->params + \json_decode($this->params['data'], true);
            unset($this->params['data']);
        }
    }

    public function beforeFilter() {
        return true;
    }

    public function afterFilter() {
        
    }

    public function getParams() {
        return $this->params;
    }

    protected function getInteger(array $params, $key, $default = null, $abs = true, $notEmpty = false) {

        if (!isset($params[$key])) {
            if ($default !== null) {
                return $default;
            }
            throw new common\GameException("no params {$key}", common\ERROR::PARAM_ERROR);
        }

        $integer = isset($params[$key]) ? \intval($params[$key]) : 0;

        if ($abs) {
            $integer = \abs($integer);
        }

        if ($notEmpty && empty($integer)) {
            throw new common\GameException('params no empty', common\ERROR::PARAM_ERROR);
        }

        return $integer;
    }

    protected function getIntegers($params, $key, $abs = false, $notEmpty = false) {
        $params = (array) $params;
        $integers = (\array_key_exists($key, $params) && !empty($params[$key])) ? \array_map('intval', (array) $params[$key]) : array();

        if ($abs) {
            $integers = \array_map('abs', $integers);
        }

        if (!empty($notEmpty) && empty($integers)) {
            throw new common\GameException('params no empty', common\ERROR::PARAM_ERROR);
        }

        return $integers;
    }

    protected function getFloat($params, $key, $abs = false, $notEmpty = false) {
        $params = (array) $params;

        if (!isset($params[$key])) {
            throw new common\GameException("no params {$key}", common\ERROR::PARAM_ERROR);
        }

        $float = \array_key_exists($key, $params) ? \floatval($params[$key]) : 0;

        if ($abs) {
            $float = \abs($float);
        }

        if (!empty($notEmpty) && empty($float)) {
            throw new common\GameException('params no empty', common\ERROR::PARAM_ERROR);
        }

        return $float;
    }

    protected function getString($params, $key, $default = null, $notEmpty = false) {
        $params = (array) $params;

        if (!isset($params[$key])) {
            if (null !== $default) {
                return $default;
            }
            throw new common\GameException("no params {$key}", common\ERROR::PARAM_ERROR);
        }

        $string = \trim($params[$key]);

        if (!empty($notEmpty) && empty($string)) {
            throw new common\GameException('params no empty', common\ERROR::PARAM_ERROR);
        }

        return \addslashes($string);
    }

    protected function getStrings($params, $key, $notEmpty = false) {
        $params = (array) $params;
        $strings = (\array_key_exists($key, $params) && !empty($params[$key])) ? \array_map('trim', (array) $params[$key]) : array();

        if (!empty($notEmpty) && empty($strings)) {
            throw new common\GameException('params no empty', common\ERROR::PARAM_ERROR);
        }

        return \array_map("addslashes", $strings);
    }

    protected function getJson(array $params, $key, $default = null, $array = true) {

        if (!isset($params[$key])) {
            if (null !== $default) {
                return $default;
            }
            throw new common\GameException("no params {$key}", common\ERROR::PARAM_ERROR);
        }

        if (\is_array($params[$key]) || \is_object($params[$key])) {
            return $params[$key];
        }

        return \json_decode($params[$key], $array);
    }
}
