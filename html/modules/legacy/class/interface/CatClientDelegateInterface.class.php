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
	 * getClientList	Legacy_CategoryClient.{dirname}.GetClientList
	 *
	 * @param mixed[]	&$list
	 *  list[]['dirname']	client module's dirname
	 *  list[]['dataname']	client module's dataname(tablename)
	 * @param string	$dirname	Legacy_Category's dirname
	 *
	 * @return	void
	 */ 
	public static function getClientList(/*** mixed[] ***/ &$list, /*** string ***/ $dirname);

	/**
	 * getClientData	Legacy_CategoryClient.{dirname}.GetClientData
	 * Get client modules' data to show them inside LEGACY_CATEGORY module
	 *
	 * @param mixed[]	&$list
	 *  list[]['dirname']	string	client module's dirname
	 *  list[]['dataname']	string	client module's dataname(tablename)
	 *  list[]['title']	string	client module's title
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
