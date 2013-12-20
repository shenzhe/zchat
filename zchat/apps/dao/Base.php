<?php

namespace dao;

use dao,
    common,
    ZPHP\Core\Config as ZConfig,
    ZPHP\Storage\Factory as ZStorage,
    ZPHP\Serialize\Factory as ZSerialize,
    ZPHP\Db\Pdo as ZPdo,
    ZPHP\Cache\Factory as ZCache;

abstract class Base
{
    private $entity;
    private $_db = null;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    public function useDb()
    {
        if (empty($this->_db)) {
            $config = ZConfig::get('pdo');
            $this->_db = new ZPdo($config, $this->entity, $config['dbname']);
            $this->_db->setClassName($this->entity);
        }
        return $this->_db;
    }

    public function fetchById($id)
    {
        $this->useDb();
        return $this->_db->fetchEntity("id={$id}");
    }

    public function fetchAll(array $items=[])
    {
        $this->useDb();
        if(empty($items)) {
            return $this->_db->fetchAll();
        }
        $where = "1";
        foreach ($items as $k => $v) {
            $where .= " and {$k}={$v}";
        }
        return $this->_db->fetchAll($where);
    }

    public function fetchWhere($where='')
    {
        $this->useDb();
        return $this->_db->fetchAll($where);
    }

    public function update($attr)
    {
        $fields = array();
        $params = array();
        foreach ($attr as $key => $val) {
            $fields[] = $key;
            $params[$key] = $val;
        }
        $this->useDb();
        return $this->_db->update($fields, $params, 'id=' . $attr->id);
    }

    public function add($attr)
    {
        $this->useDb();
        return $this->_db->replace($attr, \array_keys(\get_object_vars($attr)));
    }

    public function remove($where)
    {
        $this->useDb();
        $this->dbHelper->remove($where);
    }
}
