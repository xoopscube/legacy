<?php
/**
 * Filemaneger
 * (C)2007-2009 BeaBo Japan by Hiroki Seike
 * http://beabo.net/
 **/

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH.'/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH.'/legacy/class/Legacy_Validator.class.php';

class FileManager_AddFolderAdminForm extends XCube_ActionForm
{
	function FileManager_AddFolderAdminForm()
	{
		parent::XCube_ActionForm();
	}

	function getTokenName()
	{
		return 'module.fileManager.AddFolderAdminForm.TOKEN';
	}

	function prepare()
	{
		$this->mFormProperties['foldername'] =& new XCube_StringProperty('foldername');
		$this->mFormProperties['path'] =& new XCube_StringProperty('path');

		$this->mFieldProperties['foldername'] =& new XCube_FieldProperty($this);
		$this->mFieldProperties['foldername']->setDependsByArray(array('required'));
		$this->mFieldProperties['foldername']->addMessage('required', _AD_FILEMANAGER_ERROR_REQUIRED, _AD_FILEMANAGER_FOLDERNAME);
	}

	function validateFoldername()
	{
		if ($this->get('foldername') != null) {
			// check words
			if (!preg_match("/^[a-z0-9_~\-]+$/", $this->get('foldername'))) {
				$this->addErrorMessage(_AD_FILEMANAGER_ERROR_FOLDERNAME);
			}
			// check directory traversal
			if (preg_match ("/\..\//", $this->get('foldername'))) {
				$this->addErrorMessage(_AD_FILEMANAGER_ERROR_PATH);
			}
		}
	}

	function load(&$obj)
	{
		$this->set('foldername', $this->get('foldername'));
		$this->set('path', $this->get('path'));

	}

	function update(&$obj)
	{
		$this->set('hello',1);
	}
}
?>
