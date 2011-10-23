<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractFilterForm.class.php";

define('MAILJOB_SORT_KEY_MAILJOB_ID', 1);
define('MAILJOB_SORT_KEY_TITLE', 2);
define('MAILJOB_SORT_KEY_BODY', 3);
define('MAILJOB_SORT_KEY_FROM_NAME', 4);
define('MAILJOB_SORT_KEY_FROM_EMAIL', 5);
define('MAILJOB_SORT_KEY_IS_PM', 6);
define('MAILJOB_SORT_KEY_IS_MAIL', 7);
define('MAILJOB_SORT_KEY_CREATE_UNIXTIME', 8);
define('MAILJOB_SORT_KEY_MAXVALUE', 8);

define('MAILJOB_SORT_KEY_DEFAULT', -MAILJOB_SORT_KEY_CREATE_UNIXTIME);

class User_MailjobFilterForm extends User_AbstractFilterForm
{
	var $mSortKeys = array(
		MAILJOB_SORT_KEY_MAILJOB_ID => 'mailjob_id',
		MAILJOB_SORT_KEY_TITLE => 'title',
		MAILJOB_SORT_KEY_BODY => 'body',
		MAILJOB_SORT_KEY_FROM_NAME => 'from_name',
		MAILJOB_SORT_KEY_FROM_EMAIL => 'from_email',
		MAILJOB_SORT_KEY_IS_PM => 'is_pm',
		MAILJOB_SORT_KEY_IS_MAIL => 'is_mail',
		MAILJOB_SORT_KEY_CREATE_UNIXTIME => 'create_unixtime'
	);
	
	function getDefaultSortKey()
	{
		return MAILJOB_SORT_KEY_DEFAULT;
	}

	function fetch()
	{
		parent::fetch();
	
		if (isset($_REQUEST['title'])) {
			//
			// TODO like
			//
			$this->mNavi->addExtra('title', xoops_getrequest('title'));
			$this->_mCriteria->add(new Criteria('title', array(XOBJ_DTYPE_STRING, xoops_getrequest('title'))));
		}
	
		if (isset($_REQUEST['is_pm'])) {
			$this->mNavi->addExtra('is_pm', xoops_getrequest('is_pm'));
			$this->_mCriteria->add(new Criteria('is_pm', xoops_getrequest('is_pm')));
		}
	
		if (isset($_REQUEST['is_mail'])) {
			$this->mNavi->addExtra('is_mail', xoops_getrequest('is_mail'));
			$this->_mCriteria->add(new Criteria('is_mail', xoops_getrequest('is_mail')));
		}
		
		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
	}
}

?>
