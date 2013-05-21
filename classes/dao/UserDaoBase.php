<?php

namespace dao;

use framework\manager;
use framework\helper;
use framework\util;

abstract class UserDaoBase extends DaoBase {

    public $dbHelper = null;

    public function __construct($entity) {
        if(empty($this->dbHelper)){
            $this->dbHelper = new helper\PDOHelper($this->entity, DB_LIB);
            $pdo = manager\PDOManager::getInstance(DB_PREFIX);
            $this->dbHelper->setPdo($pdo);
        } else {
            $this->dbHelper->setClassName($this->entity);
        }
    }

    public function getDb() {
        return $this->dbHelper;
    }

    public function update($entity) {
        $fields = array();
        $params = array();
        foreach ($entity as $key => $val) {
            $fields[] = $key;
            $params[$key] = $val;
        }

        return $this->dbHelper->update($fields, $params, 'id=' . $entity->id);
    }

    public function add($entity) {
        return $this->dbHelper->replace($entity, \array_keys(\get_object_vars($entity)));
    }

    public function remove($id, $field='id') {
        if(empty($id)) {
            return;
        }
        $this->dbHelper->remove("{$field} = :id", [':id'=>$id]);
    }

    public function removeByWhere($where) {
        $this->dbHelper->remove($where);
    }

    public function fetchWhere($where="1") {
        return $this->dbHelper->fetchAll($where);
    }

    public function fetchCount($where='1') {
        return $this->dbHelper->fetchCount($where);
    }
    
    

}
