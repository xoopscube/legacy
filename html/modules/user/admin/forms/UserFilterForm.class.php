<?php
/**
 * @package user
 * @version $Id: UserFilterForm.class.php,v 1.2 2007/06/07 05:27:37 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractFilterForm.class.php";

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

/***
 * @internal
 * [Notice]
 * We should have our policy about filtering items.
 */
class User_UserFilterForm extends User_AbstractFilterForm
{
	var $mSortKeys = array(
		USER_SORT_KEY_UID => 'uid',
		USER_SORT_KEY_NAME => 'name',
		USER_SORT_KEY_UNAME => 'uname',
		USER_SORT_KEY_EMAIL => 'email',
		USER_SORT_KEY_URL => 'url',
		USER_SORT_KEY_USER_AVATAR => 'user_avatar',
		USER_SORT_KEY_USER_REGDATE => 'user_regdate',
		USER_SORT_KEY_USER_ICQ => 'user_icq',
		USER_SORT_KEY_USER_FROM => 'user_from',
		USER_SORT_KEY_USER_SIG => 'user_sig',
		USER_SORT_KEY_USER_VIEWEMAIL => 'user_viewemail',
		USER_SORT_KEY_ACTKEY => 'actkey',
		USER_SORT_KEY_USER_AIM => 'user_aim',
		USER_SORT_KEY_USER_YIM => 'user_yim',
		USER_SORT_KEY_USER_MSNM => 'user_msnm',
		USER_SORT_KEY_PASS => 'pass',
		USER_SORT_KEY_POSTS => 'posts',
		USER_SORT_KEY_ATTACHSIG => 'attachsig',
		USER_SORT_KEY_RANK => 'rank',
		USER_SORT_KEY_LEVEL => 'level',
		USER_SORT_KEY_THEME => 'theme',
		USER_SORT_KEY_TIMEZONE_OFFSET => 'timezone_offset',
		USER_SORT_KEY_LAST_LOGIN => 'last_login',
		USER_SORT_KEY_UMODE => 'umode',
		USER_SORT_KEY_UORDER => 'uorder',
		USER_SORT_KEY_NOTIFY_METHOD => 'notify_method',
		USER_SORT_KEY_NOTIFY_MODE => 'notify_mode',
		USER_SORT_KEY_USER_OCC => 'user_occ',
		USER_SORT_KEY_BIO => 'bio',
		USER_SORT_KEY_USER_INTREST => 'user_intrest',
		USER_SORT_KEY_USER_MAILOK => 'user_mailok'
	);

	var $mKeyword = "";
	var $mOptionField = "";

	function getDefaultSortKey()
	{
		return USER_SORT_KEY_DEFAULT;
	}
	
	function fetch()
	{
		parent::fetch();
	
		$root =& XCube_Root::getSingleton();
		$uid = $root->mContext->mRequest->getRequest('uid');
		$email = $root->mContext->mRequest->getRequest('email');
		$attachsig = $root->mContext->mRequest->getRequest('attachsig');
		$rank = $root->mContext->mRequest->getRequest('rank');
		$level = $root->mContext->mRequest->getRequest('level');
		$timezone_offset = $root->mContext->mRequest->getRequest('timezone_offset');
		$user_mailok = $root->mContext->mRequest->getRequest('user_mailok');
		$option_field = $root->mContext->mRequest->getRequest('option_field');
		$search = $root->mContext->mRequest->getRequest('search');

		if (isset($_REQUEST['uid'])) {
			$this->mNavi->addExtra('uid', xoops_getrequest('uid'));
			$this->_mCriteria->add(new Criteria('uid', xoops_getrequest('uid')));
		}

		if (isset($_REQUEST['email'])) {
			$this->mNavi->addExtra('email', xoops_getrequest('email'));
			$this->_mCriteria->add(new Criteria('email', xoops_getrequest('email')));
		}
	
		if (isset($_REQUEST['attachsig'])) {
			$this->mNavi->addExtra('attachsig', xoops_getrequest('attachsig'));
			$this->_mCriteria->add(new Criteria('attachsig', xoops_getrequest('attachsig')));
		}
	
		if (isset($_REQUEST['rank'])) {
			$this->mNavi->addExtra('rank', xoops_getrequest('rank'));
			$this->_mCriteria->add(new Criteria('rank', xoops_getrequest('rank')));
		}
	
		if (isset($_REQUEST['level'])) {
			$this->mNavi->addExtra('level', xoops_getrequest('level'));
			$this->_mCriteria->add(new Criteria('level', xoops_getrequest('level')));
		}
	
		if (isset($_REQUEST['timezone_offset'])) {
			$this->mNavi->addExtra('timezone_offset', xoops_getrequest('timezone_offset'));
			$this->_mCriteria->add(new Criteria('timezone_offset', xoops_getrequest('timezone_offset')));
		}
	
		if (isset($_REQUEST['user_mailok'])) {
			$this->mNavi->addExtra('user_mailok', xoops_getrequest('user_mailok'));
			$this->_mCriteria->add(new Criteria('user_mailok', xoops_getrequest('user_mailok')));
		}

		//wanikoo
		if (isset($_REQUEST['option_field'])) {
			$this->mNavi->addExtra('option_field', xoops_getrequest('option_field'));
			$this->mOptionField = $option_field;

			if ( $option_field == "inactive" ) {
			//only inactive users
			$this->_mCriteria->add(new Criteria('level', '0'));
			}
			elseif ( $option_field == "active" ) {
			//only active users
			$this->_mCriteria->add(new Criteria('level', '0', '>'));
			}
			else {
			//all
			}
		}
		
		//
		if (!empty($search)) {
			$this->mKeyword = $search;
			$this->mNavi->addExtra('search', $this->mKeyword);
			$this->_mCriteria->add(new Criteria('uname', '%' . $this->mKeyword . '%', 'LIKE'));
		}

		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
	}
}

?>
