<?php

if (!defined('XOOPS_ROOT_PATH')) exit();


class UserGroups_users_linkHandler extends XoopsObjectGenericHandler
{
	var $mTable = "groups_users_link";
	var $mPrimary = "linkid";
	var $mClass = "UserGroups_users_linkObject";
	
	function isUserOfGroup($uid, $groupid)
	{
		$criteria =new CriteriaCompo();
		$criteria->add(new Criteria('groupid', $groupid));
		$criteria->add(new Criteria('uid', $uid));
		
		$objs =& $this->getObjects($criteria);
		return (count($objs) > 0 && is_object($objs[0]));
	}
	function deleteAllByUser($uid)
	{
    	$sql = "DELETE FROM `".$this->db->prefix($this->mTable)."` ";
    	$sql.= "WHERE `uid` = ".$uid." ";
		$this->_db->queryF($sql);
	}
	/*
	 *  Insert & Update for groups_users_link table
	 */
	function insert_groups(&$handler,$uid,$groupIds){
		$flag = true;
		// delete before insert
		if ($uid){
			$this->deleteAllByUser($uid);
		}
		// insert gid
		foreach ($groupIds as $gid) {
			if ($gid){
				$link =& $handler->create();
				$link->set('groupid', $gid);
				$link->set('uid', $uid);
				$flag &= $handler->insert($link, true);
			}
			unset($link);
		}
		return $flag;
	}
	
}

?>
