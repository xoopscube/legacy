<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . "/core/XCube_ActionForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/class/Legacy_Validator.class.php";

class User_UserSearchForm extends XCube_ActionForm
{
	function prepare()
	{
		//
		// Set form properties
		//
		$this->mFormProperties['uname'] =new XCube_StringProperty('uname');
		$this->mFormProperties['name'] =new XCube_StringProperty('name');
		$this->mFormProperties['email'] =new XCube_StringProperty('email');
		$this->mFormProperties['user_icq'] =new XCube_StringProperty('user_icq');
		$this->mFormProperties['user_aim'] =new XCube_StringProperty('user_aim');
		$this->mFormProperties['user_yim'] =new XCube_StringProperty('user_yim');
		$this->mFormProperties['user_msnm'] =new XCube_StringProperty('user_msnm');
		$this->mFormProperties['url'] =new XCube_StringProperty('url');
		$this->mFormProperties['user_from'] =new XCube_StringProperty('user_from');
		$this->mFormProperties['user_occ'] =new XCube_StringProperty('user_occ');
		$this->mFormProperties['user_intrest'] =new XCube_StringProperty('user_intrest');
		$this->mFormProperties['lastlog_more'] =new XCube_IntProperty('lastlog_more');
		$this->mFormProperties['lastlog_less'] =new XCube_IntProperty('lastlog_less');
		$this->mFormProperties['regdate_more'] =new XCube_IntProperty('regdate_more');
		$this->mFormProperties['regdate_less'] =new XCube_IntProperty('regdate_less');
		$this->mFormProperties['over_posts'] =new XCube_IntProperty('over_posts');
		$this->mFormProperties['under_posts'] =new XCube_IntProperty('under_posts');
		$this->mFormProperties['mail_condition'] =new XCube_IntProperty('mail_condition');
		$this->mFormProperties['user_level'] =new XCube_IntProperty('user_level');
		$this->mFormProperties['groups'] =new XCube_IntArrayProperty('groups');
		//
		$this->mFormProperties['user_uname_match'] =new XCube_IntProperty('user_uname_match');
		$this->mFormProperties['user_name_match'] =new XCube_IntProperty('user_name_match');
		$this->mFormProperties['user_email_match'] =new XCube_IntProperty('user_email_match');
		$this->mFormProperties['user_icq_match'] =new XCube_IntProperty('user_icq_match');
		$this->mFormProperties['user_aim_match'] =new XCube_IntProperty('user_aim_match');
		$this->mFormProperties['user_yim_match'] =new XCube_IntProperty('user_yim_match');
		$this->mFormProperties['user_msnm_match'] =new XCube_IntProperty('user_msnm_match');

		//
		// Set field properties
		//
		$this->mFieldProperties['uname'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['uname']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['uname']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_UNAME, '25');
		$this->mFieldProperties['uname']->addVar('maxlength', '25');
	
		$this->mFieldProperties['name'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['name']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['name']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_NAME, '60');
		$this->mFieldProperties['name']->addVar('maxlength', '60');
	
		$this->mFieldProperties['email'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['email']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['email']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_EMAIL, '60');
		$this->mFieldProperties['email']->addVar('maxlength', '60');
	
		$this->mFieldProperties['user_icq'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['user_icq']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['user_icq']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_USER_ICQ, '15');
		$this->mFieldProperties['user_icq']->addVar('maxlength', '15');
	
		$this->mFieldProperties['user_aim'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['user_aim']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['user_aim']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_USER_AIM, '18');
		$this->mFieldProperties['user_aim']->addVar('maxlength', '18');
	
		$this->mFieldProperties['user_yim'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['user_yim']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['user_yim']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_USER_YIM, '25');
		$this->mFieldProperties['user_yim']->addVar('maxlength', '25');
	
		$this->mFieldProperties['user_msnm'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['user_msnm']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['user_msnm']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_USER_MSNM, '100');
		$this->mFieldProperties['user_msnm']->addVar('maxlength', '100');
	
		$this->mFieldProperties['url'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['url']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['url']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_URL, '100');
		$this->mFieldProperties['url']->addVar('maxlength', '100');
	
		$this->mFieldProperties['user_from'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['user_from']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['user_from']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_USER_FROM, '100');
		$this->mFieldProperties['user_from']->addVar('maxlength', '100');
	
		$this->mFieldProperties['user_occ'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['user_occ']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['user_occ']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_USER_OCC, '100');
		$this->mFieldProperties['user_occ']->addVar('maxlength', '100');
	
		$this->mFieldProperties['user_intrest'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['user_intrest']->setDependsByArray(array('maxlength'));
		$this->mFieldProperties['user_intrest']->addMessage('maxlength', _MD_USER_ERROR_MAXLENGTH, _MD_USER_LANG_USER_INTREST, '150');
		$this->mFieldProperties['user_intrest']->addVar('maxlength', '150');
	
		$this->mFieldProperties['lastlog_more'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['lastlog_more']->setDependsByArray(array('intRange'));
		$this->mFieldProperties['lastlog_more']->addMessage('intRange', _AD_USER_ERROR_INTRANGE, _AD_USER_LANG_LASTLOG_MORE, '0');
		$this->mFieldProperties['lastlog_more']->addVar('min', '0');
		$this->mFieldProperties['lastlog_more']->addVar('max', '65535');
	
		$this->mFieldProperties['lastlog_less'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['lastlog_less']->setDependsByArray(array('intRange'));
		$this->mFieldProperties['lastlog_less']->addMessage('intRange', _AD_USER_ERROR_INTRANGE, _AD_USER_LANG_LASTLOG_LESS, '0');
		$this->mFieldProperties['lastlog_less']->addVar('min', '0');
		$this->mFieldProperties['lastlog_less']->addVar('max', '65535');
	
		$this->mFieldProperties['regdate_more'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['regdate_more']->setDependsByArray(array('intRange'));
		$this->mFieldProperties['regdate_more']->addMessage('intRange', _AD_USER_ERROR_INTRANGE, _AD_USER_LANG_REGDATE_MORE, '0');
		$this->mFieldProperties['regdate_more']->addVar('min', '0');
		$this->mFieldProperties['regdate_more']->addVar('max', '65535');
	
		$this->mFieldProperties['regdate_less'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['regdate_less']->setDependsByArray(array('intRange'));
		$this->mFieldProperties['regdate_less']->addMessage('intRange', _AD_USER_ERROR_INTRANGE, _AD_USER_LANG_REGDATE_LESS, '0');
		$this->mFieldProperties['regdate_less']->addVar('min', '0');
		$this->mFieldProperties['regdate_less']->addVar('max', '65535');
	
		$this->mFieldProperties['over_posts'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['over_posts']->setDependsByArray(array('intRange'));
		$this->mFieldProperties['over_posts']->addMessage('intRange', _AD_USER_ERROR_INTRANGE, _AD_USER_LANG_OVER_POSTS, '0');
		$this->mFieldProperties['over_posts']->addVar('min', '0');
		$this->mFieldProperties['over_posts']->addVar('max', '65535');
	
		$this->mFieldProperties['under_posts'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['under_posts']->setDependsByArray(array('intRange'));
		$this->mFieldProperties['under_posts']->addMessage('intRange', _AD_USER_ERROR_INTRANGE, _AD_USER_LANG_UNDER_POSTS, '0');
		$this->mFieldProperties['under_posts']->addVar('min', '0');
		$this->mFieldProperties['under_posts']->addVar('max', '65535');
	
		$this->mFieldProperties['mail_condition'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['mail_condition']->setDependsByArray(array('required','intRange'));
		$this->mFieldProperties['mail_condition']->addMessage('required', _MD_USER_ERROR_REQUIRED, _AD_USER_LANG_DISPLAY_USER_MAIL_CONDITION);
		$this->mFieldProperties['mail_condition']->addMessage('intRange', _AD_USER_ERROR_INTRANGE, _AD_USER_LANG_DISPLAY_USER_MAIL_CONDITION);
		$this->mFieldProperties['mail_condition']->addVar('min', '1');
		$this->mFieldProperties['mail_condition']->addVar('max', '3');
	
		$this->mFieldProperties['user_level'] =new XCube_FieldProperty($this);
		$this->mFieldProperties['user_level']->setDependsByArray(array('required','intRange'));
		$this->mFieldProperties['user_level']->addMessage('required', _MD_USER_ERROR_REQUIRED, _AD_USER_LANG_DISPLAY_USER_LEVEL);
		$this->mFieldProperties['user_level']->addMessage('intRange', _AD_USER_ERROR_INTRANGE, _AD_USER_LANG_DISPLAY_USER_LEVEL);
		$this->mFieldProperties['user_level']->addVar('min', '1');
		$this->mFieldProperties['user_level']->addVar('max', '3');
	}
	
	/**
	 * FIXME: We may implement this member function as the objectExist
	 * validator.
	 */
	function validateGroups()
	{
		$groupHandler =& xoops_gethandler('group');
		foreach ($this->get('groups') as $gid) {
			$group =& $groupHandler->get($gid);
			if (!is_object($group)) {
				$this->addErrorMessage(_AD_USER_ERROR_GROUP_VALUE);
			}
		}
	}
}

?>
