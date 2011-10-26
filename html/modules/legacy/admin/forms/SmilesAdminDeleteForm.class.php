<?php
/**
 *
 * @package Legacy
 * @version $Id: SmilesAdminDeleteForm.class.php,v 1.3 2008/09/25 15:11:10 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";

class Legacy_SmilesAdminDeleteForm extends XCube_ActionForm
{
	function getTokenName()
	{
		return "module.legacy.SmilesAdminDeleteForm.TOKEN" . $this->get('id');
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['id'] =new XCube_IntProperty('id');
	
		//
		// Set field properties
		//
		$this->mFieldProperties['id'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['id']->setDependsByArray(array('required'));
		$this->mFieldProperties['id']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_ID);
	}

	function load(&$obj)
	{
		$this->set('id', $obj->get('id'));
	}

	function update(&$obj)
	{
		$obj->set('id', $this->get('id'));
	}
}

?>
