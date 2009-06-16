<?php
/**
 *
 * @package Legacy
 * @version $Id: ModuleListForm.class.php,v 1.4 2008/09/25 15:11:11 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH."/core/XCube_ActionForm.class.php";

class Legacy_ModuleListForm extends XCube_ActionForm 
{
	/***
	 * If the request is GET, never return token name.
	 * By this logic, a action can have three page in one action.
	 */
	function getTokenName()
	{
		//
		//
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			return "module.legacy.ModuleSettingsForm.TOKEN";
		}
		else {
			return null;
		}
	}
	
	/***
	 * For displaying the confirm-page, don't show CSRF error.
	 * Always return null.
	 */
	function getTokenErrorMessage()
	{
		return null;
	}
	
	function prepare()
	{
		// set properties
		$this->mFormProperties['name']=new XCube_StringArrayProperty('name');
		$this->mFormProperties['weight']=new XCube_IntArrayProperty('weight');
		$this->mFormProperties['isactive']=new XCube_BoolArrayProperty('isactive');

		// set fields
		$this->mFieldProperties['name']=new XCube_FieldProperty($this);
		$this->mFieldProperties['name']->setDependsByArray(array('required','maxlength'));
		$this->mFieldProperties['name']->addMessage("required",_MD_LEGACY_ERROR_REQUIRED,_AD_LEGACY_LANG_NAME,"140");
		$this->mFieldProperties['name']->addMessage("maxlength",_MD_LEGACY_ERROR_MAXLENGTH,_AD_LEGACY_LANG_NAME,"140");
		$this->mFieldProperties['name']->addVar("maxlength",140);

		$this->mFieldProperties['weight']=new XCube_FieldProperty($this);
		$this->mFieldProperties['weight']->setDependsByArray(array('required','min'));
		$this->mFieldProperties['weight']->addMessage("min",_AD_LEGACY_ERROR_MIN,_AD_LEGACY_LANG_WEIGHT,"0");
		$this->mFieldProperties['weight']->addVar("min",0);
	}
}

?>
