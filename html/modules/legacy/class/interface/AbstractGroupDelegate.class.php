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
	 * getMyGroupIdList Legacy_Group.GetMyGroupIdList
	 *
	 * @param int[] &$list
	 * @param int	$setId
	 * @param Enum	$rank	Lenum_GroupRank
	 *
	 * @return	void
	 */ 
	abstract public function getMyGroupIdList(/*** int[] ***/ &$list, /*** int ***/ $setId, /*** Enum ***/ $rank);

	/**
	 * getMyGroupIdListByDataname Legacy_Group.GetMyGroupIdListByDataname
	 *
	 * @param int[]		&$list
	 * @param int	$setId
	 * @param string	$dirname
	 * @param string	$dataname
	 *
	 * @return	void
	 */ 
	abstract public function getMyGroupIdListByDataname(/*** int[] ***/ &$list, /*** int ***/ $setId, /*** string ***/ $dirname, /*** string ***/ $dataname);

	/**
	 * getMyGroupList Legacy_Group.GetMyGroupList
	 *
	 * @param mixed[] &$list
	 *	  $list['group_id'][]	int
	 *	  $list['title'][]		string
	 *	  $list['url'][]		string
	 * @param int	$setId
	 * @param Enum	$rank	Lenum_GroupRank
	 * @param int	$limit
	 * @param int	$start
	 *
	 * @return	void
	 */ 
	abstract public function getMyGroupList(/*** mixed[] ***/ &$list, /*** int ***/ $setId, /*** Enum ***/ $rank, /*** int ***/ $limit=20, /*** int ***/ $start=0);

	/**
	 * getMemberList	  Legacy_Group.GetMemberList
	 * get member list in the given group
	 *
	 * @param mixed $list
	 *	$list['uid']
	 *	$list['rank']
	 * @param int	$setId
	 * @param int	$groupId
	 * @param Enum	$rank	Lenum_GroupRank
	 *
	 * @return	void
	 */ 
	abstract public function getMemberList(/*** int[] ***/ &$list, /*** int ***/ $setId, /*** int ***/ $groupId, /*** Enum ***/ $rank);

	/**
	 * isMember 	 Legacy_Group.IsMember
	 * check the user's belonging and rank in the given group
	 *
	 * @param int	$setId
	 * @param bool	&$check
	 * @param int	$groupId
	 * @param int	$uid
	 * @param Enum	$rank	Lenum_GroupRank
	 *
	 * @return	void
	 */ 
	abstract public function isMember(/*** bool ***/ &$check, /*** int ***/ $setId, /*** int ***/ $groupId, /*** int ***/ $uid, /*** Enum ***/ $rank=Lenum_GroupRank::REGULAR);

	/**
	 * getMyGroupsActivitiesList 	Legacy_Group.GetGroupActivitiesList
	 * get friends recent action list
	 *
	 * @param Legacy_AbstractGroupActivityObject[] &$actionList
	 * @param int	$setId
	 * @param int	$uid
	 * @param int	$limit
	 * @param int	$start
	 *
	 * @return	void
	 */ 
	abstract public function getMyGroupsActivitiesList(/*** Legacy_AbstractGroupActivityObject[] ***/ &$actionList, /*** int ***/ $setId, /*** int ***/ $uid, /*** int ***/ $limit=20, /*** int ***/ $start=0);

}

?>
