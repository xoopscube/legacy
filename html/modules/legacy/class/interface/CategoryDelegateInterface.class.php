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
	 * getServerDirname Site.GetServerDirname
	 * get server module's dirname from client module's info.
	 *
	 * @param string	&$server	server module's dirname
	 * @param string	$dirname	client module's dirname
	 * @param string	$dataname	client's target tablename
	 * @param string	$fieldname	client's target fieldname
	 *
	 * @return	void
	 */ 
	public static function getServerDirname(/*** string ***/ &$server, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** string ***/ $fieldname=null);

	/**
	 * getTitle
	 *
	 * @param string 	&$title
	 * @param string 	$catDir	category module's directory name
	 * @param int 		$catId
	 *
	 * @return	void
	 */ 
	public static function getTitle(/*** string ***/ &$title, /*** string ***/ $catDir, /*** int ***/ $catId);

	/**
	 * getTree
	 * Get category Legacy_AbstractCategoryObject array in parent-child tree order
	 *
	 * @param Legacy_AbstractCategoryObject[] $tree
	 * @param string $catDir	category module's directory name
	 * @param string 	$authType	ex) viewer, editor, manager
	 * @param int 		$catId	get tree under this cat_id
	 * @param string	$module module confinement
	 *
	 * @return	void
	 */ 
	public static function getTree(/*** Legacy_AbstractCategoryObject[] ***/ &$tree, /*** string ***/ $catDir, /*** string ***/ $authType, /*** int ***/ $catId=0, /*** string ***/ $module=null);

	/**
	 * getTitleList
	 *
	 * @param string &$titleList
	 * @param string $catDir	category module's directory name
	 *
	 * @return	void
	 */ 
	public static function getTitleList(/*** string[] ***/ &$titleList, /*** string ***/ $catDir);

	/**
	 * hasPermission
	 *
	 * @param bool &$check
	 * @param string	$catDir	category module's directory name
	 * @param int		$catId
	 * @param string	$authType	ex) viewer, editor, manager
	 * @param string	$module	module confinement
	 *
	 * @return	void
	 */ 
	public static function hasPermission(/*** bool ***/ &$check, /*** string ***/ $catDir, /*** int ***/ $catId, /*** string ***/ $authType, /*** string ***/ $module=null);

	/**
	 * getParent		Legacy_Category.GetParent
	 * get the parent category object.
	 *
	 * @param Legacy_AbstractCategoryObject &$parent
	 * @param string 	$catDir	category module's directory name
	 * @param int 		$catId
	 *
	 * @return	void
	 */ 
	public static function getParent(/*** Legacy_AbstractCategoryObject ***/ &$parent, /*** string ***/ $catDir, /*** int ***/ $catId);

	/**
	 * getChildren		Legacy_Category.GetChildren
	 * get the child category objects. Be careful that you can get only children objects, excluded the given category itself.
	 *
	 * @param Legacy_AbstractCategoryObject[] &$children
	 * @param string	$catDir	category module's directory name
	 * @param int		$catId	the parent's category id
	 * @param string	$authType	ex) viewer, editor, manager
	 * @param string	$module	 module confinement
	 *
	 * @return	void
	 */ 
	public static function getChildren(/*** Legacy_AbstractCategoryObject[] ***/ &$children, /*** string ***/ $catDir, /*** int ***/ $catId, /*** string ***/ $authType, /*** string ***/ $module=null);

	/**
	 * getCatPath		Legacy_Category.GetCatPath
	 * get category path array from top to the given category.
	 *
	 * @param string[] &$catPath
	 *	 $catPath['cat_id']
	 *	 $catPath['title']
	 * @param string $catDir	category module's directory name
	 * @param int $catId		terminal category id in the category path
	 * @param string $order		'ASC' or 'DESC'
	 *
	 * @return	void
	 */ 
	public static function getCatPath(/*** array ***/ &$catPath, /*** string ***/ $catDir, /*** int ***/ $catId, /*** string ***/ $order='ASC');

	/**
	 * getPermittedIdList		Legacy_Category.GetPermittedIdList
	 * get category ids of permission.
	 *
	 * @param int[]		&$idList
	 * @param string	$catDir	category module's directory name
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
