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
 * Interface of friend delegate
**/
interface Legacy_iFriendDelegate
{
	/**
	 * getFriendIdList Legacy_Friend.GetFriendIdList
	 * @comment $list should be sorted by recent friends.
	 *
	 * @param int[] &$list
	 * @param int	$uid
	 *
	 * @return	void
	 */ 
	public static function getFriendIdList(/*** int[] ***/ &$list, /*** int ***/ $uid);

	/**
	 * isFriend 	Legacy_Friend.IsFriend
	 * check she is a friend
	 *
	 * @param bool	&$check
	 * @param int	$uid
	 * @param int	$friend_uid
	 *
	 * @return	void
	 */ 
	public static function isFriend(/*** bool ***/ &$check, /*** int ***/ $uid, /*** int ***/ $friend_uid);

	/**
	 * getMyFriendsActivitiesList 	Legacy_Friend.GetFriendsActivitiesList
	 * get friends recent action list
	 *
	 * @param Legacy_AbstractUserActivityObject[] &$actionList
	 * @param int	$uid
	 * @param int	$limit
	 * @param int	$start
	 *
	 * @return	void
	 */ 
	public static function getMyFriendsActivitiesList(/*** Legacy_AbstractUserActivityObject[] ***/ &$actionList, /*** int ***/ $uid, /*** int ***/ $limit=20, /*** int ***/ $start=0);


}

?>
