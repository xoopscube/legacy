<?php
/**
 * @package user
 * @version $Id: UserDataUploadAction.class.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractListAction.class.php";

class User_UserDataUploadAction extends User_Action
{
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('users');
		return $handler;
	}

	function _getBaseUrl()
	{
		return "./index.php?action=UserDataUpload";
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("user_data_upload.html");
	}
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		if (isset($_SESSION['user_csv_upload_data'])){
			unset($_SESSION['user_csv_upload_data']);
		}
		return USER_FRAME_VIEW_INDEX;
	}
	
	
	/// equals to getDefaultView()
	function execute(&$controller, &$xoopsUser)
	{
		return $this->getDefaultView($controller, $xoopsUser);
	}
}

?>
