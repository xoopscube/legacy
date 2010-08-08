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
interface Legacy_iActivityDelegate
{
	/**
	 * addUserActivity	 Legacy_Activity.AddUserActivity
	 *
	 * @param Legacy_AbstractUserActivityObject &$activity
	 *
	 * @return	void
	 */ 
	public static function addUserActivity(/*** Legacy_AbstractUserActivityObject ***/ &$activity);

	/**
	 * deleteUserActivity	Legacy_Activity.DeleteUserActivity
	 *
	 * @param bool &$result
	 * @param CriteriaElement $cri ex)$cri=new CriteriaCompo();$cri->add(new Criteria('dirname',$dirname);$cri->add(new Criteria('dataname',$dataname);$cri->add(new Criteria('data_id',$data_id);
	 *
	 * @return	void
	 */ 
	public static function deleteUserActivity(/*** bool ***/ &$result, /*** CriteriaElement ***/ $cri);

	/**
	 * getUsersActivities	Legacy_Activity.GetUsersAcitivities
	 *
	 * @param Legacy_AbstractActivityObject[]	&$activityList
	 * @param int[]	$uids
	 * @param int	$limit
	 * @param int	$start
	 *
	 * @return	void
	 */ 
	public static function getUsersActivities(/*** Legacy_AbstractUserActivityObject[] ***/ &$activityList, /*** int[] ***/ $uids, /*** int ***/ $limit=20, /*** int ***/ $start=0);

	/**
	 * addGroupActivity	 Legacy_Activity.AddGroupActivity
	 *
	 * @param Legacy_AbstractUserActivityObject &$activity
	 *
	 * @return	void
	 */ 
	public static function addGroupActivity(/*** Legacy_AbstractGroupActivityObject ***/ &$activity);

	/**
	 * deleteGroupActivity	Legacy_Activity.deleteGroupActivity
	 *
	 * @param bool &$result
	 * @param CriteriaElement $cri ex)$cri=new CriteriaCompo();$cri->add(new Criteria('dirname',$dirname);$cri->add(new Criteria('dataname',$dataname);$cri->add(new Criteria('data_id',$data_id);
	 *
	 * @return	void
	 */ 
	public static function deleteGroupActivity(/*** bool ***/ &$result, /*** CriteriaElement ***/ $cri);

	/**
	 * getGroupsActivities	 Legacy_Activity.GetGroupsAcitivities
	 *
	 * @param Legacy_AbstractActivityObject[]	&$activityList
	 * @param int[]	$groupIds
	 * @param int	$limit
	 * @param int	$start
	 *
	 * @return	void
	 */ 
	public static function getGroupsActivities(/*** Legacy_AbstractGroupActivityObject[] ***/ &$activityList, /*** int[] ***/ $groupIds, /*** int ***/ $limit=20, /*** int ***/ $start=0);

}

?>
