<?php

// -*-coding:utf-8; mode:php-mode;-*-

namespace framework\util;

/**
 * 文件操作工具类
 */
class FileUtil {

    /**
     * 取得目录下及所有子孙目录的文件路径，并且以$dir参数为根目录名
     * @param String $dir 路径名
     * @param String $filter 正则表达式，过滤掉文件名不匹配该表达式的文件
     * @return array
     */
    public static function treeDirectory($dir, $filter = null) {
        $files = array();
        $dirpath = \realpath($dir);
        $filenames = \scandir($dir);

        foreach ($filenames as $filename) {
            if (\in_array($filename, array('.', '..', '.svn'))) {
                continue;
            }

            if (!empty($filter) && !\preg_match($filter, $filename)) {
                continue;
            }

            $file = $dirpath . DIRECTORY_SEPARATOR . $filename;

            if (\is_dir($file)) {
                $files = \array_merge($files, self::treeDirectory($file));
            } else {
                $files[] = $file;
            }
        }

        return $files;
    }

    public static function delDirectory($dir, $filter = null) {
        $dirpath = \realpath($dir);
        $filenames = \scandir($dir);
        foreach ($filenames as $filename) {
            if (\in_array($filename, array('.', '..'))) {
                continue;
            }

            $file = $dirpath . DIRECTORY_SEPARATOR . $filename;

            if (\is_dir($file)) {
                self::delDirectory($file);
            } else {
                \unlink($file);
            }
        }

        return \rmdir($dir);
    }

}