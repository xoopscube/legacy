<?php
/**
 *
 * @package Legacy
 * @version $Id: ImagecategoryAdminNewForm.class.php,v 1.4 2008/09/25 15:10:54 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/ImagecategoryAdminEditForm.class.php";

class Legacy_ImagecategoryAdminNewForm extends Legacy_ImagecategoryAdminEditForm
{
	function getTokenName()
	{
		return "module.legacy.ImagecategoryAdminNewForm.TOKEN";
	}

	function prepare()
	{
		parent::prepare();
		
		//
		// Set form properties
		//
		$this->mFormProperties['imgcat_storetype'] =new XCube_StringProperty('imgcat_storetype');
	
		//
		// Set field properties
		//
		$this->mFieldProperties['imgcat_storetype'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['imgcat_storetype']->setDependsByArray(array('required','mask'));
		$this->mFieldProperties['imgcat_storetype']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_IMGCAT_STORETYPE);
		$this->mFieldProperties['imgcat_storetype']->addMessage('mask', _MD_LEGACY_ERROR_MASK, _AD_LEGACY_LANG_IMGCAT_STORETYPE);
		$this->mFieldProperties['imgcat_storetype']->addVar('mask', '/^(file|db)$/');
	}
	
	function load(&$obj)
	{
		parent::load($obj);
		$this->set('imgcat_storetype', $obj->get('imgcat_storetype'));
	}

	function update(&$obj)
	{
		parent::update($obj);
		$obj->set('imgcat_storetype', $this->get('imgcat_storetype'));
	}
}

?>
