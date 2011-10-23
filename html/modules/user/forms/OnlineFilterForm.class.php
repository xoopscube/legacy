<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractFilterForm.class.php";

define('ONLINE_SORT_KEY_ONLINE_UID', 1);
define('ONLINE_SORT_KEY_ONLINE_UNAME', 2);
define('ONLINE_SORT_KEY_ONLINE_UPDATED', 3);
define('ONLINE_SORT_KEY_ONLINE_MODULE', 4);
define('ONLINE_SORT_KEY_ONLINE_IP', 5);
define('ONLINE_SORT_KEY_MAXVALUE', 5);

define('ONLINE_SORT_KEY_DEFAULT', ONLINE_SORT_KEY_ONLINE_UID);

class User_OnlineFilterForm extends User_AbstractFilterForm
{
	var $mSortKeys = array(
		ONLINE_SORT_KEY_ONLINE_UID => 'online_uid',
		ONLINE_SORT_KEY_ONLINE_UNAME => 'online_uname',
		ONLINE_SORT_KEY_ONLINE_UPDATED => 'online_updated',
		ONLINE_SORT_KEY_ONLINE_MODULE => 'online_module',
		ONLINE_SORT_KEY_ONLINE_IP => 'online_ip'
	);
	
	function getDefaultSortKey()
	{
		return ONLINE_SORT_KEY_DEFAULT;
	}

	function fetch()
	{
		parent::fetch();

		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
	}
}

?>
