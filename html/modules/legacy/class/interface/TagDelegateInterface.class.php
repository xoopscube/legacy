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
	 * @param string $dirname
	 * @param string $dataname
	 * @param int	$data_id
	 * @param string[] $tagArr
	 * @param int $posttime
	 * @param string $title
	 * @param string $url
	 */	
	public function setTags($dirname, $dataname, $data_id, $tagArr, $posttime, $title, $url);

	/**
	 * getTags
	 *
	 * @param string[] $tagArr
	 * @param string $dirname
	 * @param string $dataname
	 * @param int $data_id
	 */	
	public function getTags(&$tagArr, $dirname, $dataname, $data_id);

	/**
	 * getTagCloudSrc
	 *
	 * @param mixed		$cloud
	 *	 @param string	$cloud['tag'][]
	 *	 @param int		$cloud['count'][]
	 * @param int	$setId
	 */	
	public function getTagCloudSrc(&$cloud, $setId=0);

	/**
	 * getContentsByTags
	 *
	 * @param id[]		$idList
	 * @param string	$dirname
	 * @param string	$dataname
	 * @param string[]	$tagArr
	 */	
	public function getContentsByTags(&$idList, $dirname, $dataname, $tagArr);

}

?>
