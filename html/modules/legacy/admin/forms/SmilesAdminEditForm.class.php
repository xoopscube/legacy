<?php
/**
 *
 * @package Legacy
 * @version $Id: SmilesAdminEditForm.class.php,v 1.3 2008/09/25 15:11:20 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";

class Legacy_SmilesAdminEditForm extends XCube_ActionForm
{
	var $mOldFileName = null;
	var $_mIsNew = null;
	var $mFormFile = null;

	function getTokenName()
	{
		return "module.legacy.SmilesAdminEditForm.TOKEN" . $this->get('id');
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['id'] =new XCube_IntProperty('id');
		$this->mFormProperties['code'] =new XCube_StringProperty('code');
		$this->mFormProperties['smile_url'] =new XCube_ImageFileProperty('smile_url');
		$this->mFormProperties['emotion'] =new XCube_StringProperty('emotion');
		$this->mFormProperties['display'] =new XCube_BoolProperty('display');
	
		//
		// Set field properties
		//
		$this->mFieldProperties['id'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['id']->setDependsByArray(array('required'));
		$this->mFieldProperties['id']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_ID);
	
		$this->mFieldProperties['code'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['code']->setDependsByArray(array('required','maxlength'));
		$this->mFieldProperties['code']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_CODE, '50');
		$this->mFieldProperties['code']->addMessage('maxlength', _MD_LEGACY_ERROR_MAXLENGTH, _MD_LEGACY_LANG_CODE, '50');
		$this->mFieldProperties['code']->addVar('maxlength', '50');
	
		$this->mFieldProperties['smile_url'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['smile_url']->setDependsByArray(array('extension'));
		$this->mFieldProperties['smile_url']->addMessage('extension', _AD_LEGACY_ERROR_EXTENSION);
		$this->mFieldProperties['smile_url']->addVar('extension', 'jpg,gif,png');
	
		$this->mFieldProperties['emotion'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['emotion']->setDependsByArray(array('required','maxlength'));
		$this->mFieldProperties['emotion']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_EMOTION, '75');
		$this->mFieldProperties['emotion']->addMessage('maxlength', _MD_LEGACY_ERROR_MAXLENGTH, _MD_LEGACY_LANG_EMOTION, '75');
		$this->mFieldProperties['emotion']->addVar('maxlength', '75');
	}

	function validateSmile_url()
	{
		if ($this->_mIsNew && $this->get('smile_url') == null) {
			$this->addErrorMessage(XCube_Utils::formatMessage(_MD_LEGACY_ERROR_REQUIRED, _AD_LEGACY_LANG_SMILE_URL));
		}
	}

	function load(&$obj)
	{
		$this->set('id', $obj->get('id'));
		$this->set('code', $obj->get('code'));
		$this->set('emotion', $obj->get('emotion'));
		$this->set('display', $obj->get('display'));
		
		$this->_mIsNew = $obj->isNew();
		$this->mOldFileName = $obj->get('smile_url');
	}

	function update(&$obj)
	{
		$obj->set('id', $this->get('id'));
		$obj->set('code', $this->get('code'));
		$obj->set('emotion', $this->get('emotion'));
		$obj->set('display', $this->get('display'));
		
		$this->mFormFile = $this->get('smile_url');
		if ($this->mFormFile != null) {
			$this->mFormFile->setRandomToBodyName('smil');	// Fix your prefix
			$obj->set('smile_url', $this->mFormFile->getFileName());
		}
	}
}

?>
