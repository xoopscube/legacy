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
abstract class Legacy_AbstractFriendDelegate
{
	/**
	 * getFriendIdList Legacy_Friend.GetFriendIdList
	 *
	 * @param int[] &$friendList
	 * @param int	$uid
	 *
	 * @return	void
	 */ 
	abstract public function getFriendIdList(/*** int[] ***/ &$friendList, /*** int ***/ $uid);

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
	abstract public function isFriend(/*** bool ***/ &$check, /*** int ***/ $uid, /*** int ***/ $friend_uid);

	/**
	 * getFriend	 Legacy_Friend.GetFriend
	 * get friend Object
	 *
	 * @param XoopsSimpleObject &$friend
	 * @param int	$uid
	 * @param int	$friend_uid
	 *
	 * @return	void
	 */ 
	abstract public function getFriend(/*** XoopsSimpleObject ***/ &$frined, /*** int ***/ $uid, /*** int ***/ $friend_uid);

	/**
	 * getPendingFriendIdList Legacy_Friend.GetPendingFriendIdList
	 *
	 * @param int[] &$friendList
	 * @param int	$uid
	 *
	 * @return	void
	 */ 
	abstract public function getPendingFriendIdList(/*** id[] ***/ &$friendList, /*** int ***/ $uid);

	/**
	 * getMyFriendsActivitiesList 	Legacy_Friend.GetFriendsActivitiesList
	 * get friends recent action list
	 *
	 * @param Legacy_AbstractUserActivityObject[] &$actionList
	 * @param int	$uid
	 * @param int	$start
	 * @param int	$limit
	 *
	 * @return	void
	 */ 
	abstract public function getMyFriendsActivitiesList(/*** Legacy_AbstractUserActivityObject[] ***/ &$actionList, /*** int ***/ $uid, /*** int ***/ $start=0, , /*** int ***/ $limit=20);


}

?>
