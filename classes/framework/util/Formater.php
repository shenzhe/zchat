<?php

namespace framework\util;

use \Exception;

/**
 * 格式转换工具类
 */
class Formater {

    /**
     * 格式化异常
     *
     * @param Exception $exception
     * @return array
     */
    public static function formatException(\Exception $exception) {
        $exceptionHash = array(
            'className' => 'Exception',
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'userAgent' => $_SERVER['HTTP_USER_AGENT'],
            'trace' => array(),
        );

        if (true) {
            $traceItems = $exception->getTrace();

            foreach ($traceItems as $traceItem) {
                $traceHash = array(
                    'file' => isset($traceItem['file']) ? $traceItem['file'] : 'null',
                    'line' => isset($traceItem['line']) ? $traceItem['line'] : 'null',
                    'function' => isset($traceItem['function']) ? $traceItem['function'] : 'null',
                    'args' => array(),
                );

                if (!empty($traceItem['class'])) {
                    $traceHash['class'] = $traceItem['class'];
                }

                if (!empty($traceItem['type'])) {
                    $traceHash['type'] = $traceItem['type'];
                }

                if (!empty($traceItem['args'])) {
                    foreach ($traceItem['args'] as $argsItem) {
                        $traceHash['args'][] = \var_export($argsItem, true);
                    }
                }

                $exceptionHash['trace'][] = $traceHash;
            }
        }

        return $exceptionHash;
    }

}