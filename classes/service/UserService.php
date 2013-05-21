<?php

namespace service;

use entity,
    common;


class UserService {


    private $dao;

    public function __construct() {
        $this->dao = common\ClassLocator::getDao('User');
    }

    public function save($userName, $password) {
        $user = new entity\User();
        $user->name = $userName;
        $user->password = sha1(PWD_KEY.$password);
        return $this->add($user);
    }

    public function add(entity\User $user) {
        $this->dao->add($user);
        return $user;
    }


    public function fetchById($id) {
        return $this->dao->fetchById($id);
    }

    public function remove($userId) {
        return $this->dao->remove($userId);
    }

}
