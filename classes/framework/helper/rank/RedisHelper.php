<?php

namespace framework\helper\rank;

use framework\manager;

/**
 * redis 排行榜处理类
 */
class RedisHelper implements IRank {

    private $redis = null;
    private $pconnect = false;

    public function __construct($name, $pconnect = false) {
        if (empty($this->redis)) {
            $this->redis = manager\RedisManager::getInstance($name, $pconnect);
            $this->pconnect = $pconnect;
        }
    }

    /**
     * 添加一个排行
     * @param $rankType
     * @param $key
     * @param $score
     * @param int $length
     * @return bool
     */
    public function addRank($rankType, $key, $score, $length = 0) {
        $this->redis->zAdd($rankType, $score, $key);
        if ($length > 0) { //限个数
            $all = $this->redis->zCard($rankType);
            if ($all > $length) {
                $keys = $this->redis->zRange($rankType, 0, $all - $length);
                foreach ($keys as $key) {
                    $this->redis->zDelete($rankType, $key);
                }
            }
        }

        return true;
    }

    /**
     * 获取一段排行
     * @param $rankType
     * @param int $start
     * @param int $limit
     * @param bool $score
     * @return mixed
     */
    public function getRank($rankType, $start = 0, $limit = 100, $score = true) {
        return $this->redis->zRevRange($rankType, $start, $limit, $score);
    }

    /**
     * 返回某一区间排行的人数
     * @param $rankType
     * @param $start
     * @param $end
     * @return mixed
     */
    public function getRankBetweenCount($rankType, $start, $end) {
        return $this->redis->zCount($rankType, $start, $end);
    }

    /**
     *
     * 返回排行榜总人数
     * @param type $rankType
     */
    public function getRankCount($rankType) {
        return $this->redis->zCard($rankType);
    }

    /**
     *
     * 获取指定key的排行
     * @param type $rankType
     * @param type $key
     */
    public function getRankByKey($rankType, $key) {
        return $this->redis->zRevRank($rankType, $key);
    }

    /**
     *
     * 更新指定key的排行
     * @param type $rankType
     * @param type $key
     * @param type $score
     * @return type
     */
    public function updateRankByKey($rankType, $key, $score) {
        return $this->redis->zIncrBy($rankType, $score, $key);
    }

    /**
     * 删除指定key的排行
     * @param $rankType
     * @param $key
     * @return mixed
     */
    public function zDelete($rankType, $key) {
        return $this->redis->zDelete($rankType, $key);
    }

    /**
     * 删除某类排行
     * @param $rankType
     * @return mixed
     */
    public function deleteRank($rankType) {
        return $this->redis->delete($rankType);
    }

    /**
     * 获取指定key的分数
     * @param $rankType
     * @param $key
     * @return mixed
     */
    public function getScoreByKey($rankType, $key) {
        return $this->redis->zScore($rankType, $key);
    }

}
