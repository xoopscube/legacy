<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

class groupsUsersLink
{
	private function _deleteGroupsUsersLink(&$groupLinkHandler,$uid){
		// delete before insert
		if ($uid) {
			$groupLinkCriteria = new CriteriaCompo();
			$groupLinkCriteria->add(new Criteria('uid', $uid));
			$groupLinkHandler->deleteAll($groupLinkCriteria);
		}
	}
	private function _insertGroupsUsersLink(&$groupLinkHandler,$uid,$groupIds){
		$flag = true;
		// insert gid
		foreach ($groupIds as $gid) {
			if ($gid) {
				$link = $groupLinkHandler->create();
				$link->set('groupid', $gid);
				$link->set('uid', $uid);
				$flag = $groupLinkHandler->insert($link, true);
			}
		}
		return $flag;
	}
	/**
	 * Insert & Update for groups_users_link table
	 *
	 * @param $uid
	 * @param $groupIds
	 * @return bool
	 */
	function insertGroups($uid, $groupIds)
	{
		$flag = true;

		$handler =& xoops_getmodulehandler('groups_users_link', 'user');
		if ( !$this->_deleteGroupsUsersLink($handler, $uid) ) {
			$flag = false;
		}
		if ( !$this->_insertGroupsUsersLink($handler, $uid, $groupIds) ) {
			$flag = false;
		}
		return $flag;
	}
}
