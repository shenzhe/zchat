<?php

namespace dao;

use entity;

class UserDao extends UserDaoBase {

    public function __construct() {
        parent::__construct('entity\\User', true);
    }

    public function add(entity\User $user) {
        $this->add($user);
        return $user;
    }

    public function fetchByWhere($where) {
        return $this->fetchByWhere($where);
    }

    public function fetchById($userId) {
        return $this->fetchByWhere("id={$userId}");
    }

    public function remove($userId) {
        return $this->remove($userId);
    }

}
