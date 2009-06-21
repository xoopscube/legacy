<?php
/*=====================================================================
  (C)2007 BeaBo Japan by Hiroki Seike
  http://beabo.net/
=====================================================================*/

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH.'/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH.'/legacy/class/Legacy_Validator.class.php';


class FileManager_DelFolderAdminForm extends XCube_ActionForm
{

	function FileManager_DelFolderAdminForm()
	{
		parent::XCube_ActionForm();
	}

	function getTokenName()
	{
		return 'module.fileManager.DelFolderAdminForm.TOKEN';
	}

	function prepare()
	{
		$this->mFormProperties['path'] =& new XCube_StringProperty('path');

		$this->mFieldProperties['path'] =& new XCube_FieldProperty($this);
		$this->mFieldProperties['path']->setDependsByArray(array('required'));
		$this->mFieldProperties['path']->addMessage('required', _AD_FILEMANAGER_ERROR_REQUIRED, _AD_FILEMANAGER_FOLDERNAME);
	}

	function validateFoldername()
	{
		if ($this->get('path') != null) {
			// check folder name
			if (preg_match ("/\..\//", $this->get('path'))) {
				$this->addErrorMessage(_AD_FILEMANAGER_ERROR_FOLDERNAME);
			}
		}
	}

	function load(&$obj)
	{
		$this->set('path', $this->get('path'));
	}

	function update(&$obj)
	{
		$this->set('hello',1);
	}
}
?>