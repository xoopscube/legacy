<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/user/admin/forms/Mailjob_linkFilterForm.class.php";
require_once XOOPS_MODULE_PATH . "/user/admin/forms/Mailjob_linkAdminDeletesForm.class.php";

class User_Mailjob_linkListAction extends User_AbstractListAction
{
	var $mMailjob = null;
	var $mActionForm = null;
	
	function prepare(&$controller, &$xoopsUser, &$moduleConfig)
	{
		$this->mActionForm =new User_Mailjob_linkAdminDeletesForm();
		$this->mActionForm->prepare();

		$this->mActionForm->fetch();
	}
	
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('mailjob_link');
		return $handler;
	}

	function &_getFilterForm()
	{
		$filter =new User_Mailjob_linkFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	function _getBaseUrl()
	{
		return "./index.php?action=Mailjob_linkList";
	}
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		$handler =& xoops_getmodulehandler('mailjob', 'user');
		$this->mMailjob =& $handler->get($this->mActionForm->get('mailjob_id'));
		
		if ($this->mMailjob == null) {
			return USER_FRAME_VIEW_ERROR;
		}
		
		return parent::getDefaultView($controller, $xoopsUser);
	}
	
	function execute(&$controller, &$xoopsUser)
	{
		$this->mActionForm->validate();
		if ($this->mActionForm->hasError()) {
			return $this->getDefaultView($controller, $xoopsUser);
		}
		
		$mailjob_id = $this->mActionForm->get('mailjob_id');
		$uidArr = $this->mActionForm->get('uid');
		
		$handler =& xoops_getmodulehandler('mailjob_link', 'user');
		foreach (array_keys($uidArr) as $uid) {
			$mailjob_link =& $handler->get($mailjob_id, $uid);
			if ($mailjob_link != null) {
				$handler->delete($mailjob_link);
			}
		}
		
		return $this->getDefaultView($controller, $xoopsUser);
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("mailjob_link_list.html");
		#cubson::lazy_load_array('mailjob_link', $this->mObjects);
		$render->setAttribute("mailJob", $this->mMailjob);
		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
		$render->setAttribute('actionForm', $this->mActionForm);
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward('./index.php?action=MailjobList');
	}
}

?>
