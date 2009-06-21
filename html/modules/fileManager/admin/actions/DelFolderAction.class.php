<?php
/*=====================================================================
  (C)2007 BeaBo Japan by Hiroki Seike
  http://beabo.net/
=====================================================================*/
if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH. '/fileManager/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH. '/fileManager/admin/forms/AddFolderAdminForm.class.php';
require_once XOOPS_MODULE_PATH. '/fileManager/admin/include/functions.php';

class FileManager_DelFolderAction extends FileManager_AbstractEditAction
{

	var $folderName = null;
	var $isDelete = true;
	// for menu
	var $breadCrumbs = array();
	var $confirmMssage = null;
	var $moduleHeader = null;

	// change dirctory name
	function _getPath()
	{
		return xoops_getrequest('path');
	}
	// Over ride not usse
	function &_getHandler()
	{
		return 0;
	}

	// Over ride
	function _setupObject()
	{
		return 0;
	}

	function _setupActionForm()
	{
		$this->mActionForm =& new FileManager_AddFolderAdminForm();
		$this->mActionForm->prepare();
	}

	// Over ride
	function prepare(&$controller, &$xoopsUser, $moduleConfig)
	{
		$this->mConfig = $moduleConfig;
		if ($this->mConfig['dirhandle'] ==false) {
			$controller->executeForward('index.php');
		}
		$this->_setupActionForm();
	}

	// Over ride
	function getDefaultView(&$controller, &$xoopsUser)
	{
		if (preg_match ("/\..\//", $this->_getPath())) {
			return CONTENTS_FRAME_VIEW_ERROR;
		} 

		if ($this->_getPath() != '') {
			$this->confirmMssage = sprintf(_AD_FILEMANAGER_DELFOLDER_CONFIRMMSSAGE,  $this->_getPath() );
			$this->mActionForm->set('path',$this->_getPath());
		}

		$folderPath = XOOPS_UPLOAD_PATH. $this->_getPath();

		// isnot dirctory
		if (!is_dir($folderPath)) {
			$this->confirmMssage = sprintf(_AD_FILEMANAGER_DELFOLDER_ISDIR,  $this->_getPath() );
			$this->isDelete = false;
		}

		// dirctory permission is not 777
		if ( fileperms($folderPath) != '16895') {
			$this->confirmMssage = sprintf(_AD_FILEMANAGER_DELFOLDER_NOTACCESS,  $this->_getPath() );
			$this->isDelete = false;
		}

		// dirctory files check
		if (count(scandir($folderPath)) > 2) {
			$this->confirmMssage = sprintf(_AD_FILEMANAGER_DELFOLDER_FILE_EXISTS,  $this->_getPath() );
			$this->isDelete = false;
		}
		return CONTENTS_FRAME_VIEW_INPUT;
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{

		$root =& XCube_Root::getSingleton();

		// for menu

		// Initial setting
		$this->breadCrumbs[]  = array('name' => _AD_FILEMANAGER_DELFOLDER ) ;
		$this->menuDescription = _AD_FILEMANAGER_DELFOLDER_DSC ;

		// set template
		$render->setTemplateName('fileManager_dellfolder.html');

		// module info ( /admin/include/functions.php )
		$render->setAttribute('module_info'   , getModuleInfo());

		// haeder menu
		$render->setAttribute('moduleHeader'  , $this->moduleHeader);
		$render->setAttribute('bread_crumbs'  , $this->breadCrumbs);

		$render->setAttribute('confirm_mssage', $this->confirmMssage);
		$render->setAttribute('actionForm'    , $this->mActionForm);
		$render->setAttribute('is_delete'     , $this->isDelete);

	}

	// Over ride
	function execute(&$controller, &$xoopsUser)
	{
		if (xoops_getrequest('_form_control_cancel') != null) {
			return CONTENTS_FRAME_VIEW_CANCEL;
		}

		$delSuccess = false;
		// del dirctory
		$folderPath = XOOPS_UPLOAD_PATH. $this->_getPath();

		if (is_dir($folderPath)) {
			$dirPermission = fileperms($folderPath) ;
			
			// dirctory  permission is not 777
			if($dirPermission !='16895') {
				return CONTENTS_FRAME_VIEW_ERROR;
			}
			if (count(scandir($folderPath)) > 2) {
				return CONTENTS_FRAME_VIEW_ERROR;
			} else {
				$delSuccess = rmdir($folderPath);
			}
		}

		return $delSuccess ? CONTENTS_FRAME_VIEW_SUCCESS : CONTENTS_FRAME_VIEW_ERROR;
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect('index.php', 1, _AD_FILEMANAGER_DELFOLDER_SUCCESS);
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect('index.php', 1, _AD_FILEMANAGER_DELFOLDER_ERROR);
	}

	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward('index.php');
	}

	function _doExecute()
	{
		//return $this->mObjectHandler->delete($this->mObject);
	}

}
?>
