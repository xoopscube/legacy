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
 * Modules which uses Legacy_Group must implement this interface.
**/
interface Legacy_iGroupClientDelegate
{
	/**
	 * getClientData
	 * Get client modules' data to show them inside Legacy_Group module
	 *
	 * @param mixed[]	&$list
	 *  list[]['data']	mixed
	 *  list[]['template']	string
	 * @param string	$gDirname	//Legacy_Group module's dirname
	 * @param int		$groupId
	 *
	 * @return	void
	 */ 
	public static function getClientData(/*** mixed ***/ &$list, /*** string ***/ $gDirname, /*** int ***/ $groupId);

	/**
	 * getActionList
	 * Get client module's actions(view, edit, etc) to set their permission
	 * by member's group rank.
	 *
	 * @param mixed[]	&$list
	 *	$list['action'][]	string
	 *	$list['rank'][]		Lenum_GroupRank
	 *	$list['title'][]	string
	 *	$list['desctiption'][]	string
	 * @param string	$dirname
	 * @param string	$dataname
	 *
	 * @return	void
	 */ 
	public static function getActionList(/*** mixed[] ***/ &$list, /*** string ***/ $dirname, /*** string ***/ $dataname);
}
?>
