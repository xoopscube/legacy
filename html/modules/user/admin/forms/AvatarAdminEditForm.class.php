<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";

class User_AvatarAdminEditForm extends XCube_ActionForm
{
	var $mOldFileName = null;
	var $_mIsNew = false;
	var $mFormFile = null;
	
	function getTokenName()
	{
		return "module.user.AvatarAdminEditForm.TOKEN" . $this->get('avatar_id');
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['avatar_id'] =new XCube_IntProperty('avatar_id');
		$this->mFormProperties['avatar_file'] =new XCube_FileProperty('avatar_file');
		$this->mFormProperties['avatar_name'] =new XCube_StringProperty('avatar_name');
		$this->mFormProperties['avatar_display'] =new XCube_BoolProperty('avatar_display');
		$this->mFormProperties['avatar_weight'] =new XCube_IntProperty('avatar_weight');

		//
		// Set field properties
		//
		$this->mFieldProperties['avatar_id'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['avatar_id']->setDependsByArray(array('required'));
		$this->mFieldProperties['avatar_id']->addMessage('required', _MD_USER_ERROR_REQUIRED, _MD_USER_LANG_AVATAR_ID);

		$this->mFieldProperties['avatar_file'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['avatar_file']->setDependsByArray(array('extension'));
		$this->mFieldProperties['avatar_file']->addMessage('extension', _MD_USER_ERROR_AVATAR_EXTENSION, _AD_USER_LANG_AVATAR_FILE);
		$this->mFieldProperties['avatar_file']->addVar('extension', "gif,png,jpg");

		$this->mFieldProperties['avatar_name'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['avatar_name']->setDependsByArray(array('required','maxlength'));
		$this->mFieldProperties['avatar_name']->addMessage('required', _MD_USER_ERROR_REQUIRED, _AD_USER_LANG_AVATAR_NAME, '100');
		$this->mFieldProperties['avatar_name']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _AD_USER_LANG_AVATAR_NAME, '100');
		$this->mFieldProperties['avatar_name']->addVar('maxlength', 100);

		$this->mFieldProperties['avatar_weight'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['avatar_weight']->setDependsByArray(array('required'));
		$this->mFieldProperties['avatar_weight']->addMessage('required', _MD_USER_ERROR_REQUIRED, _AD_USER_LANG_AVATAR_WEIGHT);
	}

	function validateAvatar_file()
	{
		if ($this->_mIsNew && $this->get('avatar_file') == null) {
			$this->addErrorMessage(_AD_USER_ERROR_IMAGE_REQUIRED);
		}
	}
	
	function load(&$obj)
	{
		$this->set('avatar_id', $obj->get('avatar_id'));
		$this->set('avatar_name', $obj->get('avatar_name'));
		$this->set('avatar_display', $obj->get('avatar_display'));
		$this->set('avatar_weight', $obj->get('avatar_weight'));
		
		$this->_mIsNew = $obj->isNew();
		$this->mOldFileName = $obj->get('avatar_file');
	}

	function update(&$obj)
	{
		$obj->set('avatar_id', $this->get('avatar_id'));
		$obj->set('avatar_name', $this->get('avatar_name'));
		$obj->set('avatar_display', $this->get('avatar_display'));
		$obj->set('avatar_weight', $this->get('avatar_weight'));
	
		$this->mFormFile = $this->get('avatar_file');
		if ($this->mFormFile != null) {
			$this->mFormFile->setRandomToBodyName('savt');
			$filename = $this->mFormFile->getBodyName();
			$this->mFormFile->setBodyName(substr($filename, 0, 24));
			
			$obj->setVar('avatar_file', $this->mFormFile->getFileName());
			$obj->setVar('avatar_mimetype', $this->mFormFile->getContentType());
		}
	}
}

?>
