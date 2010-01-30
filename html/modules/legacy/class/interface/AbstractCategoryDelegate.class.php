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
	 * getCategoryGroupList
	 *
	 * @param string[] &$grList
	 *
     * @return  void
	 */	
	abstract public function getCategoryGroupList(/*** string[] ***/ &$grList);

	/**
	 * getTitle
	 * get the category title by category id.
	 *
	 * @param string &$title
	 * @param int $catId
	 *
     * @return  void
	 */	
	abstract public function getTitle(/*** string ***/ &$title, /*** int ***/ $catId);

	/**
	 * getTree
	 * get category objects in the form of tree.
	 *
	 * @param XoopsSimpleObject[] $tree
	 * @param int $grId	:category group id
	 * @param string $action	:ex.view, edit
	 * @param int $uid
	 * @param int $catId
	 * @param string $module
	 *
     * @return  void
	 */	
	abstract public function getTree(/*** XoopsSimpleObject[] ***/ &$tree, /*** int ***/ $grId, /*** string ***/ $action, /*** int ***/ $uid, /*** int ***/ $catId=0, $modules="");

	/**
	 * getTitleList
	 * get category titles.
	 *
	 * @param string[] &$titleList
	 * @param int $grId	:category group id
	 *
     * @return  void
	 */	
	abstract public function getTitleList(/*** string[] ***/ &$titleList, /*** int ***/ $grId);

	/**
	 * checkPermitByUserId
	 * check permission of the given category by user id.
	 *
	 * @param bool &$check
	 * @param int $catId
	 * @param string $action
	 * @param int $uid
	 * @param string $module
	 *
     * @return  void
	 */	
	abstract public function checkPermitByUserId(/*** bool ***/ &$check, /*** int ***/ $catId, /*** string ***/ $action, /*** int ***/ $uid, /*** string ***/ $module="");

	/**
	 * checkPermitByGroupId
	 * check permission of the given category by user group id.
	 *
	 * @param bool &$check
	 * @param int $catId
	 * @param string $action
	 * @param int $groupId	:user group id
	 * @param string $module
	 *
     * @return  void
	 */	
	abstract public function checkPermitByGroupId(/*** bool ***/ &$check, /*** int ***/ $catId, /*** string ***/ $action, /*** int ***/ $groupId, /*** string ***/ $module="");

	/**
	 * getParent
	 * get the parent category object.
	 *
	 * @param XoopsSimpleObject &$parent
	 * @param int $catId
	 *
     * @return  void
	 */	
	abstract public function getParent(/*** Lecat_CatObject ***/ &$parent, /*** int ***/ $catId);

	/**
	 * getChildren
	 * get the child category objects. Be careful that you can get only children objects, excluded the given category itself.
	 *
	 * @param Lecat_CatObject[] &$children
	 * @param int $catId
	 * @param string $action
	 * @param int $uid
	 * @param string $module
	 *
     * @return  void
	 */	
	abstract public function getChildren(/*** Lecat_CatObject[] ***/ &$children, /*** int ***/ $catId, /*** string ***/ $action, /*** int ***/ $uid, /*** string ***/ $module="");

	/**
	 * getCatPath
	 * get category path array from top to the given category.
	 *
	 * @param array &$catPath	:$catPath includes cat_id, title
	 * @param int $catId
	 * @param string $order	:order of categories. From top OR From the given category
	 *
     * @return  void
	 */	
	abstract public function getCatPath(/*** array ***/ &$catPath, /*** int ***/ $catId, /*** string ***/ $order);

	/**
	 * getPermittedIdList
	 * get category ids of permission.
	 *
	 * @param int[] &$idArr
	 * @param int $grId
	 * @param string $action
	 * @param int $uid
	 * @param int $catId	
	 * @param string $module
	 *
     * @return  void
	 */	
	abstract public function getPermittedIdList(/*** int[] ***/ &$idArr, /*** int ***/ $grId, /*** string ***/ $action, /*** int ***/ $uid, /*** int ***/ $catId=0, /*** string ***/ $module="");

}

?>
