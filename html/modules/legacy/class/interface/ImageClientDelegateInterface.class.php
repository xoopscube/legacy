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
	 * getClientModules
	 * Get client module's dirname and dataname(tablename)
	 *
	 * @param mixed[] &$list
	 *
	 * @return	void
	 */ 
	public static function getClientModules(/*** array ***/ &$list);
}
?>
