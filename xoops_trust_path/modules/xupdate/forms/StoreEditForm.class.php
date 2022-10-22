<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Naoki Sawada, Naoki Okino, Gigamaster (XCL 2.3)
 * @copyright Copyright 2005-2022 XOOPSCube Project
 * @license https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

/**
 * Xupdate_StoreEditForm
 **/
class Xupdate_StoreEditForm extends XCube_ActionForm {
	/**
	 * getTokenName
	 *
	 * @param void
	 *
	 * @return  string
	 **/
	public function getTokenName() {
		return 'module.xupdate.StoreEditForm.TOKEN';
	}

	/**
	 * prepare
	 *
	 * @param void
	 *
	 * @return  void
	 **/
	public function prepare() {
		//
		// Set form properties
		//
		$this->mFormProperties['sid'] = new XCube_IntProperty( 'sid' );
//		$this->mFormProperties['uid'] = new XCube_IntProperty('uid');
//		$this->mFormProperties['valid'] = new XCube_IntProperty('valid');
		$this->mFormProperties['name']         = new XCube_StringProperty( 'name' );
		$this->mFormProperties['contents']     = new XCube_StringProperty( 'contents' );
		$this->mFormProperties['addon_url']    = new XCube_StringProperty( 'addon_url' );
		$this->mFormProperties['setting_type'] = new XCube_IntProperty( 'setting_type' );
//		$this->mFormProperties['theme_url'] = new XCube_StringProperty('theme_url');
		$this->mFormProperties['reg_unixtime'] = new XCube_IntProperty( 'reg_unixtime' );


		//
		// Set field properties
		//
		$this->mFieldProperties['sid'] = new XCube_FieldProperty( $this );
		$this->mFieldProperties['sid']->setDependsByArray( [ 'required' ] );
		$this->mFieldProperties['sid']->addMessage( 'required', _MD_XUPDATE_ERROR_REQUIRED, _MD_XUPDATE_LANG_SID );
		/*
				$this->mFieldProperties['uid'] = new XCube_FieldProperty($this);
				$this->mFieldProperties['uid']->setDependsByArray(array('required'));
				$this->mFieldProperties['uid']->addMessage('required', _MD_XUPDATE_ERROR_REQUIRED, _MD_XUPDATE_LANG_UID);
				$this->mFieldProperties['valid'] = new XCube_FieldProperty($this);
				$this->mFieldProperties['valid']->setDependsByArray(array('required'));
				$this->mFieldProperties['valid']->addMessage('required', _MD_XUPDATE_ERROR_REQUIRED, _MD_XUPDATE_LANG_VALID);
		*/
		$this->mFieldProperties['name'] = new XCube_FieldProperty( $this );
		$this->mFieldProperties['name']->setDependsByArray( [ 'required', 'maxlength' ] );
		$this->mFieldProperties['name']->addMessage( 'required', _MD_XUPDATE_ERROR_REQUIRED, _MD_XUPDATE_LANG_NAME );
		$this->mFieldProperties['name']->addMessage( 'maxlength', _MD_XUPDATE_ERROR_MAXLENGTH, _MD_XUPDATE_LANG_NAME, '191' );
		$this->mFieldProperties['name']->addVar( 'maxlength', '191' );
		$this->mFieldProperties['contents'] = new XCube_FieldProperty( $this );
		$this->mFieldProperties['contents']->setDependsByArray( [ 'required', 'maxlength' ] );
		$this->mFieldProperties['contents']->addMessage( 'required', _MD_XUPDATE_ERROR_REQUIRED, _MD_XUPDATE_LANG_NAME );
		$this->mFieldProperties['contents']->addMessage( 'maxlength', _MD_XUPDATE_ERROR_MAXLENGTH, _MD_XUPDATE_LANG_NAME, '191' );
		$this->mFieldProperties['contents']->addVar( 'maxlength', '191' );
		$this->mFieldProperties['addon_url'] = new XCube_FieldProperty( $this );
		$this->mFieldProperties['addon_url']->setDependsByArray( [ 'required', 'maxlength' ] );
		$this->mFieldProperties['addon_url']->addMessage( 'required', _MD_XUPDATE_ERROR_REQUIRED, _MD_XUPDATE_LANG_ADDON_URL );
		$this->mFieldProperties['addon_url']->addMessage( 'maxlength', _MD_XUPDATE_ERROR_MAXLENGTH, _MD_XUPDATE_LANG_ADDON_URL, '191' );
		$this->mFieldProperties['addon_url']->addVar( 'maxlength', '191' );

		$this->mFieldProperties['setting_type'] = new XCube_FieldProperty( $this );
		$this->mFieldProperties['setting_type']->setDependsByArray( [ 'required' ] );
		$this->mFieldProperties['setting_type']->addMessage( 'required', _MD_XUPDATE_ERROR_REQUIRED, _MD_XUPDATE_LANG_SETTING_TYPE );
		/*
				$this->mFieldProperties['theme_url'] = new XCube_FieldProperty($this);
				$this->mFieldProperties['theme_url']->setDependsByArray(array('required','maxlength'));
				$this->mFieldProperties['theme_url']->addMessage('required', _MD_XUPDATE_ERROR_REQUIRED, _MD_XUPDATE_LANG_THEME_URL);
				$this->mFieldProperties['theme_url']->addMessage('maxlength', _MD_XUPDATE_ERROR_MAXLENGTH, _MD_XUPDATE_LANG_THEME_URL, '191');
				$this->mFieldProperties['theme_url']->addVar('maxlength', '191');
		*/
		$this->mFieldProperties['reg_unixtime'] = new XCube_FieldProperty( $this );
		$this->mFieldProperties['reg_unixtime']->setDependsByArray( [ 'required' ] );
		$this->mFieldProperties['reg_unixtime']->addMessage( 'required', _MD_XUPDATE_ERROR_REQUIRED, _MD_XUPDATE_LANG_REG_UNIXTIME );
	}

	/**
	 * load
	 *
	 * @param XoopsSimpleObject  &$obj
	 *
	 * @return  void
	 **/
	public function load( /*** XoopsSimpleObject ***/ &$obj ) {
		$this->set( 'sid', $obj->get( 'sid' ) );
// 	   $this->set('uid', $obj->get('uid'));
// 	   $this->set('valid', $obj->get('valid'));
		$this->set( 'name', $obj->get( 'name' ) );
		$this->set( 'contents', $obj->get( 'contents' ) );
		$this->set( 'addon_url', $obj->get( 'addon_url' ) );
// 	   $this->set('theme_url', $obj->get('theme_url'));
		$this->set( 'reg_unixtime', $obj->get( 'reg_unixtime' ) );
	}

	/**
	 * update
	 *
	 * @param XoopsSimpleObject  &$obj
	 *
	 * @return  void
	 **/
	public function update( /*** XoopsSimpleObject ***/ &$obj ) {
		$obj->set( 'sid', $this->get( 'sid' ) );
//		 $obj->set('uid', $this->get('uid'));
//		 $obj->set('valid', $this->get('valid'));
		$obj->set( 'name', $this->get( 'name' ) );
		$obj->set( 'addon_url', $this->get( 'addon_url' ) );
//		 $obj->set('theme_url', $this->get('theme_url'));
		$obj->set( 'reg_unixtime', $this->get( 'reg_unixtime' ) );
	}
}
