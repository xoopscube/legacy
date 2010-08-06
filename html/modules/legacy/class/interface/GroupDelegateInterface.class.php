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
interface Legacy_iGroupDelegate
{

	/**
	 * getTitle 	Legacy_Group.GetTitle
	 * get the group title by group id.
	 *
	 * @param string 	&$title
	 * @param string	$gDirname	Group Module Dirname
	 * @param int 		$groupId
	 *
	 * @return	void
	 */ 
	public function getTitle(/*** string ***/ &$title, /*** string ***/ $gDirname, /*** int ***/ $groupId);

	/**
	 * getTitleList 	Legacy_Group.GetTitleList
	 * get group titles.
	 *
	 * @param string[]	&$titleList
	 * @param string	$gDirname	Group Module Dirname
	 *
	 * @return	void
	 */ 
	public function getTitleList(/*** string[] ***/ &$titleList, /*** string ***/ $gDirname);

	/**
	 * hasPermission
	 *
	 * @param bool	 &$check
	 * @param string $gDirname
	 * @param int	 $groupId
	 * @param string $dirname
	 * @param string $dataname
	 * @param string $action
	 *
	 * @return	void
	 */ 
	public function hasPermission(/*** bool ***/ &$check, /*** string ***/ $gDirname, /*** int ***/ $groupId, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** string ***/ $action);

	/**
	 * getGroupIdList Legacy_Group.GetMyGroupIdList
	 *
	 * @param int[] 	&$list
	 * @param string	$gDirname	Group Module Dirname
	 * @param Enum	$rank	Lenum_GroupRank
	 * @param int		$limit
	 * @param int		$start
	 *
	 * @return	void
	 */ 
	public function getGroupIdList(/*** int[] ***/ &$list, /*** string ***/ $gDirname, /*** Enum ***/ $rank, /*** int ***/ $limit=null, /*** int ***/ $start=null);

	/**
	 * getGroupList Legacy_Group.GetMyGroupList
	 *
	 * @param Legacy_AbstractGroupObject[] &$list
	 * @param string	$gDirname	Group Module Dirname
	 * @param Enum		$rank	Lenum_GroupRank
	 * @param int		$limit
	 * @param int		$start
	 *
	 * @return	void
	 */ 
	public function getGroupList(/*** mixed[] ***/ &$list, /*** string ***/ $gDirname, /*** Enum ***/ $rank, /*** int ***/ $limit=null, /*** int ***/ $start=null);

	/**
	 * getGroupIdListByAction Legacy_Group.GetGroupIdListByAction
	 *
	 * @param int[] 	&$list
	 * @param string	$gDirname	Group Module Dirname
	 * @param string	$dirname
	 * @param string	$dataname
	 * @param string	$action
	 * @param int		$limit
	 * @param int		$start
	 *
	 * @return	void
	 */ 
	public function getGroupIdListByAction(/*** int[] ***/ &$list, /*** string ***/ $gDirname, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** string ***/ $action, /*** int ***/ $limit=null, /*** int ***/ $start=null);

	/**
	 * getGroupListByAction Legacy_Group.GetGroupListByAction
	 *
	 * @param Legacy_AbstractGroupObject[] &$list
	 * @param string	$gDirname	Group Module Dirname
	 * @param string	$dirname
	 * @param string 	$dataname
	 * @param string 	$action
	 * @param int		$limit
	 * @param int		$start
	 *
	 * @return	void
	 */ 
	public function getGroupListByAction(/*** mixed[] ***/ &$list, /*** string ***/ $gDirname, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** string ***/ $action, /*** int ***/ $limit=null, /*** int ***/ $start=null);

	/**
	 * getMemberList	  Legacy_Group.GetMemberList
	 * get member list in the given group
	 *
	 * @param mixed $list
	 *	$list['uid']
	 *	$list['rank']
	 * @param string	$gDirname	Group Module Dirname
	 * @param int		$groupId
	 * @param Enum		$rank	Lenum_GroupRank
	 *
	 * @return	void
	 */ 
	public function getMemberList(/*** int[] ***/ &$list, /*** string ***/ $gDirname, /*** int ***/ $groupId, /*** Enum ***/ $rank);

	/**
	 * isMember 	 Legacy_Group.IsMember
	 * check the user's belonging and rank in the given group
	 *
	 * @param string	$gDirname	Group Module Dirname
	 * @param bool	&$check
	 * @param int	$groupId
	 * @param int	$uid
	 * @param Enum	$rank	Lenum_GroupRank
	 *
	 * @return	void
	 */ 
	public function isMember(/*** bool ***/ &$check, /*** string ***/ $gDirname, /*** int ***/ $groupId, /*** int ***/ $uid, /*** Enum ***/ $rank=Lenum_GroupRank::REGULAR);

	/**
	 * getGroupsActivitiesList 	Legacy_Group.GetGroupActivitiesList
	 * get friends recent action list
	 *
	 * @param Legacy_AbstractGroupActivityObject[] &$actionList
	 * @param string	$gDirname	Group Module Dirname
	 * @param int	$uid
	 * @param int	$limit
	 * @param int	$start
	 *
	 * @return	void
	 */ 
	public function getGroupsActivitiesList(/*** Legacy_AbstractGroupActivityObject[] ***/ &$actionList, /*** string ***/ $gDirname, /*** int ***/ $uid, /*** int ***/ $limit=20, /*** int ***/ $start=0);
}

?>
