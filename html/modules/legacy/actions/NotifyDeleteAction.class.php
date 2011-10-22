<?php
/**
 *
 * @package Legacy
 * @version $Id: NotifyDeleteAction.class.php,v 1.4 2008/09/25 15:12:11 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/include/notification_functions.php";

require_once XOOPS_MODULE_PATH . "/legacy/forms/NotifyDeleteForm.class.php";

/***
 * @internal
 * List up notifications. This action is like notifications.php (when $op is
 * 'list').
 */
class Legacy_NotifyDeleteAction extends Legacy_Action
{
	var $mModules = array();
	var $mActionForm = null;
	
	var $mErrorMessage = null;
	
	function prepare(&$controller, &$xoopsUser)
	{
		$controller->mRoot->mLanguageManager->loadPageTypeMessageCatalog('notification');
		$controller->mRoot->mLanguageManager->loadModuleMessageCatalog('legacy');
		
		$this->mActionForm =new Legacy_NotifyDeleteForm();
		$this->mActionForm->prepare();
	}

	function hasPermission(&$controller, &$xoopsUser)
	{
		return is_object($xoopsUser);
	}

	/**
	 * This member function is a special case. Because the confirm is must, it
	 * uses token error for displaying confirm.
	 */	
	function execute(&$contoller, &$xoopsUser)
	{
		$this->mActionForm->fetch();
		$this->mActionForm->validate();
		
		//
		// If input values are error, the action form returns fatal error flag.
		// If it's not fatal, display confirm form.
		//
		if ($this->mActionForm->hasError()) {
			return $this->mActionForm->mFatalError ? LEGACY_FRAME_VIEW_ERROR : LEGACY_FRAME_VIEW_INPUT;
		}

		//
		// Execute deleting.
		//
		$successFlag = true;
		$handler =& xoops_gethandler('notification');
		foreach ($this->mActionForm->mNotifiyIds as $t_idArr) {
			$t_notify =& $handler->get($t_idArr['id']);
			if (is_object($t_notify) && $t_notify->get('not_uid') == $xoopsUser->get('uid') && $t_notify->get('not_modid') == $t_idArr['modid']) {
				$successFlag = $successFlag & $handler->delete($t_notify);
			}
		}
		
		return $successFlag ? LEGACY_FRAME_VIEW_SUCCESS : LEGACY_FRAME_VIEW_ERROR;
	}
		
	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("legacy_notification_delete.html");
		$render->setAttribute('actionForm', $this->mActionForm);
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward(XOOPS_URL . "/notifications.php");
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect(XOOPS_URL . "/notifications.php", 2, _NOT_NOTHINGTODELETE);
	}
}

?>
