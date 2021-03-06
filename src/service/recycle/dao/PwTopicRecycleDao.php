<?php

defined('WEKIT_VERSION') || exit('Forbidden');

/**
 * 主题回收站记录数据表
 *
 * @author Jianmin Chen <sky_hold@163.com>
 * @copyright ©2003-2103 phpwind.com
 * @license http://www.phpwind.com
 * @version $Id: PwTopicRecycleDao.php 14354 2012-07-19 10:36:06Z jieyin $
 * @package src.service.user.dao
 */
class PwTopicRecycleDao extends PwBaseDao
{
    protected $_table = 'recycle_topic';
    protected $_table_thread = 'bbs_threads';
    protected $_pk = 'tid';
    protected $_dataStruct = array('tid', 'fid', 'operate_time', 'operate_username', 'reason');

    public function fetchRecord($tids)
    {
        return $this->_fetch($tids, 'tid');
    }

    public function add($fields)
    {
        return $this->_add($fields);
    }

    public function batchAdd($data)
    {
        $fields = array();
        foreach ($data as $key => $value) {
            if (!$this->_filterStruct($value)) {
                continue;
            }
            $fields[] = array($value['tid'], $value['fid'], $value['operate_time'], $value['operate_username'], $value['reason']);
        }
        if (!$fields) {
            return false;
        }
        $sql = $this->_bindSql('REPLACE INTO %s (tid, fid, operate_time, operate_username, reason) VALUES %s', $this->getTable(), $this->sqlMulti($fields));
        $this->getConnection()->execute($sql);

        return true;
    }

    public function batchDelete($tids)
    {
        return $this->_batchDelete($tids);
    }

    public function countSearchRecord($field)
    {
        list($where, $arg) = $this->_buildCondition($field);
        $sql = $this->_bindSql('SELECT COUNT(*) AS sum FROM %s a LEFT JOIN %s b ON a.tid=b.tid WHERE %s', $this->getTable(), $this->getTable($this->_table_thread), $where);
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->getValue($arg);
    }

    public function searchRecord($field, $orderby, $limit, $offset)
    {
        list($where, $arg) = $this->_buildCondition($field);
        $order = $this->_buildOrderby($orderby);
        $sql = $this->_bindSql('SELECT a.*,b.*,a.fid AS fid FROM %s a LEFT JOIN %s b ON a.tid=b.tid WHERE %s %s %s', $this->getTable(), $this->getTable($this->_table_thread), $where, $order, $this->sqlLimit($limit, $offset));
        $smt = $this->getConnection()->createStatement($sql);

        return $smt->queryAll($arg, 'tid');
    }

    protected function _buildCondition($field)
    {
        $where = '1';
        $arg = array();
        foreach ($field as $key => $value) {
            switch ($key) {
                case 'fid':
                    $where .= ' AND a.fid'.$this->_sqlIn($value, $arg);
                    break;
                case 'created_userid':
                    $where .= ' AND b.created_userid'.$this->_sqlIn($value, $arg);
                    break;
                case 'title_keyword':
                    $where .= ' AND b.subject LIKE ?';
                    $arg[] = "%$value%";
                    break;
                case 'created_time_start':
                    $where .= ' AND b.created_time>?';
                    $arg[] = $value;
                    break;
                case 'created_time_end':
                    $where .= ' AND b.created_time<?';
                    $arg[] = $value;
                    break;
                case 'operator':
                    $where .= ' AND a.operate_username=?';
                    $arg[] = $value;
                    break;
                case 'operate_time_start':
                    $where .= ' AND a.operate_time>?';
                    $arg[] = $value;
                    break;
                case 'operate_time_end':
                    $where .= ' AND a.operate_time<?';
                    $arg[] = $value;
                    break;
            }
        }

        return array($where, $arg);
    }

    protected function _buildOrderby($orderby)
    {
        $array = array();
        foreach ($orderby as $key => $value) {
            switch ($key) {
                case 'tid':
                    $array[] = 'a.tid '.($value ? 'ASC' : 'DESC');
                    break;
            }
        }

        return $array ? ' ORDER BY '.implode(',', $array) : '';
    }

    protected function _sqlIn($value, &$arg)
    {
        if (is_array($value)) {
            return ' IN '.$this->sqlImplode($value);
        }
        $arg[] = $value;

        return '=?';
    }
}
