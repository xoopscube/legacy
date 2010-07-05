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
	 * getTitle
	 *
	 * @param string 	&$title
	 * @param string 	$catDir	category module's directory name
	 * @param int 		$catId
	 *
	 * @return	void
	 */ 
	public function getTitle(/*** string ***/ &$title, /*** string ***/ $catDir, /*** int ***/ $catId);

	/**
	 * getTree
	 * Get category Legacy_AbstractCategoryObject array in parent-child tree order
	 *
	 * @param Legacy_AbstractCategoryObject[] $tree
	 * @param string $catDir	category module's directory name
	 * @param string 	$authType	ex) viewer, editor, manager
	 * @param int 		$uid
	 * @param int 		$catId	get tree under this cat_id
	 * @param string	$module module confinement
	 *
	 * @return	void
	 */ 
	public function getTree(/*** Legacy_AbstractCategoryObject[] ***/ &$tree, /*** string ***/ $catDir, /*** string ***/ $authType, /*** int ***/ $uid, /*** int ***/ $catId=0, /*** string ***/ $module=null);

	/**
	 * getTitleList
	 *
	 * @param string &$titleList
	 * @param string $catDir	category module's directory name
	 *
	 * @return	void
	 */ 
	public function getTitleList(/*** string[] ***/ &$titleList, /*** string ***/ $catDir);

	/**
	 * checkPermitByUserId
	 *
	 * @param bool &$check
	 * @param string	$catDir	category module's directory name
	 * @param int		$catId
	 * @param string	$authType	ex) viewer, editor, manager
	 * @param int		$uid
	 * @param string	$module	module confinement
	 *
	 * @return	void
	 */ 
	public function checkPermitByUserId(/*** bool ***/ &$check, /*** string ***/ $catDir, /*** int ***/ $catId, /*** string ***/ $authType, /*** int ***/ $uid, /*** string ***/ $module=null);

	/**
	 * checkPermitByGroupId
	 *
	 * @param bool		&$check
	 * @param string	$catDir	category module's directory name
	 * @param int		$catId
	 * @param string	$authType	ex) viewer, editor, manager
	 * @param int		$groupId
	 * @param string	$module	 module confinement
	 *
	 * @return	void
	 */ 
	public function checkPermitByGroupId(/*** bool ***/ &$check, /*** string ***/ $catDir, /*** int ***/ $catId, /*** string ***/ $authType, /*** int ***/ $groupId, /*** string ***/ $module=null);

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
	public function getParent(/*** Legacy_AbstractCategoryObject ***/ &$parent, /*** string ***/ $catDir, /*** int ***/ $catId);

	/**
	 * getChildren		Legacy_Category.GetChildren
	 * get the child category objects. Be careful that you can get only children objects, excluded the given category itself.
	 *
	 * @param Legacy_AbstractCategoryObject[] &$children
	 * @param string	$catDir	category module's directory name
	 * @param int		$catId	the parent's category id
	 * @param string	$authType	ex) viewer, editor, manager
	 * @param int		$uid
	 * @param string	$module	 module confinement
	 *
	 * @return	void
	 */ 
	public function getChildren(/*** Legacy_AbstractCategoryObject[] ***/ &$children, /*** string ***/ $catDir, /*** int ***/ $catId, /*** string ***/ $authType, /*** int ***/ $uid, /*** string ***/ $module=null);

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
	public function getCatPath(/*** array ***/ &$catPath, /*** string ***/ $catDir, /*** int ***/ $catId, /*** string ***/ $order='ASC');

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
	public function getPermittedIdList(/*** int[] ***/ &$idList, /*** string ***/ $catDir, /*** string ***/ $authType, /*** int ***/ $uid, /*** int ***/ $catId=0, /*** string ***/ $module=null);

}

?>
