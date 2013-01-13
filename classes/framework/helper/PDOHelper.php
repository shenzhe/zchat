<?php

namespace framework\helper;

use \PDO;

/**
 * PDO数据处理类
 *
 */
class PDOHelper {

    /**
     * pdo对象
     *
     * @var \PDO
     */
    private $pdo;

    /**
     * 数据库名
     *
     * @var string
     */
    private $dbName;

    /**
     * 数据表名
     *
     * @var string
     */
    private $tableName;

    /**
     * 类名
     *
     * @var string
     */
    private $className;

    /**
     * 构造函数
     *
     * @param string $className
     * @param string $dbName
     */
    public function __construct($className, $dbName = null) {
        $this->className = $className;

        if (!empty($dbName)) {
            $this->dbName = $dbName;
        }
    }

    /**
     * 取得库名
     *
     * @return String
     */
    function getDBName() {
        return $this->dbName;
    }

    /**
     * 设置库名
     *
     * @param String $dbName
     */
    function setDBName($dbName) {
        $this->dbName = $dbName;
    }

    /**
     * 取得表名
     *
     * @return String
     */
    function getTableName() {
        if (empty($this->tableName)) {
            $classRef = new \ReflectionClass($this->className);
            $this->tableName = $classRef->getConstant('TABLE_NAME');
        }

        return $this->tableName;
    }

    /**
     * 取得类名
     *
     * @return String
     */
    function getClassName() {
        return $this->className;
    }

    /**
     * 取得查询表名
     *
     * @return String
     */
    function getLibName() {
        return "`{$this->getDBName()}`.`{$this->getTableName()}`";
    }

    /**
     * 取得PDO对象
     *
     * @return \PDO
     */
    function getPdo() {
        return $this->pdo;
    }

