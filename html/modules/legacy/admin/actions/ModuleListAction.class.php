<?php
/**
 *
 * @package Legacy
 * @version $Id: ModuleListAction.class.php,v 1.3 2008/09/25 15:11:49 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_LEGACY_PATH . "/admin/forms/ModuleListFilterForm.class.php";
require_once XOOPS_LEGACY_PATH . "/admin/forms/ModuleListForm.class.php";

class Legacy_ModuleListAction extends Legacy_Action
{
	var $mModuleObjects = array();
	var $mFilter = null;

	var $mActionForm = null;

	function prepare(&$controller, &$xoopsUser)
	{
		$this->mActionForm =new Legacy_ModuleListForm();
		$this->mActionForm->prepare();
	}
	

	function getDefaultView(&$controller, &$xoopsUser)
	{
		$this->mFilter =new Legacy_ModuleListFilterForm();
		$this->mFilter->fetch();

		$moduleHandler =& xoops_gethandler('module');
		$this->mModuleObjects =& $moduleHandler->getObjects($this->mFilter->getCriteria());

		return LEGACY_FRAME_VIEW_INDEX;
	}
	
	function execute(&$controller, &$xoopsUser)
	{
		$form_cancel = $controller->mRoot->mContext->mRequest->getRequest('_form_control_cancel');
		if ($form_cancel != null) {
			return LEGACY_FRAME_VIEW_CANCEL;
		}

		$this->mActionForm->fetch();
		$this->mActionForm->validate();

		if ($this->mActionForm->hasError()) {
			return $this->_processConfirm($controller, $xoopsUser);
		}
		else {
			return $this->_processSave($controller, $xoopsUser);
		}
	}
	
	function _processConfirm(&$controller,&$xoopsUser)
	{
		$moduleHandler =& xoops_gethandler('module');
		$t_objectArr =& $moduleHandler->getObjects();

		//
		// Do mapping.
		//
		foreach ($t_objectArr as $module) {
			$this->mModuleObjects[$module->get('mid')] =& $module;
			unset($module);
		}

		return LEGACY_FRAME_VIEW_INPUT;
	}

    function _processSave(&$controller, &$xoopsUser)
    {
        $moduleHandler =& xoops_gethandler('module');
    	$blockHandler =& xoops_gethandler('block');
        $t_objectArr =& $moduleHandler->getObjects();

        $successFlag = true;
        foreach($t_objectArr as $module) {
            $mid = $module->get('mid');
            $olddata['name'] = $module->get('name');
            $olddata['weight'] = $module->get('weight');
            $olddata['isactive'] = $module->get('isactive');
            $newdata['name'] = $this->mActionForm->get('name', $mid);
            $newdata['weight'] = $this->mActionForm->get('weight', $mid);
            $newdata['isactive'] = $this->mActionForm->get('isactive', $mid);
            if (count(array_diff_assoc($olddata, $newdata)) > 0 ) {
                $module->set('name', $this->mActionForm->get('name', $mid));
                $module->set('weight', $this->mActionForm->get('weight', $mid));
                $module->set('isactive', $this->mActionForm->get('isactive', $mid));

                //
                // Store & Sync isactive of blocks with isactive of the module.
                //
                if ($moduleHandler->insert($module)) {
                	$successFlag &= true;
                	$blockHandler->syncIsActive($module->get('mid'), $module->get('isactive'));
                }
                else {
                	$successFlag = false;
                }
            }
        }

        return $successFlag ? LEGACY_FRAME_VIEW_SUCCESS : LEGACY_FRAME_VIEW_ERROR;
    }

	/**
	 * To support a template writer, this send the list of mid that actionForm kept.
	 */
	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("module_list_confirm.html");
		$render->setAttribute('moduleObjects', $this->mModuleObjects);
		$render->setAttribute('actionForm', $this->mActionForm);
		
		//
		// To support a template writer, this send the list of mid that
		// actionForm kept.
		//
		$t_arr = $this->mActionForm->get('name');
		$render->setAttribute('mids', array_keys($t_arr));
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("module_list.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		
		//
		// Load adminmenu before we assign these to template.
		//
		foreach(array_keys($this->mModuleObjects) as $key) {
			$this->mModuleObjects[$key]->loadAdminMenu();
			$this->mModuleObjects[$key]->loadInfo($this->mModuleObjects[$key]->get('dirname'));
		}
		
		$render->setAttribute('moduleObjects', $this->mModuleObjects);

		$moduleHandler =& xoops_gethandler('module');
		$module_total = $moduleHandler->getCount();
		$active_module_total = $moduleHandler->getCount(new Criteria('isactive', 1));
		$render->setAttribute('ModuleTotal', $module_total);
		$render->setAttribute('activeModuleTotal', $active_module_total );
		$render->setAttribute('inactiveModuleTotal', $module_total - $active_module_total);
	}

	function executeViewSuccess(&$controller,&$xoopsUser,&$renderer)
	{
		$controller->executeForward('./index.php?action=ModuleList');
	}

	function executeViewError(&$controller, &$xoopsUser, &$renderer)
	{
		$controller->executeRedirect('./index.php?action=ModuleList', 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
	}

	function executeViewCancel(&$controller,&$xoopsUser,&$renderer)
	{
		$controller->executeForward('./index.php?action=ModuleList');
	}
}

?>
