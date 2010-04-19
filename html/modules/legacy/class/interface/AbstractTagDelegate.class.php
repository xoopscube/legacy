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
	 * @param int	$data_id
	 * @param string[] $tagArr
	 * @param int $posttime
	 * @param string $title
	 * @param string $url
	 */	
	abstract public function setTags($dirname, $dataname, $data_id, $tagArr, $posttime, $title, $url);

	/**
	 * getTags
	 *
	 * @param string[] $tagArr
	 * @param string $dirname
	 * @param string $dataname
	 * @param int $data_id
	 */	
	abstract public function getTags(&$tagArr, $dirname, $dataname, $data_id);

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
