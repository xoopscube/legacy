<?php
/**
 * @package legacyRender
 * @version $Id: TplfileViewAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class LegacyRender_TplfileViewAction extends LegacyRender_Action
{
	var $mObject = null;
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		$id = xoops_getrequest('tpl_id');
		
		$handler =& xoops_getmodulehandler('tplfile');
		$this->mObject =& $handler->get($id);
		
		if (!is_object($this->mObject)) {
			return LEGACYRENDER_FRAME_VIEW_ERROR;
		}
		
		return LEGACYRENDER_FRAME_VIEW_SUCCESS;
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$this->mObject->loadSource();

		$render->setTemplateName("tplfile_view.html");
		$render->setAttribute('object', $this->mObject);
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect("./index.php?action=TplsetList", 1, _AD_LEGACYRENDER_ERROR_OBJECT_IS_NOT_EXIST);
	}
}

?>