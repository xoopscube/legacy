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
 * Interface of module's acitivity delegate
 * Legacy_Activity module must be unique.
 * You can get its dirname by constant LEGACY_ACTIVITY_DIRNAME
**/
interface Legacy_iActivityDelegate
{
	/**
	 * addActivity	Legacy_Activity.AddActivity
	 *
	 * @param bool		&$result
	 * @param int		$uid		poster's user id
	 * @param int		$categoryId	access controller id
	 * @param string	$dirname	client module's dirname
	 * @param string	$dataname	client module's dataname(tablename)
	 * @param int		$dataId		client module's primary key
	 * @param int		$pubdate	entry's published date(unixtime)
	 *
	 * @return	void
	 */ 
	public static function addActivity(/*** bool ***/ &$result, /*** int ***/ $uid, /*** int ***/ $categoryId, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** int ***/ $dataId, /*** int ***/ $pubdate);

	/**
	 * deleteActivity	Legacy_Activity.DeleteActivity
	 *
	 * @param bool		&$result
	 * @param string	$dirname	client module's dirname
	 * @param string	$dataname	client module's dataname(tablename)
	 * @param int		$dataId		client module's primary key
	 *
	 * @return	void
	 */ 
	public static function deleteActivity(/*** bool ***/ &$result, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** int ***/ $dataId);

	/**
	 * getActivities	Legacy_Activity.GetActivity
	 *
	 * @param mixed[]	&$list
	 *  string	$list['dirname']	client module's dirname
	 *  string	$list['dataname']	client module's dataname(tablename)
	 *  int		$list['data_id']	client module's primary key
	 *  mixed	$list['data']
	 *  string  $list['title']		client module's title
	 *  string	$list['template_name']
	 * @param mixed[]	$categoryArr	access controller's info
	 *  string	$categoryArr['dirname']	access controller's dirname
	 *  int[]	$categoryArr['id']		access controller's id list
	 * @param mixed		$moduleArr
	 *  string	$moduleArr['dirname']
	 *  string	$moduleArr['dataname']
	 * @param int		$uid		poster's uid
	 * @param int		$limit		the number of returned entries
	 * @param int		$start		offset value
	 *
	 * @return	void
	 */ 
	public static function getActivities(/*** mixed[] ***/ &$list, /*** mixed[] ***/ $categoryArr=null, /*** mixed[] ***/ $moduleArr=null, /*** int ***/ $uid, /*** int ***/ $limit, /*** int ***/ $start);
}

?>
