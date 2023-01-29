<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class UserMailjob_linkObject extends XoopsSimpleObject
{
    public function __construct()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->mVars = $initVars;
            return;
        }
        $this->initVar('mailjob_id', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('uid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('retry', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('message', XOBJ_DTYPE_STRING, '', false, 191);
        $initVars = $this->mVars;
    }
}

// @todo @gigamasterWARNING:
// Declaration of & UserMailjob_linkHandler::get($mailjob_id, $uid)
// should be compatible with & XoopsObjectGenericHandler::get($id)

class UserMailjob_linkHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'user_mailjob_link';
    public $mPrimary = 'mailjob_id';
    public $mClass = 'UserMailjob_linkObject';

    public function &get( $id, $mailjob_id = '', $uid = '' )
    {

        $ret = null;

        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('mailjob_id', $mailjob_id));
        $criteria->add(new Criteria('uid', $uid));

        $objArr = &$this->getObjects($criteria);

        if (1 == count($objArr)) {
            $ret = &$objArr[0];
        }

        return $ret;
    }

    public function getCurrentRetry($mailjob_id)
    {
        $mailjob_id = (int)$mailjob_id;
        $table = $this->mTable;

        $sql = "SELECT min(retry) AS cretry FROM {$table} where mailjob_id='{$mailjob_id}'";

        $result = $this->db->query($sql);
        $row = $this->db->fetchArray($result);

        return $row['cretry'];
    }

    public function _update(&$obj)
    {
        $set_lists = [];
        $where = '';

        $arr = $this->_makeVars4sql($obj);

        foreach ($arr as $_name => $_value) {
            if ('mailjob_id' == $_name || 'uid' == $_name) {
                $where = "{$_name}={$_value}";
            } else {
                $set_lists[] = "{$_name}={$_value}";
            }
        }

        $sql = @sprintf('UPDATE ' . $this->mTable . ' SET %s WHERE %s', implode(',', $set_lists), $where);

        return $sql;
    }

    public function delete(&$obj, $force = false)
    {
        //
        // Because Criteria can generate the most appropriate sentence, use
        // criteria even if this approach is few slow.
        //
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('mailjob_id', $obj->get('mailjob_id')));
        $criteria->add(new Criteria('uid', $obj->get('uid')));
        $sql = 'DELETE FROM ' . $this->mTable . ' WHERE ' . $this->_makeCriteriaElement4sql($criteria, $obj);

        return $force ? $this->db->queryF($sql) : $this->db->query($sql);
    }
}
