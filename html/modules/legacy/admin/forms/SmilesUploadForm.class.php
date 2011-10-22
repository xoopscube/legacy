<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

class Legacy_SmilesUploadForm extends XCube_ActionForm
{
	var $mOldFileName = null;
	var $_mIsNew = null;
	var $mFormFile = null;
	var $_allowExtensions = array('tar', 'tar.gz', 'tgz', 'gz', 'zip');

	function getTokenName()
	{
		return "module.legacy.SmilesUploadForm.TOKEN";
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['upload'] =new XCube_FileProperty('upload');
	
		//
		// Set field properties
		//
		$this->mFieldProperties['upload'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['upload']->setDependsByArray(array('required'));
		$this->mFieldProperties['upload']->addMessage('required', _AD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_SMILES_UPLOAD_FILE);
	
	}
	
	function validateUpload()
	{
		$formFile = $this->get('upload');
		if ($formFile != null) {
			$flag = false;
			foreach ($this->_allowExtensions as $ext) {
				$flag |= preg_match("/" . str_replace(".", "\.", $ext) . "$/", $formFile->getFileName());
			}
			
			if (!$flag) {
				$this->addErrorMessage(_AD_LEGACY_ERROR_EXTENSION_IS_WRONG);
			}
		}
	}
}

?>
