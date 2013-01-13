<?php

namespace framework\util;

/**
 * 格式转换工具类
 */
class Serialize {

    public static function Serialize($data, $type = 'serialize') {
        switch ($type) {
            case 'igbinary';
                return \igbinary_serialize($data);
                break;
            case 'msgpack':
                return \msgpack_pack($data);
                break;
            default:
                return \serialize($data);
        }
    }

    public static function Unserialize($data, $type = "serialize") {
        switch ($type) {
            case 'igbinary';
                return \igbinary_unserialize($data);
                break;
            case 'msgpack':
                return \msgpack_unpack($data);
                break;
            default:
                return \unserialize($data);
        }
    }

}
