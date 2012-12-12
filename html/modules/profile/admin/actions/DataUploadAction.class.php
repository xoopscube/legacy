<?php
/**
 * @package user
 * @version $Id: UserDataUploadAction.class.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/profile/class/AbstractListAction.class.php";

class Profile_Admin_DataUploadAction extends Profile_AbstractListAction
{
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('profile');
		return $handler;
	}

	function _getBaseUrl()
	{
		return "./index.php?action=DataUpload";
	}

	function executeViewIndex(&$render)
	{
		$render->setTemplateName("data_upload.html");
	}
	
	function getDefaultView()
	{
		if (isset($_SESSION['profile_csv_upload_data'])){
			unset($_SESSION['profile_csv_upload_data']);
		}
		return PROFILE_FRAME_VIEW_INDEX;
	}
	
	
	/// equals to getDefaultView()
	function execute(&$controller, &$xoopsUser)
	{
		return $this->getDefaultView();
	}
}

?>
