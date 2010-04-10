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
abstract class Legacy_AbstractCategoryDelegate
{
	/**
	 * getCategorySetList Legacy_Category.GetCategorySetList
	 *
	 * @param string[] &$setList :key should be set_id
	 *
	 * @return	void
	 */ 
	abstract public function getCategorySetList(/*** string[] ***/ &$setList);

	/**
	 * getTitle 	Legacy_Category.GetTitle
	 * get the category title by category id.
	 *
	 * @param string &$title
	 * @param int $catId
	 *
	 * @return	void
	 */ 
	abstract public function getTitle(/*** string ***/ &$title, /*** int ***/ $catId);

	/**
	 * getTree		Legacy_Category.GetTree
	 * get category objects in the form of tree.
	 *
	 * @param Legacy_AbstractCategoryObject[] $tree
	 * @param int $setId :category set id
	 * @param string $action	:ex.view, edit
	 * @param int $uid
	 * @param int $catId
	 * @param string $module
	 *
	 * @return	void
	 */ 
	abstract public function getTree(/*** Legacy_AbstractCategoryObject[] ***/ &$tree, /*** int ***/ $setId, /*** string ***/ $action, /*** int ***/ $uid, /*** int ***/ $catId=0, /*** string ***/ $module="");

	/**
	 * getTitleList 	Legacy_Category.GetTitleList
	 * get category titles.
	 *
	 * @param string[] &$titleList
	 * @param int $setId :category set id
	 *
	 * @return	void
	 */ 
	abstract public function getTitleList(/*** string[] ***/ &$titleList, /*** int ***/ $setId);

	/**
	 * checkPermitByUserId		Legacy_Category.CheckPermitByUserId
	 * check permission of the given category by user id.
	 *
	 * @param bool &$check
	 * @param int $catId
	 * @param string $action
	 * @param int $uid
	 * @param string $module
	 *
	 * @return	void
	 */ 
	abstract public function checkPermitByUserId(/*** bool ***/ &$check, /*** int ***/ $catId, /*** string ***/ $action, /*** int ***/ $uid, /*** string ***/ $module="");

	/**
	 * checkPermitByGroupId 	Legacy_Category.CheckPermitByGroupId
	 * check permission of the given category by user group id.
	 *
	 * @param bool &$check
	 * @param int $catId
	 * @param string $action
	 * @param int $groupId	:user group id
	 * @param string $module
	 *
	 * @return	void
	 */ 
	abstract public function checkPermitByGroupId(/*** bool ***/ &$check, /*** int ***/ $catId, /*** string ***/ $action, /*** int ***/ $groupId, /*** string ***/ $module="");

	/**
	 * getParent		Legacy_Category.GetParent
	 * get the parent category object.
	 *
	 * @param Legacy_AbstractCategoryObject &$parent
	 * @param int $catId
	 *
	 * @return	void
	 */ 
	abstract public function getParent(/*** Legacy_AbstractCategoryObject ***/ &$parent, /*** int ***/ $catId);

	/**
	 * getChildren		Legacy_Category.GetChildren
	 * get the child category objects. Be careful that you can get only children objects, excluded the given category itself.
	 *
	 * @param Legacy_AbstractCategoryObject[] &$children
	 * @param int $catId
	 * @param string $action
	 * @param int $uid
	 * @param string $module
	 *
	 * @return	void
	 */ 
	abstract public function getChildren(/*** Legacy_AbstractCategoryObject[] ***/ &$children, /*** int ***/ $catId, /*** string ***/ $action, /*** int ***/ $uid, /*** string ***/ $module="");

	/**
	 * getCatPath		Legacy_Category.GetCatPath
	 * get category path array from top to the given category.
	 *
	 * @param array &$catPath	:$catPath includes cat_id, title
	 * @param int $catId
	 * @param string $order :order of categories. From top OR From the given category. 'ASC' or 'DESC'.
	 *
	 * @return	void
	 */ 
	abstract public function getCatPath(/*** array ***/ &$catPath, /*** int ***/ $catId, /*** string ***/ $order);

	/**
	 * getPermittedIdList		Legacy_Category.GetPermittedIdList
	 * get category ids of permission.
	 *
	 * @param int[] &$idArr
	 * @param int $setId
	 * @param string $action
	 * @param int $uid
	 * @param int $catId	
	 * @param string $module
	 *
	 * @return	void
	 */ 
	abstract public function getPermittedIdList(/*** int[] ***/ &$idArr, /*** int ***/ $setId, /*** string ***/ $action, /*** int ***/ $uid, /*** int ***/ $catId=0, /*** string ***/ $module="");

}

?>
