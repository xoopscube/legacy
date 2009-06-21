<?php
/*=====================================================================
  (C)2007 BeaBo Japan by Hiroki Seike
  http://beabo.net/
=====================================================================*/
if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH. '/fileManager/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH. '/fileManager/admin/include/functions.php';

class FileManager_UploadAction extends FileManager_AbstractEditAction
{
	// for menu
	var $breadCrumbs     = array();
	var $confirmMssage   = null;
	var $moduleHeader    = null;
	var $menuDescription = null;

	var $debugMode   = false;
	var $defaultPass = null;
	var $upload_path = null;

	// change dirctory name
	function _getPath()
	{
		return xoops_getrequest('path');
	}

	// Over ride
	function prepare(&$controller, &$xoopsUser, $moduleConfig)
	{
		$this->mConfig = $moduleConfig;
	}

	// Over ride
	function getDefaultView(&$controller, &$xoopsUser)
	{
		// Relative path check
		if (preg_match ("/\..\//", $this->_getPath())) {
			return CONTENTS_FRAME_VIEW_ERROR;
		} 

		if ($this->_getPath() != '') {
			$this->defaultPass = $this->_getPath() .'/' ;
			$this->upload_path = $this->_getPath();
		} else {
			if ($this->mConfig['defaultpath'] != '') {
				// Relative path check for module config path
				if (preg_match ("/\..\//", $this->mConfig['defaultpath'])) {
					return CONTENTS_FRAME_VIEW_ERROR;
				} 
				$this->defaultPass = '/'. $this->mConfig['defaultpath'].'/' ;
				$this->upload_path = '/'. $this->mConfig['defaultpath'];
			} else {
				$this->defaultPass = '/' ;
			}
		}

		// SWFUpload debug mode is set to text
		if ($this->mConfig['debugon'] >0) {
			$this->debugMode = 'true';
		} else {
			$this->debugMode = 'false';
		}

		// upload path
		$defaultPath = XOOPS_UPLOAD_PATH . $this->defaultPass;
		if ($this->_getPath()) {
			$uploadPath = $defaultPath;
		} else {
			$uploadPath = $defaultPath.  $this->_getPath();
		}

		// info upload path and uploads size
		$this->confirmMssage = sprintf(_AD_FILEMANAGER_CONFIRMMSSAGE, $uploadPath,  FileSystemUtilty::bytes(ini_get('upload_max_filesize')) );


		// uploads directory
		$dirpath = XOOPS_UPLOAD_PATH. $this->_getPath();

		// check folder upload permission
		if (!is_writable($dirpath)) {
			return CONTENTS_FRAME_VIEW_ERROR;
		}

		$this->breadCrumbs[]  = array('name' => _AD_FILEMANAGER_UPLOAD ) ;
		$this->menuDescription = _AD_FILEMANAGER_UPLOAD_DSC ;

		return CONTENTS_FRAME_VIEW_INPUT;
	}

	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$root =& XCube_Root::getSingleton();

		$render->setTemplateName('fileManager_upload.html');
		$render->setAttribute('module_info'   , getModuleInfo());
		$render->setAttribute('moduleHeader'  , $this->moduleHeader);
		$render->setAttribute('bread_crumbs'  , $this->breadCrumbs);
		$render->setAttribute('upload_path'   , $this->upload_path);
		$render->setAttribute('debug_mode'    , $this->debugMode);
		$render->setAttribute('default_pass'  , $this->defaultPass);
		$render->setAttribute('confirm_mssage', $this->confirmMssage);

	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect('index.php', 1, _AD_FILEMANAGER_UPLOAD_PERMISSION);
	}


}
?>
