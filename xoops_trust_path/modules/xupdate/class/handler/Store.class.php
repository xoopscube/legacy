<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit;
}

/**
 * Xupdate_StoreObject
**/
class Xupdate_StoreObject extends XoopsSimpleObject
{
	/**
	 * __construct
	 * 
	 * @param	void
	 * 
	 * @return	void
	**/
	public function __construct()
	{
		$this->initVar('sid', XOBJ_DTYPE_INT, '0', false);
		$this->initVar('uid', XOBJ_DTYPE_INT, '0', false);
		$this->initVar('valid', XOBJ_DTYPE_INT, '0', false);
		$this->initVar('name', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('addon_url', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('theme_url', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('reg_unixtime', XOBJ_DTYPE_INT, '0', false);

	}

}

/**
 * Xupdate_StoreHandler
**/
class Xupdate_StoreHandler extends XoopsObjectGenericHandler
{
	public /*** string ***/ $mTable = '{dirname}_store';

	public /*** string ***/ $mPrimary = 'sid';

	public /*** string ***/ $mClass = 'Xupdate_StoreObject';

	/**
	 * __construct
	 * 
	 * @param	XoopsDatabase  &$db
	 * @param	string	$dirname
	 * 
	 * @return	void
	**/
	public function __construct(/*** XoopsDatabase ***/ &$db,/*** string ***/ $dirname)
	{
		$this->mTable = strtr($this->mTable,array('{dirname}' => $dirname));
		parent::XoopsObjectGenericHandler($db);
	}

}

?>
