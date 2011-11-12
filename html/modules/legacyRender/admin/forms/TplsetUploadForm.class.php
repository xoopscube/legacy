<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

class LegacyRender_TplsetUploadForm extends XCube_ActionForm
{
	var $mOldFileName = null;
	var $_mIsNew = null;
	var $mFormFile = null;
	var $_allowExtensions = array('tar', 'tar.gz', 'tgz', 'gz');

	function getTokenName()
	{
		return "module.legacyRender.TplsetUploadForm.TOKEN";
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['upload'] =new XCube_FileProperty('upload');
		$this->mFormProperties['tplset_name'] =new XCube_StringProperty('tplset_name');
	
		//
		// Set field properties
		//
		$this->mFieldProperties['upload'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['upload']->setDependsByArray(array('required'));
		$this->mFieldProperties['upload']->addMessage('required', _AD_LEGACYRENDER_ERROR_REQUIRED, _AD_LEGACYRENDER_LANG_TPLSET_UPLOAD_FILE);
	
		$this->mFieldProperties['tplset_name'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['tplset_name']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['tplset_name']->addMessage('maxlength', _AD_LEGACYRENDER_ERROR_MAXLENGTH, _AD_LEGACYRENDER_LANG_TPLSET_DESC, '50');
		$this->mFieldProperties['tplset_name']->addVar('maxlength', '50');
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
				$this->addErrorMessage(_AD_LEGACYRENDER_ERROR_EXTENSION_IS_WRONG);
			}
		}
	}
}

?>
