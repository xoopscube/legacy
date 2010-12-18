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
 * Interface of image client delegate
 * Modules which uses Legacy_Image must implement this interface.
 * Legacy_Image module must be unique.
 * You can get its dirname by constant LEGACY_IMAGE_DIRNAME
*/
interface Legacy_iImageClientDelegate
{
	/**
	 * getClientList	Legacy_Image.{dirname}.GetClientList
	 * Get client module's dirname and dataname(tablename)
	 *
	 * @param mixed[] &$list
	 *  $list[]['dirname']	client module dirname
	 *  $list[]['dataname']	client module dataname(tablename)
	 *
	 * @return	void
	 */ 
	public static function getClientList(/*** array ***/ &$list);
}
?>
