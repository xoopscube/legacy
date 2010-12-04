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
 * Interface of tag delegate
**/
interface Legacy_iTagDelegate
{
	/**
	 * setTags
	 *
	 * @param bool		$result
	 * @param string	$tDirname	//Legacy_Tag module's dirname
	 * @param string	$dirname
	 * @param string	$dataname
	 * @param int		$dataId
	 * @param int		$posttime
	 * @param string[]	$tagArr
	 */	
	public static function setTags($result, $tDirname, $dirname, $dataname, $dataId, $posttime, $tagArr);

	/**
	 * get tags from dirname/dataname/data_id
	 *
	 * @param string[] $tagArr
	 * @param string	$tDirname	//Legacy_Tag module's dirname
	 * @param string	$dirname
	 * @param string	$dataname
	 * @param int		$dataId
	 */	
	public static function getTags(&$tagArr, $tDirname, $dirname, $dataname, $dataId);

	/**
	 * getTagCloudSrc
	 *
	 * @param mixed		$cloud
	 *	 $cloud[$tag] = $count
	 * @param string	$tDirname	//Legacy_Tag module's dirname
	 * @param string	$dirname
	 * @param string	$dataname
	 * @param int[]		$uidList
	 */	
	public static function getTagCloudSrc(&$cloud, $tDirname, $dirname=null, $dataname=null, $uidList=array());

	/**
	 * getDataIdListByTags
	 *
	 * @param int[]		$list
	 * @param string	$tDirname	//Legacy_Tag module's dirname
	 * @param string[]	$tagArr
	 * @param string	$dirname
	 * @param string	$dataname
	 */	
	public static function getDataIdListByTags(&$list, $tDirname, $tagArr, $dirname, $dataname);

}

?>
