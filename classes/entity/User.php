<?php
namespace entity;

const TABLE_NAME = 'attr';

class User{
    public $uid;
    public $name;
    public $password;

    public function getHash() {
        return [
        	'uid'=>$uid,
            'name'=>$this->name,
        ];
    }
}