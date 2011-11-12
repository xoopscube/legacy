<?php
/**
 * @package pm
 * @version $Id: DeleteOneAction.class.php,v 1.1 2007/05/15 02:35:27 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/pm/class/AbstractDeleteAction.class.php";
require_once XOOPS_MODULE_PATH . "/pm/forms/DeleteOneForm.class.php";

class Pm_DeleteOneAction extends Pm_AbstractDeleteAction
{
	function _getId()
	{
		return xoops_getrequest('msg_id');
	}
	
	function &_getHandler()
	{
		$handler =& xoops_gethandler('privmessage');
		return $handler;
	}

	function _setupActionForm()
	{
		$this->mActionForm =& new Pm_DeleteOneForm();
		$this->mActionForm->prepare();
	}
	
	function hasPermission(&$controller, &$xoopsUser, $moduleConfig)
	{
		if (parent::hasPermission($controller, $xoopsUser, $moduleConfig)) {
			return $xoopsUser->get('uid') == $this->mObject->get('to_userid');
		}
		else {
			return false;
		}
	}
	
	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("pm_delete_one.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('object', $this->mObject);
		
		$sendUser =& $this->mObject->getFromUser();
		if (is_object($sendUser) && $sendUser->isActive()) {
			$render->setAttribute("sendUser", $sendUser);
		}
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward(XOOPS_URL . "/viewpmsg.php");
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect(XOOPS_URL . "/viewpmsg.php", 1, _MD_PM_ERROR_ACCESS);
	}

	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		if (is_object($this->mObject)) {
			$controller->executeForward(XOOPS_URL . "/readpmsg.php?msg_id=" . $this->mObject->get('msg_id'));
		}
		else {
			$controller->executeForward(XOOPS_URL . "/viewpmsg.php");
		}
	}
}

?>