    /**
     * 设置PDO对象
     *
     * @param \PDO $pdo
     */
    function setPdo($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * 添加一个对象到数据库
     *
     * @param Object $entity
     * @param array $fields
     * @param string $onDuplicate
     * @return int
     */
    public function add($entity, $fields, $onDuplicate = null) {
        $strFields = '`' . implode('`,`', $fields) . '`';
        $strValues = ':' . implode(', :', $fields);

        $query = "INSERT INTO {$this->getLibName()} ({$strFields}) VALUES ({$strValues})";

        if (!empty($onDuplicate)) {
            $query .= 'ON DUPLICATE KEY UPDATE ' . $onDuplicate;
        }

        $statement = $this->pdo->prepare($query);
        $params = array();

        foreach ($fields as $field) {
            $params[$field] = $entity->$field;
        }

        $statement->execute($params);
        return $this->pdo->lastInsertId();
    }

    /**
     * 扩展插入
     *
     * @param array $entitys
     * @param array $fields
     * @return bool
     */
    public function addMulti($entitys, $fields) {
        $items = array();
        $params = array();

        foreach ($entitys as $index => $entity) {
            $items[] = '(:' . implode($index . ', :', $fields) . $index . ')';

            foreach ($fields as $field) {
                $params[$field . $index] = $entity->$field;
            }
        }

        $query = "INSERT INTO {$this->getLibName()} (`" . implode('`,`', $fields) . "`) VALUES " . implode(',', $items);
        $statement = $this->pdo->prepare($query);
        return $statement->execute($params);
    }

    /**
     * REPLACE模式添加一个对象到数据库
     *
     * @param Object $entity
     * @param array $fields
     * @return int
     */
    public function replace($entity, $fields) {
        $strFields = '`' . implode('`,`', $fields) . '`';
        $strValues = ':' . implode(', :', $fields);

        $query = "REPLACE INTO {$this->getLibName()} ({$strFields}) VALUES ({$strValues})";
        $statement = $this->pdo->prepare($query);
        $params = array();

        foreach ($fields as $field) {
            $params[$field] = $entity->$field;
        }

        $statement->execute($params);
        return $this->pdo->lastInsertId();
    }

    /**
     * 更新所有符合条件的对象
     *
     * @param array $fields
     * @param array $params
     * @param string $where
     * @param bool $change
     * @return bool
     */
    public function update($fields, $params, $where, $change = false) {
        if ($change) {
            $updateFields = array_map(__CLASS__ . '::changeFieldMap', $fields);
        } else {
            $updateFields = array_map(__CLASS__ . '::updateFieldMap', $fields);
        }

        $strUpdateFields = implode(',', $updateFields);
        $query = "UPDATE {$this->getLibName()} SET {$strUpdateFields} WHERE {$where}";
        $statement = $this->pdo->prepare($query);
        return $statement->execute($params);
    }

    /**
     * 取得符合条件的第一条记录的第一个值
     *
     * @param string $where
     * @param array $params
     * @param string $fields
     * @return mixed
     */
    public function fetchValue($where = '1', $params = null, $fields = '*') {
        $query = "SELECT {$fields} FROM {$this->getLibName()} WHERE {$where} limit 1";
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        return $statement->fetchColumn();
    }

    /**
     * 取得所有符合条件的数据（数组）
     *
     * @param string $where
     * @param array $params
     * @param string $fields
     * @param string $orderBy
     * @param string $limit
     * @return array
     */
    public function fetchArray($where = '1', $params = null, $fields = '*', $orderBy = null, $limit = null) {
        $query = "SELECT {$fields} FROM {$this->getLibName()} WHERE {$where}";

        if ($orderBy) {
            $query .= " ORDER BY {$orderBy}";
        }

        if ($limit) {
            $query .= " limit {$limit}";
        }

        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        $statement->setFetchMode(PDO::FETCH_ASSOC);
        return $statement->fetchAll();
    }

    /**
     * 获取所有符合条件的数据的第一列（一维数组）
     *
     * @param string $where
     * @param array $params
     * @param string $fields
     * @param string $orderBy
     * @param string $limit
     * @return array
     */
    public function fetchCol($where = '1', $params = null, $fields = '*', $orderBy = null, $limit = null) {
        $results = $this->fetchArray($where, $params, $fields, $orderBy, $limit);
        return empty($results) ? array() : array_map('reset', $results);
    }

    /**
     * 取得所有符合条件的对象
     *
     * @param string $where
     * @param array $params
     * @param string $fields
     * @param string $orderBy
     * @param string $limit
     * @return array
     */
    public function fetchAll($where = '1', $params = null, $fields = '*', $orderBy = null, $limit = null) {
        $query = "SELECT {$fields} FROM {$this->getLibName()} WHERE {$where}";

        if ($orderBy) {
            $query .= " order by {$orderBy}";
        }

        if ($limit) {
            $query .= " limit {$limit}";
        }

        $statement = $this->pdo->prepare($query);

        if (!$statement->execute($params)) {
            throw new \Exception('data base error');
        }

        $statement->setFetchMode(PDO::FETCH_CLASS, $this->className);
        return $statement->fetchAll();
    }

    /**
     * 根据条件返回一个对象
     *
     * @param string $where
     * @param array $params
     * @param string $fields
     * @return object
     */
    public function fetchEntity($where = '1', $params = null, $fields = '*', $orderBy = null) {
        $query = "SELECT {$fields} FROM {$this->getLibName()} WHERE {$where}";

        if ($orderBy) {
            $query .= " order by {$orderBy}";
        }

        $query .= " limit 1";
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        $statement->setFetchMode(PDO::FETCH_CLASS, $this->className);
        return $statement->fetch();
    }

    public function fetchCount($where = '1', $pk = "*") {
        $query = "SELECT count({$pk}) as count FROM {$this->getLibName()} WHERE {$where}";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        $result = $statement->fetch();
        return $result["count"];
    }

    /**
     * 删除符合条件的记录
     *
     * @param string $where
     * @param array $params
     */
    public function remove($where, $params) {
        if (empty($where)) {
            return false;
        }

        $query = "DELETE FROM {$this->getLibName()} WHERE {$where}";
        $statement = $this->pdo->prepare($query);
        return $statement->execute($params);
    }

    public static function updateFieldMap($field) {
        return '`' . $field . '`=:' . $field;
    }

    public static function changeFieldMap($field) {
        return '`' . $field . '`=`' . $field . '`+:' . $field;
    }

    public function fetchBySql($sql) {
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        return $statement->fetch();
    }

}
