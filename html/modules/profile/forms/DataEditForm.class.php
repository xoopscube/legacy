<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

class Profile_DataEditForm extends XCube_ActionForm
{
	//table field definitions
	var $mDef = array();

	/**
	 * @public
	 */
	function getTokenName()
	{
		return "module.profile.DataEditForm.TOKEN";
	}

	/**
	 * @public
	 */
	function prepare()
	{
		$handler =& xoops_getmodulehandler('definitions');
		$this->mDef = $handler->getObjects();
	
		//
		// Set form properties
		//
		$this->mFormProperties['uid'] =& new XCube_IntProperty('uid');
		foreach(array_keys($this->mDef) as $key){
			switch($this->mDef[$key]->get('type')){
				case 'string':
					$this->mFormProperties[$this->mDef[$key]->get('field_name')] =& new XCube_StringProperty($this->mDef[$key]->get('field_name'));
					break;
				case 'text':
					$this->mFormProperties[$this->mDef[$key]->get('field_name')] =& new XCube_TextProperty($this->mDef[$key]->get('field_name'));
					break;
				case 'int':
					$this->mFormProperties[$this->mDef[$key]->get('field_name')] =& new XCube_IntProperty($this->mDef[$key]->get('field_name'));
					break;
				case 'date':
					$this->mFormProperties[$this->mDef[$key]->get('field_name')] =& new XCube_IntProperty($this->mDef[$key]->get('field_name'));
					break;
				case 'checkbox':
					$this->mFormProperties[$this->mDef[$key]->get('field_name')] =& new XCube_BoolProperty($this->mDef[$key]->get('field_name'));
					break;
				case 'selectbox':
					$this->mFormProperties[$this->mDef[$key]->get('field_name')] =& new XCube_StringProperty($this->mDef[$key]->get('field_name'));
					break;
			}
			//validation checks
			$validationArr = array();
			$this->mFieldProperties[$this->mDef[$key]->get('field_name')] =& new XCube_FieldProperty($this);
			//required check
			if($this->mDef[$key]->get('required')==1){
				$validationArr[] = 'required';
				$this->mFieldProperties[$this->mDef[$key]->get('field_name')]->addMessage('required', _MD_PROFILE_ERROR_REQUIRED, $this->mDef[$key]->get('label'));
			}
			//validation check
			switch($this->mDef[$key]->get('validation')){
			case 'email' :
				$validationArr[] = 'email';
				$this->mFieldProperties[$this->mDef[$key]->get('field_name')]->addMessage($this->mDef[$key]->get('field_name'), _MD_PROFILE_ERROR_EMAIL);
			break;
			}
			$this->mFieldProperties[$this->mDef[$key]->get('field_name')]->setDependsByArray($validationArr);
		}
	
		//
		// Set field properties
		//
		$this->mFieldProperties['uid'] =& new XCube_FieldProperty($this);
		$this->mFieldProperties['uid']->setDependsByArray(array('required'));
		$this->mFieldProperties['uid']->addMessage('required', _MD_PROFILE_ERROR_REQUIRED, _MD_PROFILE_LANG_UID);
	}

	/**
	 * @public
	 */
	function load(&$obj)
	{
		$this->set('uid', $obj->get('uid'));
		foreach(array_keys($this->mDef) as $key){
			$this->set($this->mDef[$key]->get('field_name'), $obj->get($this->mDef[$key]->get('field_name')));
		}
	}

	/**
	 * @public
	 */
	function update(&$obj)
	{
		$obj->set('uid', $this->get('uid'));
		foreach(array_keys($this->mDef) as $key){
			$obj->set($this->mDef[$key]->get('field_name'), $this->get($this->mDef[$key]->get('field_name')));
		}
	}
}

?>
