<?php
/**
 * @package legacyRender
 * @version $Id: RanksFilterForm.class.php,v 1.1 2007/05/15 02:34:39 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/user/class/AbstractFilterForm.class.php";

define('RANKS_SORT_KEY_RANK_ID', 1);
define('RANKS_SORT_KEY_RANK_TITLE', 2);
define('RANKS_SORT_KEY_RANK_MIN', 3);
define('RANKS_SORT_KEY_RANK_MAX', 4);
define('RANKS_SORT_KEY_RANK_SPECIAL', 5);
define('RANKS_SORT_KEY_MAXVALUE', 5);

define('RANKS_SORT_KEY_DEFAULT', RANKS_SORT_KEY_RANK_ID);

class User_RanksFilterForm extends User_AbstractFilterForm
{
	var $mSortKeys = array(
		RANKS_SORT_KEY_RANK_ID => 'rank_id',
		RANKS_SORT_KEY_RANK_TITLE => 'rank_title',
		RANKS_SORT_KEY_RANK_MIN => 'rank_min',
		RANKS_SORT_KEY_RANK_MAX => 'rank_max',
		RANKS_SORT_KEY_RANK_SPECIAL => 'rank_special'
	);
	
	function getDefaultSortKey()
	{
		return RANKS_SORT_KEY_DEFAULT;
	}

	function fetch()
	{
		parent::fetch();
	
		if (isset($_REQUEST['rank_special'])) {
			$this->mNavi->addExtra('rank_special', xoops_getrequest('rank_special'));
			$this->_mCriteria->add(new Criteria('rank_special', xoops_getrequest('rank_special')));
		}
		
		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
	}
}

?>
