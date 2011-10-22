<?php
/**
 * @package pm
 * @version $Id: DeleteAction.class.php,v 1.1 2007/05/15 02:35:27 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/pm/forms/PmDeleteForm.class.php";

class Pm_DeleteAction extends Pm_AbstractAction
{
	var $mActionForm = null;
	
	function prepare(&$controller, &$xoopsUser, &$moduleConfig)
	{
		$this->mActionForm =& new Pm_PmDeleteForm();
		$this->mActionForm->prepare();
	}
	
	function execute(&$controller, &$xoopsUser)
	{
		//
		// Fetch request and validate.
		//
		$this->mActionForm->fetch();
		$this->mActionForm->validate();

		//
		// If error, go to re-input.
		//
		if($this->mActionForm->hasError()) {
			return PM_FRAME_VIEW_ERROR;
		}

		//
		// Delete PM
		//
		$handler =& xoops_gethandler('privmessage');
		foreach ($this->mActionForm->getVar('msg_id') as $msg_id) {
			$pm =& $handler->get($msg_id);
			if (is_object($pm) && ($pm->get('to_userid') == $xoopsUser->get('uid'))) {
				$handler->delete($pm);
			}
			unset($pm);
		}

		return PM_FRAME_VIEW_SUCCESS;
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect(XOOPS_URL . "/viewpmsg.php", 1, _MD_PM_MESSAGE_DELETED);
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect(XOOPS_URL . "/viewpmsg.php", 1, _MD_PM_ERROR_ACCESS);
	}
}

?>
