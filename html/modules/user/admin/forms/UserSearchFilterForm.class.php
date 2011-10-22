<?php
/**
 * @package user
 * @version $Id: UserSearchFilterForm.class.php,v 1.3 2007/09/08 01:09:39 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractFilterForm.class.php";
require_once XOOPS_MODULE_PATH . "/user/admin/forms/UserSearchForm.class.php";

define('USER_SORT_KEY_UID', 1);
define('USER_SORT_KEY_NAME', 2);
define('USER_SORT_KEY_UNAME', 3);
define('USER_SORT_KEY_EMAIL', 4);
define('USER_SORT_KEY_URL', 5);
define('USER_SORT_KEY_USER_AVATAR', 6);
define('USER_SORT_KEY_USER_REGDATE', 7);
define('USER_SORT_KEY_USER_ICQ', 8);
define('USER_SORT_KEY_USER_FROM', 9);
define('USER_SORT_KEY_USER_SIG', 10);
define('USER_SORT_KEY_USER_VIEWEMAIL', 11);
define('USER_SORT_KEY_ACTKEY', 12);
define('USER_SORT_KEY_USER_AIM', 13);
define('USER_SORT_KEY_USER_YIM', 14);
define('USER_SORT_KEY_USER_MSNM', 15);
define('USER_SORT_KEY_PASS', 16);
define('USER_SORT_KEY_POSTS', 17);
define('USER_SORT_KEY_ATTACHSIG', 18);
define('USER_SORT_KEY_RANK', 19);
define('USER_SORT_KEY_LEVEL', 20);
define('USER_SORT_KEY_THEME', 21);
define('USER_SORT_KEY_TIMEZONE_OFFSET', 22);
define('USER_SORT_KEY_LAST_LOGIN', 23);
define('USER_SORT_KEY_UMODE', 24);
define('USER_SORT_KEY_UORDER', 25);
define('USER_SORT_KEY_NOTIFY_METHOD', 26);
define('USER_SORT_KEY_NOTIFY_MODE', 27);
define('USER_SORT_KEY_USER_OCC', 28);
define('USER_SORT_KEY_BIO', 29);
define('USER_SORT_KEY_USER_INTREST', 30);
define('USER_SORT_KEY_USER_MAILOK', 31);
define('USER_SORT_KEY_MAXVALUE', 31);

define('USER_SORT_KEY_DEFAULT', USER_SORT_KEY_UID);

class User_UserSearchFilterForm extends User_AbstractFilterForm
{
	var $mSortKeys = array(
		USER_SORT_KEY_UID => 'u.uid',
		USER_SORT_KEY_NAME => 'u.name',
		USER_SORT_KEY_UNAME => 'u.uname',
		USER_SORT_KEY_EMAIL => 'u.email',
		USER_SORT_KEY_URL => 'u.url',
		USER_SORT_KEY_USER_AVATAR => 'u.user_avatar',
		USER_SORT_KEY_USER_REGDATE => 'u.user_regdate',
		USER_SORT_KEY_USER_ICQ => 'u.user_icq',
		USER_SORT_KEY_USER_FROM => 'u.user_from',
		USER_SORT_KEY_USER_SIG => 'u.user_sig',
		USER_SORT_KEY_USER_VIEWEMAIL => 'u.user_viewemail',
		USER_SORT_KEY_ACTKEY => 'u.actkey',
		USER_SORT_KEY_USER_AIM => 'u.user_aim',
		USER_SORT_KEY_USER_YIM => 'u.user_yim',
		USER_SORT_KEY_USER_MSNM => 'u.user_msnm',
		USER_SORT_KEY_PASS => 'u.pass',
		USER_SORT_KEY_POSTS => 'u.posts',
		USER_SORT_KEY_ATTACHSIG => 'u.attachsig',
		USER_SORT_KEY_RANK => 'u.rank',
		USER_SORT_KEY_LEVEL => 'u.level',
		USER_SORT_KEY_THEME => 'u.theme',
		USER_SORT_KEY_TIMEZONE_OFFSET => 'u.timezone_offset',
		USER_SORT_KEY_LAST_LOGIN => 'u.last_login',
		USER_SORT_KEY_UMODE => 'u.umode',
		USER_SORT_KEY_UORDER => 'u.uorder',
		USER_SORT_KEY_NOTIFY_METHOD => 'u.notify_method',
		USER_SORT_KEY_NOTIFY_MODE => 'u.notify_mode',
		USER_SORT_KEY_USER_OCC => 'u.user_occ',
		USER_SORT_KEY_BIO => 'u.bio',
		USER_SORT_KEY_USER_INTREST => 'u.user_intrest',
		USER_SORT_KEY_USER_MAILOK => 'u.user_mailok'
	);

	var $_mMatchFields = array ("uname", "name", "email", "user_icq", "user_aim", "user_yim", "user_msnm",
	                            "url", "user_from", "user_occ", "user_intrest");
	
	function getDefaultSortKey()	
	{
		return USER_SORT_KEY_DEFAULT;
	}
								
	function fetch()
	{
		parent::fetch();

		$form =new User_UserSearchForm();
		$form->prepare();

		$form->fetch();
		$form->validate();

		if ($form->hasError()) {
			return;
		}

		$root =& XCube_Root::getSingleton();

		foreach ($this->_mMatchFields as $field) {
			if (strlen($form->get($field)) > 0) {
				$this->mNavi->addExtra($field, $form->get($field));
				
				$user_field_match = $root->mContext->mRequest->getRequest('user_'.$field.'_match');
				$field_match = $root->mContext->mRequest->getRequest($field.'_match');
				if( isset($user_field_match) || isset($field_match) ){
					$formvalue =  0;
					if (isset($user_field_match)){
					$formvalue = intval($form->get('user_'.$field.'_match')) ;
					$this->mNavi->addExtra('user_'.$field.'_match', $formvalue);
					}
					elseif(isset($field_match)){
					$formvalue = intval($form->get($field.'_match')) ;
					$this->mNavi->addExtra($field.'_match', $formvalue);
					}
					switch ($formvalue) {
					case XOOPS_MATCH_START:
					$this->_mCriteria->add(new Criteria('u.' . $field, $form->get($field) . '%', 'LIKE'));
					break;
					case XOOPS_MATCH_END:
					$this->_mCriteria->add(new Criteria('u.' . $field, '%' . $form->get($field), 'LIKE'));
					break;
					case XOOPS_MATCH_EQUAL:
					$this->_mCriteria->add(new Criteria('u.' . $field, $form->get($field)));
					break;
					case XOOPS_MATCH_CONTAIN:
					$this->_mCriteria->add(new Criteria('u.' . $field, '%' . $form->get($field) . '%', 'LIKE'));
					break;
					}	
				}
				else {
				$this->_mCriteria->add(new Criteria('u.' . $field, '%' . $form->get($field) . '%', 'LIKE'));
				}
			}
		}
		
		$this->mNavi->addExtra('mail_condition', $form->get('mail_condition'));
		switch ($form->get('mail_condition')) {
			case 1:
				$this->_mCriteria->add(new Criteria('u.user_mailok', 1));
				break;
				
			case 2:
				$this->_mCriteria->add(new Criteria('u.user_mailok', 0));
				break;
		}

		$this->mNavi->addExtra('user_level', $form->get('user_level'));
		switch ($form->get('user_level')) {
			case 1:
				$this->_mCriteria->add(new Criteria('u.level', 0, '>'));
				break;
				
			case 2:
				$this->_mCriteria->add(new Criteria('u.level', 0));
				break;
		}
		
		if (strlen($form->get('over_posts')) > 0) {
			$this->mNavi->addExtra('over_posts', $form->get('over_posts'));
			$this->_mCriteria->add(new Criteria('u.posts', $form->get('over_posts'), '>='));
		}

		if (strlen($form->get('under_posts')) > 0) {
			$this->mNavi->addExtra('under_posts', $form->get('under_posts'));
			$this->_mCriteria->add(new Criteria('u.posts', $form->get('under_posts'), '<='));
		}

		if (strlen($form->get('lastlog_more')) > 0) {
			$this->mNavi->addExtra('lastlog_more', $form->get('lastlog_more'));
			$time = time() - $form->get('lastlog_more') * 86400;
			$this->_mCriteria->add(new Criteria('u.last_login', $time, '<='));
		}

		if (strlen($form->get('lastlog_less')) > 0) {
			$this->mNavi->addExtra('lastlog_less', $form->get('lastlog_less'));
			$time = time() - $form->get('lastlog_less') * 86400;
			$this->_mCriteria->add(new Criteria('u.last_login', $time, '>='));
		}

		if (strlen($form->get('regdate_more')) > 0) {
			$this->mNavi->addExtra('regdate_more', $form->get('regdate_more'));
			$time = time() - $form->get('regdate_more') * 86400;
			$this->_mCriteria->add(new Criteria('u.user_regdate', $time, '<='));
		}

		if (strlen($form->get('regdate_less')) > 0) {
			$this->mNavi->addExtra('regdate_less', $form->get('regdate_less'));
			$time = time() - $form->get('regdate_less') * 86400;
			$this->_mCriteria->add(new Criteria('u.user_regdate', $time, '>='));
		}
		
		$groups = $form->get('groups');
		if (count($groups) > 0) {
			$g_criteria =new CriteriaCompo();
			foreach($groups as $gid) {
				$g_criteria->add(new Criteria('g.groupid', $gid), $condition='OR');
				$this->mNavi->addExtra('groups[' . $gid . ']', $gid);
			}
			$this->_mCriteria->add($g_criteria);
		}
		
		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
	}
}

?>
