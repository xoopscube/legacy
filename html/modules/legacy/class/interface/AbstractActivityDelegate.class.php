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
 * Interface of acitivity delegate
**/
abstract class Legacy_AbstractActivityDelegate
{
	/**
	 * addUserActivity	 Legacy_Activity.AddUserActivity
	 *
	 * @param Legacy_AbstractUserActivityObject &$activity
	 *
	 * @return	void
	 */ 
	abstract public function addUserActivity(/*** Legacy_AbstractUserActivityObject ***/ &$activity);

	/**
	 * deleteUserActivity	Legacy_Activity.DeleteUserActivity
	 *
	 * @param bool &$result
	 * @param string $dirname
	 * @param string $dataname
	 * @param int $id
	 *
	 * @return	void
	 */ 
	abstract public function deleteUserActivity(/*** bool ***/ &$result, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** int ***/ $id);

	/**
	 * getUsersActivities	Legacy_Activity.GetUsersAcitivities
	 *
	 * @param Legacy_AbstractActivityObject[]	&$activityList
	 * @param int[]	$uid[]
	 * @param int	$start
	 * @param int	$limit
	 *
	 * @return	void
	 */ 
	abstract public function getUsersActivities(/*** Legacy_AbstractUserActivityObject[] ***/ &$activityList, /*** int[] ***/ $uid[], /*** int ***/ $start, /*** int ***/ $limit);

	/**
	 * addGroupActivity	 Legacy_Activity.AddGroupActivity
	 *
	 * @param Legacy_AbstractUserActivityObject &$activity
	 *
	 * @return	void
	 */ 
	abstract public function addGroupActivity(/*** Legacy_AbstractGroupActivityObject ***/ &$activity);

	/**
	 * deleteGroupActivity	Legacy_Activity.deleteGroupActivity
	 *
	 * @param bool &$result
	 * @param string $dirname
	 * @param string $dataname
	 * @param int $id
	 *
	 * @return	void
	 */ 
	abstract public function deleteGroupActivity(/*** bool ***/ &$result, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** int ***/ $id);

	/**
	 * getGroupsActivities	 Legacy_Activity.GetGroupsAcitivities
	 *
	 * @param Legacy_AbstractActivityObject[]	&$activityList
	 * @param int	$groupId[]
	 * @param int	$start
	 * @param int	$limit
	 *
	 * @return	void
	 */ 
	abstract public function getGroupsActivities(/*** Legacy_AbstractGroupActivityObject[] ***/ &$activityList, /*** int ***/ $groupId[], /*** int ***/ $start, /*** int ***/ $limit);

}

?>
