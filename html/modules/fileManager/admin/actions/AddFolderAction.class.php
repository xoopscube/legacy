<?php
/*=====================================================================
  (C)2007 BeaBo Japan by Hiroki Seike
  http://beabo.net/
=====================================================================*/
if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH. '/fileManager/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH. '/fileManager/admin/forms/AddFolderAdminForm.class.php';
require_once XOOPS_MODULE_PATH. '/fileManager/admin/include/functions.php';

class FileManager_AddFolderAction extends FileManager_AbstractEditAction
{

	var $folderName = null;

	// for menu
	var $breadCrumbs = array();
	var $confirmMssage = null;
	var $moduleHeader = null;
	var $menuDescription = null;

	// change dirctory name
	function _getPath()
	{
		return xoops_getrequest('path');
	}
	function _getFoldername()
	{
		return xoops_getrequest('foldername');
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

		$folderPath = XOOPS_UPLOAD_PATH. $this->_getPath();

		// check dirctory permission
		if (FileSystemUtilty::checkFolder(XOOPS_UPLOAD_PATH .$folderPath)) {
			return CONTENTS_FRAME_VIEW_ERROR;
		}

		return CONTENTS_FRAME_VIEW_INPUT;
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		// for menu

		// Initial setting
		$this->breadCrumbs[]  = array('name' => _AD_FILEMANAGER_ADDFOLDER ) ;
		$this->menuDescription = _AD_FILEMANAGER_ADDFOLDER_DSC ;

		$root =& XCube_Root::getSingleton();
		$defaultPath = XOOPS_UPLOAD_PATH .'/'. $root->mContext->mModuleConfig['defaultpath'];

		if ($this->_getPath() != '') {
			$this->confirmMssage = sprintf(_AD_FILEMANAGER_ADDFOLDER_CONFIRMMSSAGE, $defaultPath. $this->_getPath() );
			$this->mActionForm->set('path',$this->_getPath());
		} else {
			$this->confirmMssage = sprintf(_AD_FILEMANAGER_ADDFOLDER_CONFIRMMSSAGE, $defaultPath);
		}

		// set template
		$render->setTemplateName('fileManager_addfolder.html');

		// module info ( /admin/include/functions.php )
		$render->setAttribute('module_info'   , getModuleInfo());

		// haeder menu
		$render->setAttribute('moduleHeader'  , $this->moduleHeader);
		$render->setAttribute('bread_crumbs'  , $this->breadCrumbs);

		$render->setAttribute('confirm_mssage', $this->confirmMssage);
		$render->setAttribute('actionForm'    , $this->mActionForm);
	}

	// Over ride
	function execute(&$controller, &$xoopsUser)
	{
		if (xoops_getrequest('_form_control_cancel') != null) {
			return CONTENTS_FRAME_VIEW_CANCEL;
		}
		$this->mActionForm->load($this->mObject);
		$this->mActionForm->fetch();
		$this->mActionForm->validate();
		if ($this->mActionForm->hasError()) {
			return CONTENTS_FRAME_VIEW_INPUT;
		}

		$newFolderPath = XOOPS_UPLOAD_PATH. $this->_getPath(). '/'. $this->_getFoldername();

		// check same name dirctory
		if (file_exists($newFolderPath)) {
			$mkdirSuccess = false; 
		} else {
			// make new dirctory
			$mkdirSuccess = mkdir($newFolderPath, 0777);
			// chmod is not suported windows
			chmod($newFolderPath, 0777);
		}

		return $mkdirSuccess ? CONTENTS_FRAME_VIEW_SUCCESS : CONTENTS_FRAME_VIEW_ERROR;
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$url = 'index.php';
		if ($this->_getPath() != '') {
			$url = 'index.php?path='. $this->_getPath() ;
		}
		$controller->executeRedirect($url, 1, _AD_FILEMANAGER_ADDFOLDER_SUCCESS);
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$url = 'index.php';
		$controller->executeRedirect($url, 1, _AD_FILEMANAGER_ADDFOLDER_ERROR);
	}

	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		$url = 'index.php';
		if ($this->_getPath() != '') {
			$url = 'index.php?path='. $this->_getPath() ;
		}
		$controller->executeForward($url);
	}

}
?>
