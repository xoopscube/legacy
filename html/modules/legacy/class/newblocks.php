<?php
/**
 *
 * @package Legacy
 * @version $Id: newblocks.php,v 1.3 2008/09/25 15:11:31 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class LegacyNewblocksObject extends XoopsSimpleObject
{
	var $mModule = null;
	
	/**
	 * Array of group objects who can access this object.
	 * It need lazy loading to access.
	 */
	var $mGroup = array();
	
	var $mBmodule = array();
	
	var $mColumn = null;
	
	var $mCachetime = null;

	function LegacyNewblocksObject()
	{
		$this->initVar('bid', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('mid', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('func_num', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('options', XOBJ_DTYPE_STRING, '', true, 255);
		$this->initVar('name', XOBJ_DTYPE_STRING, '', true, 150);
		$this->initVar('title', XOBJ_DTYPE_STRING, '', true, 255);
		$this->initVar('content', XOBJ_DTYPE_TEXT, '', true);
		$this->initVar('side', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('weight', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('visible', XOBJ_DTYPE_BOOL, '0', true);
		$this->initVar('block_type', XOBJ_DTYPE_STRING, '', true, 1);
		$this->initVar('c_type', XOBJ_DTYPE_STRING, '', true, 1);
		$this->initVar('isactive', XOBJ_DTYPE_BOOL, '0', true);
		$this->initVar('dirname', XOBJ_DTYPE_STRING, '', true, 50);
		$this->initVar('func_file', XOBJ_DTYPE_STRING, '', true, 50);
		$this->initVar('show_func', XOBJ_DTYPE_STRING, '', true, 50);
		$this->initVar('edit_func', XOBJ_DTYPE_STRING, '', true, 50);
		$this->initVar('template', XOBJ_DTYPE_STRING, '', true, 50);
		$this->initVar('bcachetime', XOBJ_DTYPE_INT, '0', true);
		$this->initVar('last_modified', XOBJ_DTYPE_INT, time(), true);
	}
	
	function loadModule()
	{
		$handler =& xoops_gethandler('module');
		$this->mModule =& $handler->get($this->get('mid'));
	}

	/**
	 * Load group objects who can access this object. And, set the objects to mGroup.
	 * 
	 * TODO Need lock double loading.
	 */	
	function loadGroup()
	{
		$handler =& xoops_gethandler('groupperm');
		$criteria =new CriteriaCompo();
		$criteria->add(new Criteria('gperm_modid', 1));
		$criteria->add(new Criteria('gperm_itemid', $this->get('bid')));
		$criteria->add(new Criteria('gperm_name', 'block_read'));
		
		$gpermArr =&  $handler->getObjects($criteria);
		
		$handler =& xoops_gethandler('group');
		foreach ($gpermArr as $gperm) {
			$this->mGroup[] =& $handler->get($gperm->get('gperm_groupid'));
		}
	}
	
	function loadBmodule()
	{
		$handler =& xoops_getmodulehandler('block_module_link', 'legacy');
		$criteria =new Criteria('block_id', $this->get('bid'));
		
		$this->mBmodule =& $handler->getObjects($criteria);
	}
	
	function loadColumn()
	{
		$handler =& xoops_getmodulehandler('columnside', 'legacy');
		$this->mColumn =& $handler->get($this->get('side'));
	}
	
	function loadCachetime()
	{
		$handler =& xoops_gethandler('cachetime');
		$this->mCachetime =& $handler->get($this->get('bcachetime'));
	}
}

class LegacyNewblocksHandler extends XoopsObjectGenericHandler
{
	var $mTable = "newblocks";
	var $mPrimary = "bid";
	var $mClass = "LegacyNewblocksObject";
	
	function delete(&$obj, $force = false)
	{
		if (parent::delete($obj, $force)) {
			//
			// Delete related data from block_module_link.
			//
			$handler =& xoops_getmodulehandler('block_module_link', 'legacy');
			$handler->deleteAll(new Criteria('block_id'), $obj->get('bid'));
			
			//
			// Delete related permissions from groupperm.
			//
			$handler =& xoops_gethandler('groupperm');

			$criteria =new CriteriaCompo();
			$criteria->add(new Criteria('gperm_modid', 1));
			$criteria->add(new Criteria('gperm_itemid', $obj->get('bid')));
			$criteria->add(new Criteria('gperm_name', 'block_read'));
			
			$handler->deleteAll($criteria);
			
			return true;
		}
		else {
			return false;
		}
	}
}

?>
