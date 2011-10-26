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

	var $mKeyword = "";
	var $mOptionField = "";
	
	function getDefaultSortKey()
	{
		return RANKS_SORT_KEY_DEFAULT;
	}

	function fetch()
	{
		parent::fetch();

		$root =& XCube_Root::getSingleton();
		$rank_special = $root->mContext->mRequest->getRequest('rank_special');
		$option_field = $root->mContext->mRequest->getRequest('option_field');
		$search = $root->mContext->mRequest->getRequest('search');	
		if (isset($_REQUEST['rank_special'])) {
			$this->mNavi->addExtra('rank_special', xoops_getrequest('rank_special'));
			$this->_mCriteria->add(new Criteria('rank_special', xoops_getrequest('rank_special')));
		}

		if (isset($_REQUEST['option_field'])) {
			$this->mNavi->addExtra('option_field', xoops_getrequest('option_field'));
			$this->mOptionField = $option_field;
			if ( $this->mOptionField == "special" ) {
			//only system avatar
			$this->_mCriteria->add(new Criteria('rank_special', '1'));
			}
			elseif ( $this->mOptionField == "normal" ) {
			//only custom avatar
			$this->_mCriteria->add(new Criteria('rank_special', '0'));
			}
			else {
			//all
			}
		}

		//
		if (!empty($search)) {
			$this->mKeyword = $search;
			$this->mNavi->addExtra('search', $this->mKeyword);
			$this->_mCriteria->add(new Criteria('rank_title', '%' . $this->mKeyword . '%', 'LIKE'));
		}
		
		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
	}
}

?>
