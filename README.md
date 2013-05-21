zchat
====

@author: shenzhe (泽泽，半桶水)

@email: shenzhe163@gmail.com

zchat 是 基于 zphp实现的聊天室，着重于zphp在 socket, redis-storage, swoole相结合

需求的扩展：
=========

1) swoole: https://github.com/matyhtf/php_swoole

2:redis-storage: http://github.com/shenzhe/redis-storage

3: phpredis: http://github.com/shenzhe/phpredis

运行：
======

1) cd 程序目录

2) php bin/sendbox.php   //flash sendbox实现

3) php bin/socket.php    //相关业务逻辑

4) 运行redis-storage

5) webserver绑定域名到 webroot ，运行 http://host

配置：
=====
相关配置目录在inf/default

