<?php
/**
 *
 * @package Legacy
 * @version $Id: BlockEditAction.class.php,v 1.4 2008/09/25 15:11:37 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractEditAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/BlockEditForm.class.php";

class Legacy_BlockEditAction extends Legacy_AbstractEditAction
{
	/**
	 * @var string
	 */
	var $_mOptionForm = null;
	
	function _getId()
	{
		return isset($_REQUEST['bid']) ? xoops_getrequest('bid') : 0;
	}
	
	function prepare(&$controller, &$xoopsUser)
	{
		parent::prepare($controller, $xoopsUser);
		if (is_object($this->mObject)) {
			$handler =& xoops_gethandler('module');
			$module =& $handler->get($this->mObject->get('mid'));
			if (is_object($module)) {
				$controller->mRoot->mLanguageManager->loadModinfoMessageCatalog($module->get('dirname'));
				$controller->mRoot->mLanguageManager->loadModuleAdminMessageCatalog($module->get('dirname'));
			}
		}
	}
	
	function isEnableCreate()
	{
		return false;
	}
	
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('newblocks');
		return $handler;
	}

	function _setupActionForm()
	{
		$this->mActionForm =new Legacy_BlockEditForm();
		$this->mActionForm->prepare();
	}
	
	/**
	 * Return true if the target object can be edited. This private method exists 
	 * to control subclass actions.
	 * 
	 * @return bool
	 */
	function _isEditable()
	{
		if (is_object($this->mObject)) {
			return ($this->mObject->get('visible') == 1);
		}
		else {
			return false;
		}
	}
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		if (!$this->_isEditable()) {
			return LEGACY_FRAME_VIEW_ERROR;
		}
		
		$this->mObject->loadGroup();
		$this->mObject->loadBmodule();
		
		return parent::getDefaultView($controller, $xoopsUser);
	}

	function execute(&$controller, &$xoopsUser)
	{
		if (!$this->_isEditable()) {
			return LEGACY_FRAME_VIEW_ERROR;
		}

		$ret = parent::execute($controller, $xoopsUser);
		
		if ($ret == LEGACY_FRAME_VIEW_SUCCESS) {
			//
			// Reset block_module_link.
			//
			$handler =& xoops_getmodulehandler('block_module_link', 'legacy');
			$handler->deleteAll(new Criteria('block_id', $this->mObject->get('bid')));
			foreach ($this->mObject->mBmodule as $bmodule) {
				//
				// If object is new, $bmodule isn't set bid yet.
				//
				$bmodule->set('block_id', $this->mObject->get('bid'));
				$handler->insert($bmodule);
			}

			//
			// Insert group permissions.
			//
			$currentGroupid = array();
			foreach ($this->mObject->mGroup as $group) {
				$currentGroupid[] = $group->get('groupid');
			}
			
			$permHandler =& xoops_gethandler('groupperm');
			$criteria =new CriteriaCompo();
			$criteria->add(new Criteria('gperm_modid', 1));
			$criteria->add(new Criteria('gperm_itemid', $this->mObject->get('bid')));
			$criteria->add(new Criteria('gperm_name', 'block_read'));
			
			$gpermArr =&  $permHandler->getObjects($criteria);
			foreach ($gpermArr as $gperm) {
				if (!in_array($gperm->get('gperm_groupid'), $currentGroupid)) {
					$permHandler->delete($gperm);
				}
			}
			
			foreach ($this->mObject->mGroup as $group) {
				$insertFlag = true;
				foreach ($gpermArr as $gperm) {
					if ($gperm->get('gperm_groupid') == $group->get('groupid')) {
						$insertFlag = false;
					}
				}
				
				if ($insertFlag) {
					$gperm =& $permHandler->create();
					$gperm->set('gperm_modid', 1);
					$gperm->set('gperm_groupid', $group->get('groupid'));
					$gperm->set('gperm_itemid', $this->mObject->get('bid'));
					$gperm->set('gperm_name', 'block_read');
					
					$permHandler->insert($gperm);
				}
			}
		}
		
		return $ret;
	}
	
	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("block_edit.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		
		//
		// lazy loading
		//
		$this->mObject->loadModule();
		
		$render->setAttribute('object', $this->mObject);
		
		//
		// Build active modules list and set.
		//
		$handler =& xoops_gethandler('module');
		$moduleArr[0] =& $handler->create();
		$moduleArr[0]->set('mid', -1);
		$moduleArr[0]->set('name', _AD_LEGACY_LANG_TOPPAGE);
		
		$moduleArr[1] =& $handler->create();
		$moduleArr[1]->set('mid', 0);
		$moduleArr[1]->set('name', _AD_LEGACY_LANG_ALL_MODULES);

		$criteria =new CriteriaCompo();
		$criteria->add(new Criteria('hasmain', 1));
		$criteria->add(new Criteria('isactive', 1));
		
		$t_Arr =& $handler->getObjects($criteria);
		$moduleArr = array_merge($moduleArr, $t_Arr);
		$render->setAttribute('moduleArr', $moduleArr);
		
		$handler =& xoops_getmodulehandler('columnside');
		$columnSideArr =& $handler->getObjects();
		$render->setAttribute('columnSideArr', $columnSideArr);

		$handler =& xoops_gethandler('group');
		$groupArr =& $handler->getObjects();
		$render->setAttribute('groupArr', $groupArr);
		
		//
		// Build cachetime list and set.
		//
		$handler =& xoops_gethandler('cachetime');
		$cachetimeArr =& $handler->getObjects();
		$render->setAttribute('cachetimeArr', $cachetimeArr);

		//
		// Get html of option form rendered.
		//
		$this->_mOptionForm = $this->_getOptionForm();
		$render->setAttribute('hasVisibleOptionForm', $this->_hasVisibleOptionForm());
		$render->setAttribute('optionForm', $this->_mOptionForm);
	}

	/**
	 * @private
	 * Gets a value indicating whether the option form needs the row in the table to display its form.
	 * @remark This method is requred for the compatibility with XOOPS2.
	 * @return bool
	 */	
	function _hasVisibleOptionForm()
	{
		$block =& Legacy_Utils::createBlockProcedure($this->mObject);
		return $block->_hasVisibleOptionForm();
	}
	
	/**
	 * Gets rendered HTML buffer of the block optional edit form.
	 */
	function _getOptionForm()
	{
		$block =& Legacy_Utils::createBlockProcedure($this->mObject);
		return $block->getOptionForm();
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=BlockList");
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect("./index.php?action=BlockList", 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
	}
	
	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=BlockList");
	}
}

?>
