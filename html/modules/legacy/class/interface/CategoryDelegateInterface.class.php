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
 * Interface of category delegate
**/
interface Legacy_iCategoryDelegate
{
	/**
	 * getTitle		Legacy_Category.{dirname}.GetTitle
	 *
	 * @param string 	&$title	category title
	 * @param string 	$catDir	category module's directory name
	 * @param int 		$catId	category id
	 *
	 * @return	void
	 */ 
	public static function getTitle(/*** string ***/ &$title, /*** string ***/ $catDir, /*** int ***/ $catId);

	/**
	 * getTree	Legacy_Category.{dirname}.GetTree
	 * Get category Legacy_AbstractCategoryObject array in parent-child tree order
	 *
	 * @param Legacy_AbstractCategoryObject[] $tree
	 * @param string $catDir	category module's dirname
	 * @param string 	$authType	ex) viewer, editor, manager
	 * @param int 		$catId	get tree under this cat_id
	 * @param string	$module module confinement
	 *
	 * @return	void
	 */ 
	public static function getTree(/*** Legacy_AbstractCategoryObject[] ***/ &$tree, /*** string ***/ $catDir, /*** string ***/ $authType, /*** int ***/ $catId=0, /*** string ***/ $module=null);

	/**
	 * getTitleList		Legacy_Category.{dirname}.GetTitleList
	 *
	 * @param string &$titleList	category title array
	 * @param string $catDir	category module's dirname
	 *
	 * @return	void
	 */ 
	public static function getTitleList(/*** string[] ***/ &$titleList, /*** string ***/ $catDir);

	/**
	 * hasPermission	Legacy_Category.{dirname}.HasPermission
	 *
	 * @param bool &$check
	 * @param string	$catDir	category module's dirname
	 * @param int		$catId	category id
	 * @param string	$authType	ex) viewer, editor, manager
	 * @param string	$module	module confinement
	 *
	 * @return	void
	 */ 
	public static function hasPermission(/*** bool ***/ &$check, /*** string ***/ $catDir, /*** int ***/ $catId, /*** string ***/ $authType, /*** string ***/ $module=null);

	/**
	 * getParent		Legacy_Category.{dirname}.GetParent
	 * get the parent category object.
	 *
	 * @param Legacy_AbstractCategoryObject &$parent
	 * @param string 	$catDir	category module's dirname
	 * @param int 		$catId	category id
	 *
	 * @return	void
	 */ 
	public static function getParent(/*** Legacy_AbstractCategoryObject ***/ &$parent, /*** string ***/ $catDir, /*** int ***/ $catId);

	/**
	 * getChildren		Legacy_Category.{dirname}.GetChildren
	 * get the child category objects. Be careful that you can get only children objects, excluded the given category itself.
	 *
	 * @param Legacy_AbstractCategoryObject[] &$children
	 * @param string	$catDir	category module's dirname
	 * @param int		$catId	the parent's category id
	 * @param string	$authType	ex) viewer, editor, manager
	 * @param string	$module	 module confinement
	 *
	 * @return	void
	 */ 
	public static function getChildren(/*** Legacy_AbstractCategoryObject[] ***/ &$children, /*** string ***/ $catDir, /*** int ***/ $catId, /*** string ***/ $authType, /*** string ***/ $module=null);

	/**
	 * getCatPath		Legacy_Category.{dirname}.GetCatPath
	 * get category path array from top to the given category.
	 *
	 * @param string[] &$catPath
	 *	 $catPath['cat_id']
	 *	 $catPath['title']
	 * @param string $catDir	category module's dirname
	 * @param int $catId		terminal category id in the category path
	 * @param string $order		'ASC' or 'DESC'
	 *
	 * @return	void
	 */ 
	public static function getCatPath(/*** array ***/ &$catPath, /*** string ***/ $catDir, /*** int ***/ $catId, /*** string ***/ $order='ASC');

	/**
	 * getPermittedIdList		Legacy_Category.{dirname}.GetPermittedIdList
	 * get category ids of permission.
	 *
	 * @param int[]		&$idList
	 * @param string	$catDir	category module's dirname
	 * @param string	$authType	ex) viewer, editor, manager
	 * @param int		$uid
	 * @param int		$catId	get result under this cat_id
	 * @param string	$module	 module confinement
	 *
	 * @return	void
	 */ 
	public static function getPermittedIdList(/*** int[] ***/ &$idList, /*** string ***/ $catDir, /*** string ***/ $authType, /*** int ***/ $uid, /*** int ***/ $catId=0, /*** string ***/ $module=null);

}

?>
