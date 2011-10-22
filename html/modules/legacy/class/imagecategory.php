<?php
/**
 *
 * @package Legacy
 * @version $Id: imagecategory.php,v 1.4 2008/09/25 15:11:28 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class LegacyImagecategoryObject extends XoopsSimpleObject
{
	var $mImage = array();
	var $_mImageLoadedFlag = false;

	/**
	 * Array of group objects which are allowed to read files of this category.
	 */	
	var $mReadGroups = array();
	var $_mReadGroupsLoadedFlag = false;

	/**
	 * Array of group objects which are allowed to upload a file to this category.
	 */	
	var $mUploadGroups = array();
	var $_mUploadGroupsLoadedFlag = false;
	

	function LegacyImagecategoryObject()
	{
		$this->initVar('imgcat_id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('imgcat_name', XOBJ_DTYPE_STRING, '', true, 100);
		$this->initVar('imgcat_maxsize', XOBJ_DTYPE_INT, '50000', true);
		$this->initVar('imgcat_maxwidth', XOBJ_DTYPE_INT, '120', true);
		$this->initVar('imgcat_maxheight', XOBJ_DTYPE_INT, '120', true);
		$this->initVar('imgcat_display', XOBJ_DTYPE_BOOL, '1', true);
		$this->initVar('imgcat_weight', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('imgcat_type', XOBJ_DTYPE_STRING, 'C', true, 1);
		$this->initVar('imgcat_storetype', XOBJ_DTYPE_STRING, 'file', true, 5);
	}

	function loadImage()
	{
		if ($this->_mImageLoadedFlag == false) {
			$handler =& xoops_getmodulehandler('image', 'legacy');
			$this->mImage =& $handler->getObjects(new Criteria('imagecat_id', $this->get('imagecat_id')));
			$this->_mImageLoadedFlag = true;
		}
	}

	function &createImage()
	{
		$handler =& xoops_getmodulehandler('image', 'legacy');
		$obj =& $handler->create();
		$obj->set('imagecat_id', $this->get('imagecat_id'));
		return $obj;
	}
	
	function getImageCount()
	{
		$handler =& xoops_getmodulehandler('image', 'legacy');
		return $handler->getCount(new Criteria('imgcat_id', $this->get('imgcat_id')));
	}
	
	function loadReadGroups()
	{
		if ($this->_mReadGroupsLoadedFlag) {
			return;
		}
		
		$handler =& xoops_gethandler('groupperm');
		$gidArr = $handler->getGroupIds('imgcat_read', $this->get('imgcat_id'));
		
		$handler =& xoops_gethandler('group');
		foreach ($gidArr as $gid) {
			$object =& $handler->get($gid);
			
			if (is_object($object)) {
				$this->mReadGroups[] =& $object;
			}
			
			unset($object);
		}
		
		$this->_mReadGroupsLoadedFlag = true;
	}
	
	function isLoadedReadGroups()
	{
		return $this->_mReadGroupsLoadedFlag;
	}

	/**
	 * If $groups has the permission of reading this object, return true.
	 */	
	function hasReadPerm($groups)
	{
		$this->loadReadGroups();
		foreach (array_keys($this->mReadGroups) as $key) {
			foreach ($groups as $group) {
				if ($this->mReadGroups[$key]->get('groupid') == $group) {
					return true;
				}
			}
		}
		
		return false;
	}

	function loadUploadGroups()
	{
		if ($this->_mUploadGroupsLoadedFlag) {
			return;
		}
		
		$handler =& xoops_gethandler('groupperm');
		$gidArr = $handler->getGroupIds('imgcat_write', $this->get('imgcat_id'));
		
		$handler =& xoops_gethandler('group');
		foreach ($gidArr as $gid) {
			$object =& $handler->get($gid);
			
			if (is_object($object)) {
				$this->mUploadGroups[] =& $object;
			}
			
			unset($object);
		}
		
		$this->_mUploadGroupsLoadedFlag = true;
	}
	
	function isLoadedUploadGroups()
	{
		return $this->_mUploadGroupsLoadedFlag;
	}

	function hasUploadPerm($groups)
	{
		$this->loadUploadGroups();
		foreach (array_keys($this->mUploadGroups) as $key) {
			foreach ($groups as $group) {
				if ($this->mUploadGroups[$key]->get('groupid') == $group) {
					return true;
				}
			}
		}
		
		return false;
	}
}

class LegacyImagecategoryHandler extends XoopsObjectGenericHandler
{
	var $mTable = "imagecategory";
	var $mPrimary = "imgcat_id";
	var $mClass = "LegacyImagecategoryObject";

	function insert(&$obj, $force = false)
	{
		$returnFlag = parent::insert($obj, $force);
		
		$handler =& xoops_getmodulehandler('group_permission', 'legacy');
		
		//
		// If the object has groups which are allowed to read.
		//
		if ($obj->isLoadedReadGroups()) {
			$criteria =new CriteriaCompo();
			$criteria->add(new Criteria('gperm_itemid', $obj->get('imgcat_id')));
			$criteria->add(new Criteria('gperm_modid', 1));
			$criteria->add(new Criteria('gperm_name', 'imgcat_read'));
			$handler->deleteAll($criteria);
			
			foreach ($obj->mReadGroups as $group) {
				$perm =& $handler->create();
				$perm->set('gperm_groupid', $group->get('groupid'));
				$perm->set('gperm_itemid', $obj->get('imgcat_id'));
				$perm->set('gperm_modid', 1);
				$perm->set('gperm_name', 'imgcat_read');
				
				$returnFlag &= $handler->insert($perm, $force);
			}
		}

		//
		// If the object has groups which are allowed to upload.
		//
		if ($obj->isLoadedUploadGroups()) {
			$criteria =new CriteriaCompo();
			$criteria->add(new Criteria('gperm_itemid', $obj->get('imgcat_id')));
			$criteria->add(new Criteria('gperm_modid', 1));
			$criteria->add(new Criteria('gperm_name', 'imgcat_write'));
			$handler->deleteAll($criteria);
			
			foreach ($obj->mUploadGroups as $group) {
				$perm =& $handler->create();
				$perm->set('gperm_groupid', $group->get('groupid'));
				$perm->set('gperm_itemid', $obj->get('imgcat_id'));
				$perm->set('gperm_modid', 1);
				$perm->set('gperm_name', 'imgcat_write');
				
				$returnFlag &= $handler->insert($perm, $force);
			}
		}
		
		return $returnFlag;
	}
	
	function &getObjectsWithReadPerm($groups = array(), $display = null)
	{
		$criteria = new CriteriaCompo();
		if ($display != null) {
			$criteria->add(new Criteria('imgcat_display', $display));
		}
		$criteria->setSort('imgcat_weight');
		$objs =& $this->getObjects($criteria);
		unset($criteria);

		$ret = array();
		foreach (array_keys($objs) as $key) {
			if ($objs[$key]->hasReadPerm($groups)) {
				$ret[] =& $objs[$key];
			}
		}
		
		return $ret;
	}

	function delete(&$obj, $force = false)
	{
		$handler =& xoops_getmodulehandler('image', 'legacy');
		$handler->deleteAll(new Criteria('imgcat_id', $obj->get('imgcat_id')));
		unset($handler);
	
		$handler =& xoops_getmodulehandler('group_permission', 'legacy');
		$criteria =new CriteriaCompo();
		$criteria->add(new Criteria('gperm_itemid', $obj->get('imgcat_id')));
		$criteria->add(new Criteria('gperm_modid', 1));
		
		$nameCriteria =new CriteriaCompo();
		$nameCriteria->add(new Criteria('gperm_name', 'imgcat_read'));
		$nameCriteria->add(new Criteria('gperm_name', 'imgcat_write'), 'OR');
		
		$criteria->add($nameCriteria);
		
		$handler->deleteAll($criteria);

		return parent::delete($obj, $force);
	}
}

?>
