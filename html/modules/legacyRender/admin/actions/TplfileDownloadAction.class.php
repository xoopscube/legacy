<?php
/**
 * @package legacyRender
 * @version $Id: TplfileDownloadAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacyRender/admin/forms/TplfileEditForm.class.php";

class LegacyRender_TplfileDownloadAction extends LegacyRender_Action
{
	var $mObject = null;
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		$id = xoops_getrequest('tpl_id');
		
		$handler =& xoops_getmodulehandler('tplfile');
		$this->mObject =& $handler->get($id);
		
		return $this->mObject != null ? LEGACYRENDER_FRAME_VIEW_SUCCESS : LEGACYRENDER_FRAME_VIEW_ERROR;
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$this->mObject->loadSource();
		if ($this->mObject->Source == null) {
			return LEGACYRENDER_FRAME_VIEW_ERROR;
		}

		$source = $this->mObject->Source->get('tpl_source');
		
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-Type: application/force-download');
		
		if (preg_match("/MSIE 5.5/", $_SERVER['HTTP_USER_AGENT'])) {
			header('Content-Disposition: filename=' . $this->mObject->getShow('tpl_file'));
		} else {
			header('Content-Disposition: attachment; filename=' . $this->mObject->getShow('tpl_file'));
		}

		header('Content-length: ' . strlen($source));
		print $source;
		
		exit(0);
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect("./index.php?action=TplsetList", 1, _AD_LEGACYRENDER_ERROR_DBUPDATE_FAILED);
	}
}

?>
