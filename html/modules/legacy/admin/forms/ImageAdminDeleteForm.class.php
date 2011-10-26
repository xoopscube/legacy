<?php
/**
 *
 * @package Legacy
 * @version $Id: ImageAdminDeleteForm.class.php,v 1.3 2008/09/25 15:10:35 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

class Legacy_ImageAdminDeleteForm extends XCube_ActionForm
{
	function getTokenName()
	{
		return "module.legacy.ImageAdminDeleteForm.TOKEN" . $this->get('image_id');
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['image_id'] =new XCube_IntProperty('image_id');
	
		//
		// Set field properties
		//
		$this->mFieldProperties['image_id'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['image_id']->setDependsByArray(array('required'));
		$this->mFieldProperties['image_id']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_IMAGE_ID);
	}

	function load(&$obj)
	{
		$this->set('image_id', $obj->get('image_id'));
	}

	function update(&$obj)
	{
		$obj->set('image_id', $this->get('image_id'));
	}
}

?>
