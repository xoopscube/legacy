<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacyRender/class/AbstractFilterForm.class.php";

define('TPLSET_SORT_KEY_TPLSET_ID', 1);
define('TPLSET_SORT_KEY_TPLSET_NAME', 2);
define('TPLSET_SORT_KEY_TPLSET_DESC', 3);
define('TPLSET_SORT_KEY_TPLSET_CREDITS', 4);
define('TPLSET_SORT_KEY_TPLSET_CREATED', 5);

define('TPLSET_SORT_KEY_DEFAULT', TPLSET_SORT_KEY_TPLSET_ID);
define('TPLSET_SORT_KEY_MAXVALUE', 5);

class LegacyRender_TplsetFilterForm extends LegacyRender_AbstractFilterForm
{
	var $mSortKeys = array(
		TPLSET_SORT_KEY_DEFAULT => 'tplset_id',
		TPLSET_SORT_KEY_TPLSET_ID => 'tplset_id',
		TPLSET_SORT_KEY_TPLSET_NAME => 'tplset_name',
		TPLSET_SORT_KEY_TPLSET_DESC => 'tplset_desc',
		TPLSET_SORT_KEY_TPLSET_CREDITS => 'tplset_credits',
		TPLSET_SORT_KEY_TPLSET_CREATED => 'tplset_created'
	);
	
	function getDefaultSortKey()
	{
		return TPLSET_SORT_KEY_DEFAULT;
	}

	function fetch()
	{
		parent::fetch();
		
		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
	}
}

?>
