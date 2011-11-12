<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/forms/AbstractUserEditForm.class.php";

/***
 * @internal
 */
class User_EditUserForm extends User_AbstractUserEditForm 
{
	function getTokenName()
	{
		return "Module.User.EditUserForm.Token." . $this->get('uid');
	}
	
	/**
	 * TODO The argument of this member property may be moved to constructor.
	 */
	function prepare()
	{
		parent::prepare();
		
		//
		// set properties
		//
		$this->mFormProperties['uid'] =new XCube_IntProperty('uid');
		$this->mFormProperties['name'] =new XCube_StringProperty('name');
		
		if ($this->mConfig['allow_chgmail']) {
			$this->mFormProperties['email'] =new XCube_StringProperty('email');
		}

		$this->mFormProperties['user_viewemail'] =new XCube_BoolProperty('user_viewemail');
		$this->mFormProperties['url'] =new XCube_StringProperty('url');
		$this->mFormProperties['user_icq'] =new XCube_StringProperty('user_icq');
		$this->mFormProperties['user_aim'] =new XCube_StringProperty('user_aim');
		$this->mFormProperties['user_yim'] =new XCube_StringProperty('user_yim');
		$this->mFormProperties['user_msnm'] =new XCube_StringProperty('user_msnm');
		$this->mFormProperties['user_from'] =new XCube_StringProperty('user_from');
		$this->mFormProperties['timezone_offset'] =new XCube_FloatProperty('timezone_offset');
		$this->mFormProperties['umode'] =new XCube_StringProperty('umode');
		$this->mFormProperties['uorder'] =new XCube_IntProperty('uorder');
		$this->mFormProperties['notify_method'] =new XCube_IntProperty('notify_method');
		$this->mFormProperties['notify_mode'] =new XCube_IntProperty('notify_mode');
		$this->mFormProperties['user_occ'] =new XCube_StringProperty('user_occ');
		$this->mFormProperties['user_intrest'] =new XCube_StringProperty('user_intrest');
		$this->mFormProperties['user_sig'] =new XCube_TextProperty('user_sig');
		$this->mFormProperties['attachsig'] =new XCube_BoolProperty('attachsig');
		$this->mFormProperties['bio'] =new XCube_TextProperty('bio');
		$this->mFormProperties['pass'] =new XCube_StringProperty('pass');
		$this->mFormProperties['vpass'] =new XCube_StringProperty('vpass');
		$this->mFormProperties['usercookie'] =new XCube_BoolProperty('usercookie');
		$this->mFormProperties['user_mailok'] =new XCube_BoolProperty('user_mailok');

		//
		// set fields
		//
		$this->mFieldProperties['name'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['name']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['name']->addMessage("maxlength", _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_NAME, "60");
		$this->mFieldProperties['name']->addVar("maxlength", 60);

		if ($this->mConfig['allow_chgmail']) {
			$this->mFieldProperties['email'] =new XCube_FieldProperty($this);
			$this->mFieldProperties['email']->setDependsByArray(array('required', 'maxlength', 'email'));
			$this->mFieldProperties['email']->addMessage("required", _MD_USER_ERROR_REQUIRED, _MD_USER_LANG_EMAIL, "60");
			$this->mFieldProperties['email']->addMessage("maxlength", _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_EMAIL, "60");
			$this->mFieldProperties['email']->addVar("maxlength", 60);
			$this->mFieldProperties['email']->addMessage('email', _MD_USER_ERROR_EMAIL, _MD_USER_LANG_EMAIL);
		}

		$this->mFieldProperties['url'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['url']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['url']->addMessage("maxlength", _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_WEBSITE, "100");
		$this->mFieldProperties['url']->addVar("maxlength", 100);

		$this->mFieldProperties['user_icq'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['user_icq']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['user_icq']->addMessage("maxlength", _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_USER_ICQ, "15");
		$this->mFieldProperties['user_icq']->addVar("maxlength", 15);

		$this->mFieldProperties['user_from'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['user_from']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['user_from']->addMessage("maxlength", _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_USER_FROM, "100");
		$this->mFieldProperties['user_from']->addVar("maxlength", 100);

		$this->mFieldProperties['user_aim'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['user_aim']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['user_aim']->addMessage("maxlength", _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_USER_AIM, "18");
		$this->mFieldProperties['user_aim']->addVar("maxlength", 18);

		$this->mFieldProperties['user_msnm'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['user_msnm']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['user_msnm']->addMessage("maxlength", _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_USER_MSNM, "100");
		$this->mFieldProperties['user_msnm']->addVar("maxlength", 100);
		
		$this->mFieldProperties['pass'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['pass']->setDependsByArray(array('minlength', 'maxlength'));
		$this->mFieldProperties['pass']->addMessage('minlength', _MD_USER_ERROR_MINLENGTH, _MD_USER_LANG_PASS, $this->mConfig['minpass']);
		$this->mFieldProperties['pass']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_PASS, '32');
		$this->mFieldProperties['pass']->addVar('minlength', $this->mConfig['minpass']);
		$this->mFieldProperties['pass']->addVar('maxlength', 32);

		$this->mFieldProperties['vpass'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['vpass']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['vpass']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_VERIFYPASS, '32');
		$this->mFieldProperties['vpass']->addVar('maxlength', 32);
		
		$this->mFieldProperties['timezone_offset'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['timezone_offset']->setDependsByArray(array('required'));
		$this->mFieldProperties['timezone_offset']->addMessage('required', _MD_USER_ERROR_REQUIRED, _MD_USER_LANG_TIMEZONE_OFFSET);

		$this->mFieldProperties['umode'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['umode']->setDependsByArray(array('required'));
		$this->mFieldProperties['umode']->addMessage('required', _MD_USER_ERROR_REQUIRED, _MD_USER_LANG_UMODE);

		$this->mFieldProperties['uorder'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['uorder']->setDependsByArray(array('required','intRange'));
		$this->mFieldProperties['uorder']->addMessage('required', _MD_USER_ERROR_REQUIRED, _MD_USER_LANG_UORDER);
		$this->mFieldProperties['uorder']->addMessage('intRange', _MD_USER_ERROR_INJURY, _MD_USER_LANG_UORDER);
		$this->mFieldProperties['uorder']->addVar('min', 0);
		$this->mFieldProperties['uorder']->addVar('max', 1);

		$this->mFieldProperties['notify_method'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['notify_method']->setDependsByArray(array('required','intRange'));
		$this->mFieldProperties['notify_method']->addMessage('required', _MD_USER_ERROR_REQUIRED, _MD_USER_LANG_NOTIFY_METHOD);
		$this->mFieldProperties['notify_method']->addMessage('intRange', _MD_USER_ERROR_INJURY, _MD_USER_LANG_NOTIFY_METHOD);
		$this->mFieldProperties['notify_method']->addVar('min', 0);
		$this->mFieldProperties['notify_method']->addVar('max', 2);

		$this->mFieldProperties['notify_mode'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['notify_mode']->setDependsByArray(array('required','intRange'));
		$this->mFieldProperties['notify_mode']->addMessage('required', _MD_USER_ERROR_REQUIRED, _MD_USER_LANG_NOTIFY_MODE);
		$this->mFieldProperties['notify_mode']->addMessage('intRange', _MD_USER_ERROR_INJURY, _MD_USER_LANG_NOTIFY_MODE);
		$this->mFieldProperties['notify_mode']->addVar('min', 0);
		$this->mFieldProperties['notify_mode']->addVar('max', 2);
		
		$this->mFieldProperties['user_occ'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['user_occ']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['user_occ']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_USER_OCC, '100');
		$this->mFieldProperties['user_occ']->addVar('maxlength', 100);

		$this->mFieldProperties['user_intrest'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['user_intrest']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['user_intrest']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_USER_INTREST, '150');
		$this->mFieldProperties['user_intrest']->addVar('maxlength', 150);
		
		$this->mFieldProperties['bio'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['bio']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['bio']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_BIO, '250');
		$this->mFieldProperties['bio']->addVar('maxlength', 250);
	
		XCube_DelegateUtils::call('Legacy_Profile.SetupActionForm', $this);
	}
	
	function load(&$obj)
	{
		$this->set('uid', $obj->get('uid'));
		$this->set('name', $obj->get('name'));
		
		if ($this->mConfig['allow_chgmail']) {
			$this->set('email', $obj->get('email'));
		}

		$this->set('url', $obj->get('url'));
		$this->set('user_icq', $obj->get('user_icq'));
		$this->set('user_from', $obj->get('user_from'));
		$this->set('user_sig', $obj->get('user_sig'));
		$this->set('user_viewemail', $obj->get('user_viewemail'));
		$this->set('user_aim', $obj->get('user_aim'));
		$this->set('user_yim', $obj->get('user_yim'));
		$this->set('user_msnm', $obj->get('user_msnm'));

		$this->set('attachsig', $obj->get('attachsig'));
		$this->set('timezone_offset', $obj->get('timezone_offset'));
		$this->set('umode', $obj->get('umode'));
		$this->set('uorder', $obj->get('uorder'));
		$this->set('notify_method', $obj->get('notify_method'));
		$this->set('notify_mode', $obj->get('notify_mode'));
		$this->set('user_occ', $obj->get('user_occ'));
		$this->set('bio', $obj->get('bio'));
		$this->set('user_intrest', $obj->get('user_intrest'));
		$this->set('user_mailok', $obj->get('user_mailok'));
		
		$this->set('pass', null);
		$this->set('vpass', null);
		
		$root =& XCube_Root::getSingleton();
		$this->set('usercookie', empty($_COOKIE[$this->mConfig['usercookie']]) ? 0 : 1);
	
		XCube_DelegateUtils::call('Legacy_Profile.LoadActionForm', $this);
	}
	
	function update(&$obj)
	{
		$obj->set('name', $this->get('name'));
		
		if ($this->mConfig['allow_chgmail']) {
			$obj->set('email', $this->get('email'));
		}

		$obj->set('url', $this->get('url'));
		$obj->set('user_icq', $this->get('user_icq'));
		$obj->set('user_from', $this->get('user_from'));
		$obj->set('user_sig', $this->get('user_sig'));
		$obj->set('user_viewemail', $this->get('user_viewemail'));
		$obj->set('user_aim', $this->get('user_aim'));
		$obj->set('user_yim', $this->get('user_yim'));
		$obj->set('user_msnm', $this->get('user_msnm'));

		if (strlen($this->get('pass'))) {
			$obj->set('pass', md5($this->get('pass')));
		}

		$obj->set('attachsig', $this->get('attachsig'));
		$obj->set('timezone_offset', $this->get('timezone_offset'));
		$obj->set('umode', $this->get('umode'));
		$obj->set('uorder', $this->get('uorder'));
		$obj->set('notify_method', $this->get('notify_method'));
		$obj->set('notify_mode', $this->get('notify_mode'));
		$obj->set('user_occ', $this->get('user_occ'));
		$obj->set('bio', $this->get('bio'));
		$obj->set('user_intrest', $this->get('user_intrest'));
		$obj->set('user_mailok', $this->get('user_mailok'));
	}
}

?>
