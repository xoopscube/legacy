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
*/
interface Legacy_iImageClientDelegate
{
	/**
	 * getClientList
	 * Get client module's dirname and dataname(tablename)
	 *
	 * @param mixed[] &$list
	 *  $list[]['dirname']
	 *  $list[]['dataname']
	 *
	 * @return	void
	 */ 
	public static function getClientList(/*** array ***/ &$list);
}
?>
