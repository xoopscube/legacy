<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class UserMailjob_linkObject extends XoopsSimpleObject
{
	function UserMailjob_linkObject()
	{
		$this->initVar('mailjob_id', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('uid', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('retry', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('message', XOBJ_DTYPE_STRING, '', false, 255);
	}
}

class UserMailjob_linkHandler extends XoopsObjectGenericHandler
{
	var $mTable = "user_mailjob_link";
	var $mPrimary = "mailjob_id";
	var $mClass = "UserMailjob_linkObject";
	
	function &get($mailjob_id, $uid)
	{
		$ret = null;

		$criteria =new CriteriaCompo();
		$criteria->add(new Criteria('mailjob_id', $mailjob_id));
		$criteria->add(new Criteria('uid', $uid));

		$objArr =& $this->getObjects($criteria);
		
		if (count($objArr) == 1) {
			$ret =& $objArr[0];
		}

		return $ret;
	}
	
	function getCurrentRetry($mailjob_id)
	{
		$mailjob_id = intval($mailjob_id);
		$table = $this->mTable;
		
		$sql = "SELECT min(retry) AS cretry FROM ${table} where mailjob_id='${mailjob_id}'";
		
		$result = $this->db->query($sql);
		$row = $this->db->fetchArray($result);
		
		return $row['cretry'];
	}
	
	function _update(&$obj) {
		$set_lists=array();
		$where = "";

		$arr = $this->_makeVars4sql($obj);

		foreach ($arr as $_name => $_value) {
			if ($_name == 'mailjob_id' || $_name == 'uid') {
				$where = "${_name}=${_value}";
			}
			else {
				$set_lists[] = "${_name}=${_value}";
			}
		}

		$sql = @sprintf("UPDATE " . $this->mTable . " SET %s WHERE %s", implode(",",$set_lists), $where);

		return $sql;
	}
	
	function delete(&$obj, $force = false)
	{
		//
		// Because Criteria can generate the most appropriate sentence, use
		// criteria even if this approach is few slow.
		//
		$criteria =new CriteriaCompo();
		$criteria->add(new Criteria('mailjob_id', $obj->get('mailjob_id')));
		$criteria->add(new Criteria('uid', $obj->get('uid')));
		$sql = "DELETE FROM " . $this->mTable . " WHERE " . $this->_makeCriteriaElement4sql($criteria, $obj);

		return $force ? $this->db->queryF($sql) : $this->db->query($sql);
	}
}

?>
