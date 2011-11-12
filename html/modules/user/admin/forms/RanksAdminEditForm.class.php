<?php
/**
 * @package user
 * @version $Id: RanksAdminEditForm.class.php,v 1.2 2007/06/07 05:27:37 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";

class User_RanksAdminEditForm extends XCube_ActionForm
{
	var $mOldFileName = null;
	var $_mIsNew = false;
	var $mFormFile = null;
	
	function getTokenName()
	{
		return "module.user.RanksAdminEditForm.TOKEN" . $this->get('rank_id');
	}

	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['rank_id'] =new XCube_IntProperty('rank_id');
		$this->mFormProperties['rank_title'] =new XCube_StringProperty('rank_title');
		$this->mFormProperties['rank_min'] =new XCube_IntProperty('rank_min');
		$this->mFormProperties['rank_max'] =new XCube_IntProperty('rank_max');
		$this->mFormProperties['rank_special'] =new XCube_BoolProperty('rank_special');
		$this->mFormProperties['rank_image'] =new XCube_FileProperty('rank_image');

		//
		// Set field properties
		//
		$this->mFieldProperties['rank_id'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['rank_id']->setDependsByArray(array('required'));
		$this->mFieldProperties['rank_id']->addMessage('required', _MD_USER_ERROR_REQUIRED, _MD_USER_LANG_RANK_ID);

		$this->mFieldProperties['rank_title'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['rank_title']->setDependsByArray(array('required','maxlength'));
		$this->mFieldProperties['rank_title']->addMessage('required', _MD_USER_ERROR_REQUIRED, _AD_USER_LANG_RANK_TITLE, '50');
		$this->mFieldProperties['rank_title']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _AD_USER_LANG_RANK_TITLE, '50');
		$this->mFieldProperties['rank_title']->addVar('maxlength', 50);

		$this->mFieldProperties['rank_min'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['rank_min']->setDependsByArray(array('required', 'min'));
		$this->mFieldProperties['rank_min']->addMessage('required', _MD_USER_ERROR_REQUIRED, _AD_USER_LANG_RANK_MIN);
		$this->mFieldProperties['rank_min']->addMessage('min', _AD_USER_ERROR_MIN, _AD_USER_LANG_RANK_MIN, 0);
		$this->mFieldProperties['rank_min']->addVar('min', 0);

		$this->mFieldProperties['rank_max'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['rank_max']->setDependsByArray(array('required', 'min'));
		$this->mFieldProperties['rank_max']->addMessage('required', _MD_USER_ERROR_REQUIRED, _AD_USER_LANG_RANK_MAX);
		$this->mFieldProperties['rank_max']->addMessage('min', _AD_USER_ERROR_MIN, _AD_USER_LANG_RANK_MAX, 0);
		$this->mFieldProperties['rank_max']->addVar('min', 0);
	}
	
	function validateRank_max()
	{
		if ($this->get('rank_max') < $this->get('rank_min')) {
			$this->addErrorMessage(_AD_USER_ERROR_INJURY_MIN_MAX);
		}
	}

	function validateRank_image()
	{
		if ($this->_mIsNew && $this->get('rank_image') == null) {
			$this->addErrorMessage(_AD_USER_ERROR_IMAGE_REQUIRED);
		}
	}
	
	function load(&$obj)
	{
		$this->set('rank_id', $obj->get('rank_id'));
		$this->set('rank_title', $obj->get('rank_title'));
		$this->set('rank_min', $obj->get('rank_min'));
		$this->set('rank_max', $obj->get('rank_max'));
		$this->set('rank_special', $obj->get('rank_special'));

		$this->_mIsNew = $obj->isNew();
		$this->mOldFileName = $obj->get('rank_image');
	}

	function update(&$obj)
	{
		$obj->set('rank_id', $this->get('rank_id'));
		$obj->set('rank_title', $this->get('rank_title'));
		$obj->set('rank_min', $this->get('rank_min'));
		$obj->set('rank_max', $this->get('rank_max'));
		$obj->set('rank_special', $this->get('rank_special'));

		$this->mFormFile = $this->get('rank_image');
		if ($this->mFormFile != null) {
			$this->mFormFile->setRandomToBodyName('rank');
			$obj->set('rank_image', $this->mFormFile->getFileName());
		}
	}
}

?>
