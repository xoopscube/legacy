<?php
/**
 * @file
 * @package legacy
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit();
}

/**
 * Interface of group delegate
**/
abstract class Legacy_AbstractGroupDelegate
{
	/**
	 * getGroupIdList Legacy_Group.GetGroupIdList
	 *
	 * @param XoopsSimpleObject[] &$groupList
	 * @param int	$uid
	 *
	 * @return	void
	 */ 
	abstract public function getGroupIdList(/*** int[] ***/ &$groupIds, /*** int ***/ $uid);

	/**
	 * getGroupList Legacy_Group.GetGroupList
	 *
	 * @param XoopsSimpleObject[] &$groupList
	 * @param int	$uid
	 * @param int	$limit
	 * @param int	$start
	 *
	 * @return	void
	 */ 
	abstract public function getGroupList(/*** XoopsSimpleObject[] ***/ &$groupList, /*** int ***/ $uid, /*** int ***/ $limit=20, /*** int ***/ $start=0);

	/**
	 * getGroup 	Legacy_Group.GetGroup
	 * get Group Object
	 *
	 * @param XoopsSimpleObject &$group
	 * @param int	$groupId
	 *
	 * @return	void
	 */ 
	abstract public function getGroup(/*** string ***/ &$group, /*** int ***/ $groupId);

	/**
	 * getMemberList	  Legacy_Group.GetMemberList
	 * get category objects in the form of tree.
	 *
	 * @param mixed $memberList
	 *	$memberList['uid']
	 *	$memberList['level']
	 * @param int	$groupId :category group id
	 *
	 * @return	void
	 */ 
	abstract public function getMemberList(/*** int[] ***/ &$memberList, /*** int ***/ $groupId);

	/**
	 * isMember 	 Legacy_Group.IsMember
	 * check permission of the given category by user id.
	 *
	 * @param bool	&$check
	 * @param int	$groupId
	 * @param int	$uid
	 * @param Enum	$level
	 *
	 * @return	void
	 */ 
	abstract public function isMember(/*** bool ***/ &$check, /*** int ***/ $groupId, /*** int ***/ $uid, /*** Enum ***/ $level);

	/**
	 * getMyGroupsActivitiesList 	Legacy_Group.GetGroupActivitiesList
	 * get friends recent action list
	 *
	 * @param Legacy_AbstractGroupActivityObject[] &$actionList
	 * @param int	$uid
	 * @param int	$limit
	 * @param int	$start
	 *
	 * @return	void
	 */ 
	abstract public function getMyGroupsActivitiesList(/*** Legacy_AbstractGroupActivityObject[] ***/ &$actionList, /*** int ***/ $uid, /*** int ***/ $limit=20, /*** int ***/ $start=0);

}

?>
