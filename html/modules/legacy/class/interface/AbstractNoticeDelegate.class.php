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
 * Interface of notice delegate
**/
abstract class Legacy_AbstractNoticeDelegate
{
	/**
	 * getNoticeItems	Legacy_Notice.GetNoticeItems
	 *
	 * @param mix[] &$notice
	 *	$notice['dirname']
	 *	$notice['data_type']
	 *	$notice['summary']
	 *	$notice['posttime']
	 *	$notice['description']
	 *	$notice['url']
	 *	$notice['id']
	 * @param int $end
	 * @param int $uid
	 *
     * @return  void
	 */	
	abstract public function getNoticeItems(/*** mix[] ***/ &$notice, /*** int ***/ $uid);

}

?>
