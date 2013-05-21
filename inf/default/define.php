<?php

    //====cache相关=====
    define('NET_CACHE_TYPE', 'Redis');
    define('CACHE_PCONNECT', true);
    define('CACHE_PX', 'rc');
    define('CACHE_HOST', '127.0.0.1');
    define('CACHE_PORT', 6379);

    //======db相关======
    define('DB_PREFIX', 'commonDB');
    define('DB_HOST', 'mysql:host=localhost;port=3306');
    define('DB_LIB', 'zchat');
    define('DB_USER', 'zchat');
    define('DB_PASS', '11111');

    //======系统相关======
    define('DEFAULT_LOCALE', 'zh_CN');
    define('DEFAULT_CHARSET', 'UTF8');
    define('PROJECT_NAME', 'chat');
    define('STATIC_SERVER', '/static/');
    define('LOCKER_PREFIX', 'CHAT_');
    define('LOCKER_TERM', 5);
    define('KEY_SEPARATOR', '.');
    define('REDIS_CONNECT_TIMEOUT', 3);
    define('SERIALIZE_TYPE', 'msgpack');
    define('PWD_KEY', 'padia813407pieqr!');
