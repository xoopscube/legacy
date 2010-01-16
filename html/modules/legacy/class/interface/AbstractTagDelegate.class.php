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
	 * @param string $title
	 * @param string $url
	 * @param string $module
	 * @param string[] $tagArr
	 * @param string $dirname
	 */	
	abstract public function setTags($title, $url, $module, $tagArr, $dirname);

	/**
	 * getTagsByUrl
	 *
	 * @param string[] $tagArr
	 * @param string $url
	 * @param string $dirname
	 */	
	abstract public function getTagsByUrl(&$tagArr, $url, $dirname);

	/**
	 * getTagCloudSrc
	 *
	 * @param Letag_TagObject[] $cloud
	 * @param string[] $tagArr
	 * @param string $dirname
	 */	
	abstract public function getTagCloudSrc(&$cloud, $tagArr, $dirname);

	/**
	 * getContentsByTags
	 *
	 * @param Letag_BmObject[] $contents
	 * @param string[] $tagArr
	 * @param string $module
	 * @param string $dirname
	 */	
	abstract public function getContentsByTags(&$contents, $tagArr, $module, $dirname);

}

?>
