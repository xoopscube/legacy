<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractFilterForm.class.php";

define('MAILJOB_LINK_SORT_KEY_MAILJOB_ID', 1);
define('MAILJOB_LINK_SORT_KEY_UID', 2);
define('MAILJOB_LINK_SORT_KEY_RETRY', 3);
define('MAILJOB_LINK_SORT_KEY_MESSAGE', 4);

define('MAILJOB_LINK_SORT_KEY_DEFAULT', MAILJOB_LINK_SORT_KEY_MAILJOB_ID);
define('MAILJOB_LINK_SORT_KEY_MAXVALUE', 4);

class User_Mailjob_linkFilterForm extends User_AbstractFilterForm
{
	var $mSortKeys = array(
		MAILJOB_LINK_SORT_KEY_DEFAULT => 'mailjob_id',
		MAILJOB_LINK_SORT_KEY_DEFAULT => 'uid',
		MAILJOB_LINK_SORT_KEY_MAILJOB_ID => 'mailjob_id',
		MAILJOB_LINK_SORT_KEY_UID => 'uid',
		MAILJOB_LINK_SORT_KEY_RETRY => 'retry',
		MAILJOB_LINK_SORT_KEY_MESSAGE => 'message'
	);
	
	function getDefaultSortKey()
	{
		return MAILJOB_LINK_SORT_KEY_DEFAULT;
	}

	function fetch()
	{
		parent::fetch();

		if (isset($_REQUEST['mailjob_id'])) {
			$this->mNavi->addExtra('mailjob_id', xoops_getrequest('mailjob_id'));
			$this->_mCriteria->add(new Criteria('mailjob_id', xoops_getrequest('mailjob_id')));
		}
	
		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
	}
}

?>
