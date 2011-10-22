<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

class LegacyRender_TplfileUploadForm extends XCube_ActionForm
{
	function getTokenName()
	{
		return "module.legacyRender.TplfileUploadForm.TOKEN";
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['upload'] =new XCube_FileArrayProperty('upload');
	
		//
		// Set field properties
		//
	}
}

?>
