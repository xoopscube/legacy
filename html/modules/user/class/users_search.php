<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/modules/user/class/users.php";

class UserUsers_searchHandler extends UserUsersHandler
{
	function &getObjects($criteria = null, $limit = null, $start = null, $id_as_key = false)
	{
		$ret = array();
		
		$uTable = $this->db->prefix('users') . " as u";
		$gTable = $this->db->prefix('groups_users_link') . " as g";

		$sql = "SELECT DISTINCT u.* FROM ${uTable} LEFT JOIN ${gTable} ON u.uid=g.uid";

		if($criteria !== null && is_a($criteria, 'CriteriaElement')) {
			$where = $this->_makeCriteria4sql($criteria);
			
			if (trim($where)) {
				$sql .= ' WHERE ' . $where;
			}
			
			$sorts = array();
			foreach ($criteria->getSorts() as $sort) {
				$sorts[] = $sort['sort'] . ' ' . $sort['order'];
			}
			if ($criteria->getSort() != '') {
				$sql .= " ORDER BY " . implode(',', $sorts);
			}
			
			if ($limit === null) {
				$limit = $criteria->getLimit();
			}
			
			if ($start === null) {
				$start = $criteria->getStart();
			}
		}
		else {
			if ($limit === null) {
				$limit = 0;
			}
			
			if ($start === null) {
				$start = 0;
			}
		}

		$result = $this->db->query($sql, $limit, $start);

		if (!$result) {
			return $ret;
		}

		while($row = $this->db->fetchArray($result)) {
			$obj =new $this->mClass();
			$obj->assignVars($row);
			$obj->unsetNew();
			
			if ($id_as_key)	{
				$ret[$obj->get($this->mPrimary)] =& $obj;
			}
			else {
				$ret[]=&$obj;
			}
		
			unset($obj);
		}
	
		if (count($ret)) {
			foreach (array_keys($ret) as $key) {
				$ret[$key]->_loadGroups();
			}
		}
		
		return $ret;
	}

	/**
	 * Return the array which consists of an integer as the uid. This member
	 * function is more speedy than getObjects().
	 * 
	 * @return Array
	 */
	function &getUids($criteria = null, $limit = null, $start = null, $id_as_key = false)
	{
		$ret = array();
		
		$uTable = $this->db->prefix('users') . " as u";
		$gTable = $this->db->prefix('groups_users_link') . " as g";

		$sql = "SELECT DISTINCT u.uid FROM ${uTable} LEFT JOIN ${gTable} ON u.uid=g.uid";

		if($criteria !== null && is_a($criteria, 'CriteriaElement')) {
			$where = $this->_makeCriteria4sql($criteria);
			
			if (trim($where)) {
				$sql .= ' WHERE ' . $where;
			}
			
			$sorts = array();
			foreach ($criteria->getSorts() as $sort) {
				$sorts[] = $sort['sort'] . ' ' . $sort['order'];
			}
			if ($criteria->getSort() != '') {
				$sql .= " ORDER BY " . implode(',', $sorts);
			}
			
			if ($limit === null) {
				$limit = $criteria->getLimit();
			}
			
			if ($start === null) {
				$start = $criteria->getStart();
			}
		}
		else {
			if ($limit === null) {
				$limit = 0;
			}
			
			if ($start === null) {
				$start = 0;
			}
		}

		$result = $this->db->query($sql, $limit, $start);

		if (!$result) {
			return $ret;
		}

		while ($row = $this->db->fetchArray($result)) {
			$ret[] = $row['uid'];
		}
		
		return $ret;
	}
	
	function getCount($criteria = null)
	{
		$ret = array();

		$uTable = $this->db->prefix('users') . " as u";
		$gTable = $this->db->prefix('groups_users_link') . " as g";

        $sql = "SELECT COUNT(DISTINCT u.uid) c FROM ${uTable} LEFT JOIN ${gTable} ON u.uid=g.uid";
		if($criteria !== null && is_a($criteria, 'CriteriaElement')) {
			$where = $this->_makeCriteria4sql($criteria);
			
			if ($where) {
				$sql .= " WHERE " . $where;
			}
		}
			
		return $this->_getCount($sql);
	}
	
	function insert(&$user, $force = false)
	{
		if (parent::insert($user, $force)) {
			$flag = true;
			
			$user->_loadGroups();

			$handler =& xoops_getmodulehandler('groups_users_link', 'user');
			$oldLinkArr =& $handler->getObjects(new Criteria('uid', $user->get('uid')), $force);
			
			//
			// Delete
			//
			$oldGroupidArr = array();
			foreach (array_keys($oldLinkArr) as $key) {
				$oldGroupidArr[] = $oldLinkArr[$key]->get('groupid');
				if (!in_array($oldLinkArr[$key]->get('groupid'), $user->Groups)) {
					$handler->delete($oldLinkArr[$key], $force);
				}
			}

			foreach ($user->Groups as $gid) {
				if (!in_array($gid, $oldGroupidArr)) {
					$link =& $handler->create();
				
					$link->set('groupid', $gid);
					$link->set('uid', $user->get('uid'));
				
					$flag &= $handler->insert($link, $force);
				
					unset($link);
				}
			}
			
			return $flag;
		}
		
		return false;
	}

	function deleteAll($criteria, $force = false)
	{
	}
}

?>
