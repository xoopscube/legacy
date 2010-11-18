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
 * Interface of cat client delegate
 * Modules which uses LEGACY_CATEGORY must implement this interface.
**/
interface Legacy_iCategoryClientDelegate
{
	/**
	 * getClientList
	 *
	 * @param mixed[]	&$list
	 *  list[]['dirname']
	 *  list[]['dataname']
	 * @param string	$dirname
	 *
	 * @return	void
	 */ 
	public static function getClientList(/*** mixed[] ***/ &$list, /*** string ***/ $dirname);

	/**
	 * getClientData
	 * Get client modules' data to show them inside LEGACY_CATEGORY module
	 *
	 * @param mixed[]	&$list
	 *  list[]['dirname']	string
	 *  list[]['dataname']	string
	 *  list[]['title']	string
	 *  list[]['data']	mixed
	 *  list[]['template_name']	string
	 * @param string	$dirname	client module's dirname
	 * @param string	$dataname	client's target tablename
	 * @param string	$fieldname	client's target fieldname
	 * @param int		$catId
	 *
	 * @return	void
	 */ 
	public static function getClientData(/*** mixed ***/ &$list, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** string ***/ $fieldname, /*** int ***/ $catId);
}
?>
