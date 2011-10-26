<?php
/**
 *
 * @package Legacy
 * @version $Id: BlockEditForm.class.php,v 1.5 2008/10/26 04:21:37 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

class Legacy_BlockEditForm extends XCube_ActionForm
{
	function getTokenName()
	{
		return "module.legacy.BlockEditForm.TOKEN" . $this->get('bid');
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['bid'] =new XCube_IntProperty('bid');
		$this->mFormProperties['options'] =new XCube_StringArrayProperty('options');
		$this->mFormProperties['title'] =new XCube_StringProperty('title');
		$this->mFormProperties['side'] =new XCube_IntProperty('side');
		$this->mFormProperties['weight'] =new XCube_IntProperty('weight');
		$this->mFormProperties['bcachetime'] =new XCube_IntProperty('bcachetime');
		$this->mFormProperties['bmodule'] =new XCube_IntArrayProperty('bmodule');
		$this->mFormProperties['groupid'] =new XCube_IntArrayProperty('groupid');
	
		//
		// Set field properties
		//
		$this->mFieldProperties['bid'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['bid']->setDependsByArray(array('required'));
		$this->mFieldProperties['bid']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_BID);
	
		$this->mFieldProperties['title'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['title']->setDependsByArray(array('required','maxlength'));
		$this->mFieldProperties['title']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_TITLE, '255');
		$this->mFieldProperties['title']->addMessage('maxlength', _MD_LEGACY_ERROR_MAXLENGTH, _AD_LEGACY_LANG_TITLE, '255');
		$this->mFieldProperties['title']->addVar('maxlength', '255');
	
		$this->mFieldProperties['side'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['side']->setDependsByArray(array('required', 'objectExist'));
		$this->mFieldProperties['side']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_SIDE);
		$this->mFieldProperties['side']->addMessage('objectExist', _AD_LEGACY_ERROR_OBJECTEXIST, _AD_LEGACY_LANG_SIDE);
		$this->mFieldProperties['side']->addVar('handler', 'columnside');
		$this->mFieldProperties['side']->addVar('module', 'legacy');
	
		$this->mFieldProperties['weight'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['weight']->setDependsByArray(array('required', 'intRange'));
		$this->mFieldProperties['weight']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_WEIGHT);
		$this->mFieldProperties['weight']->addMessage('intRange', _AD_LEGACY_ERROR_INTRANGE, _AD_LEGACY_LANG_WEIGHT);
		$this->mFieldProperties['weight']->addVar('min', '0');
		$this->mFieldProperties['weight']->addVar('max', '65535');
	
		$this->mFieldProperties['bcachetime'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['bcachetime']->setDependsByArray(array('required', 'objectExist'));
		$this->mFieldProperties['bcachetime']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_BCACHETIME);
		$this->mFieldProperties['bcachetime']->addMessage('objectExist', _AD_LEGACY_ERROR_OBJECTEXIST, _AD_LEGACY_LANG_BCACHETIME);
		$this->mFieldProperties['bcachetime']->addVar('handler', 'cachetime');
		
		$this->mFieldProperties['groupid'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['groupid']->setDependsByArray(array('objectExist'));
		$this->mFieldProperties['groupid']->addMessage('objectExist', _AD_LEGACY_ERROR_OBJECTEXIST, _AD_LEGACY_LANG_GROUPID);
		$this->mFieldProperties['groupid']->addVar('handler', 'group');
	}

	function validateBmodule()
	{
		$bmodule = $this->get('bmodule');
		if (!(count($bmodule))) {
			$this->addErrorMessage(_AD_LEGACY_ERROR_BMODULE);
		}
		else {
			$handler =& xoops_gethandler('module');
			foreach ($this->get('bmodule') as $mid) {
				$module =& $handler->get($mid);
				if ($mid != -1 && $mid != 0 && !is_object($module)) {
					$this->addErrorMessage(XCube_Utils::formatMessage(_AD_LEGACY_ERROR_OBJECTEXIST, _AD_LEGACY_LANG_BMODULE));
				}
			}
		}
	}
	
	function validateGroupid()
	{
		$groupid = $this->get('groupid');
		if (!(count($groupid))) {
			$this->addErrorMessage(_AD_LEGACY_ERROR_GROUPID);
		}
	}
	
	function load(&$obj)
	{
		$this->set('bid', $obj->get('bid'));
		$this->set('title', $obj->get('title'));
		$this->set('side', $obj->get('side'));
		$this->set('weight', $obj->get('weight'));
		$this->set('bcachetime', $obj->get('bcachetime'));
		
		$i = 0;
		foreach ($obj->mBmodule as $module) {
			if (is_object($module)) {
				$this->set('bmodule', $i++, $module->get('module_id'));
			}
		}

		$i = 0;
		foreach ($obj->mGroup as $group) {
			if (is_object($group)) {
				$this->set('groupid', $i++, $group->get('groupid'));
			}
		}
	}

	function update(&$obj)
	{
		$obj->set('bid', $this->get('bid'));
		$obj->set('title', $this->get('title'));
		$obj->set('side', $this->get('side'));
		$obj->set('weight', $this->get('weight'));
		$obj->set('bcachetime', $this->get('bcachetime'));

		$obj->set('last_modified', time());
		
		//
		// Update options (XOOPS2 compatible)
		//
		$optionArr = $this->get('options');
		for ($i = 0; $i < count($optionArr); $i++) {
			if (is_array($optionArr[$i])) {
				$optionArr[$i] = implode(',', $optionArr[$i]);
			}
		}
		
		$obj->set('options', implode('|', $optionArr));
		
		$obj->mBmodule = array();
		$handler =& xoops_getmodulehandler('block_module_link', 'legacy');
		foreach ($this->get('bmodule') as $mid) {
			$t_obj =& $handler->create();
			$t_obj->set('block_id', $this->get('bid'));
			$t_obj->set('module_id', $mid);
			$obj->mBmodule[] =& $t_obj;
			unset($t_obj);
		}

		$obj->mGroup = array();
		$handler =& xoops_gethandler('group');
		foreach ($this->get('groupid') as $groupid) {
			$obj->mGroup[] =& $handler->get($groupid);
		}
	}
}

?>
