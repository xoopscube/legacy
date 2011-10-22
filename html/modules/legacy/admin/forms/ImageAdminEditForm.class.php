<?php
/**
 *
 * @package Legacy
 * @version $Id: ImageAdminEditForm.class.php,v 1.3 2008/09/25 15:11:09 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/forms/ImageUploadForm.class.php";

class Legacy_ImageAdminCreateForm extends Legacy_ImageUploadForm
{
	var $_mImgcatId = 0;
	
	function getTokenName()
	{
		return "module.legacy.ImageAdminEditForm.TOKEN" . $this->get('image_id');
	}

	function prepare()
	{
		parent::prepare();
		
		//
		// Set form properties
		//
		$this->mFormProperties['image_id'] =new XCube_IntProperty('image_id');
		$this->mFormProperties['image_display'] =new XCube_BoolProperty('image_display');
		$this->mFormProperties['image_weight'] =new XCube_IntProperty('image_weight');
	
		//
		// Set field properties
		//
		$this->mFieldProperties['image_id'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['image_id']->setDependsByArray(array('required'));
		$this->mFieldProperties['image_id']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_IMAGE_ID);
	
		$this->mFieldProperties['image_weight'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['image_weight']->setDependsByArray(array('required'));
		$this->mFieldProperties['image_weight']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_IMAGE_WEIGHT);
	}
	
	function load(&$obj)
	{
		parent::load($obj);
		$this->set('image_id', $obj->get('image_id'));
		$this->set('image_display', $obj->get('image_display'));
		$this->set('image_weight', $obj->get('image_weight'));
		
		$this->_mImgcatId = $obj->get('imgcat_id');
	}
	
	function update(&$obj)
	{
		parent::update($obj);
		$obj->set('image_id', $this->get('image_id'));
		$obj->set('image_display', $this->get('image_display'));
		$obj->set('image_weight', $this->get('image_weight'));
	}
}

class Legacy_ImageAdminEditForm extends Legacy_ImageAdminCreateForm
{
	function validateImgcat_id()
	{
		parent::validateImgcat_id();
		
		$handler =& xoops_getmodulehandler('imagecategory', 'legacy');
		$currentCategory =& $handler->get($this->_mImgcatId);
		
		$specificCategory =& $handler->get($this->get('imgcat_id'));
		if ($currentCategory->get('imgcat_storetype') != $specificCategory->get('imgcat_storetype')) {
			$this->set('imgcat_id', $this->_mImgcatId);
		}
	}
}

?>
