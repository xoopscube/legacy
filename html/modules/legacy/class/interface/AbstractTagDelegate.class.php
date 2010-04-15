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
abstract class Legacy_AbstractTagDelegate
{

	/**
	 * setTags
	 *
	 * @param string $dirname
	 * @param string $dataname
	 * @param int	$id
	 * @param string $url
	 * @param string $title
	 * @param string[] $tagArr
	 * @param int $posttime
	 */	
	abstract public function setTags($dirname, $dataname, $id, $url, $title, $tagArr, $posttime);

	/**
	 * getTags
	 *
	 * @param string[] $tagArr
	 * @param string $dirname
	 * @param string $dataname
	 * @param int $id
	 */	
	abstract public function getTags(&$tagArr, $dirname, $dataname, $id);

	/**
	 * getTagsByUrl
	 *
	 * @param string[]	$tagArr
	 * @param string	$url
	 */	
	abstract public function getTagsByUrl(&$tagArr, $url);

	/**
	 * getTagCloudSrc
	 *
	 * @param mixed		$cloud
	 *	 @param string	$cloud['tag'][]
	 *	 @param int		$cloud['count'][]
	 * @param int	$setId
	 */	
	abstract public function getTagCloudSrc(&$cloud, $setId=0);

	/**
	 * getContentsByTags
	 *
	 * @param id[]		$idList
	 * @param string	$dirname
	 * @param string	$dataname
	 * @param string[]	$tagArr
	 */	
	abstract public function getContentsByTags(&$idList, $dirname, $dataname, $tagArr);

}

?>
