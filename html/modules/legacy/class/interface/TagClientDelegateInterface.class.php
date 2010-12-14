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
 * Interface of group client delegate
 * Modules which uses Legacy_Tag must implement this interface.
**/
interface Legacy_iTagClientDelegate
{
	/**
	 * getClientList
	 *
	 * @param mixed[]	&$list
	 *  @list[]['dirname']
	 *  @list[]['dataname']
	 *  @list[]['fieldname']
	 * @param string	$tDirname	Legacy_Tag module's dirname
	 *
	 * @return	void
	 */ 
	public static function getClientList(/*** mixed[] ***/ &$list, /*** string ***/ $tDirname);

	/**
	 * getClientData
	 *
	 * @param mixed		&$list
	 * @param string	$dirname
	 * @param string	$dataname
	 * @param int[]		$idList
	 *
	 * @return	void
	 */ 
	public static function getClientData(/*** mixed ***/ &$list, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** int[] ***/ $idList);
}
?>
