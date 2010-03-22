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
	 * @param CriteriaElement $cri ex)$cri=new CriteriaCompo();$cri->add(new Criteria('dirname',$dirname);$cri->add(new Criteria('dataname',$dataname);$cri->add(new Criteria('id',$id);
	 *
	 * @return	void
	 */ 
	abstract public function deleteUserActivity(/*** bool ***/ &$result, /*** CriteriaElement ***/ $cri);

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
	abstract public function getUsersActivities(/*** Legacy_AbstractUserActivityObject[] ***/ &$activityList, /*** int[] ***/ $uids, /*** int ***/ $limit=20, /*** int ***/ $start=0);

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
	 * @param CriteriaElement $cri ex)$cri=new CriteriaCompo();$cri->add(new Criteria('dirname',$dirname);$cri->add(new Criteria('dataname',$dataname);$cri->add(new Criteria('id',$id);
	 *
	 * @return	void
	 */ 
	abstract public function deleteGroupActivity(/*** bool ***/ &$result, /*** CriteriaElement ***/ $cri);

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
	abstract public function getGroupsActivities(/*** Legacy_AbstractGroupActivityObject[] ***/ &$activityList, /*** int[] ***/ $groupIds, /*** int ***/ $limit=20, /*** int ***/ $start=0);

}

?>
