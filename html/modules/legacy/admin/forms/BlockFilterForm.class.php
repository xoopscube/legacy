<?php
/**
 *
 * @package Legacy
 * @version $Id: BlockFilterForm.class.php,v 1.3 2008/09/25 15:10:53 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/>
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractFilterForm.class.php";

define('NEWBLOCKS_SORT_KEY_BID', 1);
define('NEWBLOCKS_SORT_KEY_MID', 2);
define('NEWBLOCKS_SORT_KEY_FUNC_NUM', 3);
define('NEWBLOCKS_SORT_KEY_OPTIONS', 4);
define('NEWBLOCKS_SORT_KEY_NAME', 5);
define('NEWBLOCKS_SORT_KEY_TITLE', 6);
define('NEWBLOCKS_SORT_KEY_CONTENT', 7);
define('NEWBLOCKS_SORT_KEY_SIDE', 8);
define('NEWBLOCKS_SORT_KEY_WEIGHT', 9);
define('NEWBLOCKS_SORT_KEY_VISIBLE', 10);
define('NEWBLOCKS_SORT_KEY_BLOCK_TYPE', 11);
define('NEWBLOCKS_SORT_KEY_C_TYPE', 12);
define('NEWBLOCKS_SORT_KEY_ISACTIVE', 13);
define('NEWBLOCKS_SORT_KEY_DIRNAME', 14);
define('NEWBLOCKS_SORT_KEY_FUNC_FILE', 15);
define('NEWBLOCKS_SORT_KEY_SHOW_FUNC', 16);
define('NEWBLOCKS_SORT_KEY_EDIT_FUNC', 17);
define('NEWBLOCKS_SORT_KEY_TEMPLATE', 18);
define('NEWBLOCKS_SORT_KEY_BCACHETIME', 19);
define('NEWBLOCKS_SORT_KEY_LAST_MODIFIED', 20);

define('NEWBLOCKS_SORT_KEY_DEFAULT', NEWBLOCKS_SORT_KEY_SIDE);
define('NEWBLOCKS_SORT_KEY_MAXVALUE', 20);

class Legacy_BlockFilterForm extends Legacy_AbstractFilterForm
{
	var $mSortKeys = array(
		NEWBLOCKS_SORT_KEY_BID => 'bid',
		NEWBLOCKS_SORT_KEY_MID => 'mid',
		NEWBLOCKS_SORT_KEY_FUNC_NUM => 'func_num',
		NEWBLOCKS_SORT_KEY_NAME => 'name',
		NEWBLOCKS_SORT_KEY_TITLE => 'title',
		NEWBLOCKS_SORT_KEY_SIDE => 'side',
		NEWBLOCKS_SORT_KEY_WEIGHT => 'weight',
		NEWBLOCKS_SORT_KEY_BLOCK_TYPE => 'block_type',
		NEWBLOCKS_SORT_KEY_C_TYPE => 'c_type',
		NEWBLOCKS_SORT_KEY_DIRNAME => 'dirname',
		NEWBLOCKS_SORT_KEY_TEMPLATE => 'template',
		NEWBLOCKS_SORT_KEY_BCACHETIME => 'bcachetime',
		NEWBLOCKS_SORT_KEY_LAST_MODIFIED => 'last_modified'
	);
	//wanikoo
	var $mKeyword = "";
	var $mModule = null;
	var $mOptionField = "all";

	function getDefaultSortKey()
	{
		return NEWBLOCKS_SORT_KEY_DEFAULT;
	}

	function fetch()
	{
		parent::fetch();

		$root =& XCube_Root::getSingleton();
		$mid = $root->mContext->mRequest->getRequest('mid');
		$side = $root->mContext->mRequest->getRequest('side');
		$weight = $root->mContext->mRequest->getRequest('weight');
		$block_type = $root->mContext->mRequest->getRequest('block_type');
		$c_type = $root->mContext->mRequest->getRequest('c_type');
		$dirname = $root->mContext->mRequest->getRequest('dirname');
		$search = $root->mContext->mRequest->getRequest('search');
		$option_field = $root->mContext->mRequest->getRequest('option_field');

		if (isset($_REQUEST['mid'])) {
			$this->mNavi->addExtra('mid', xoops_getrequest('mid'));
			$this->_mCriteria->add(new Criteria('mid', xoops_getrequest('mid')));
		}

		if (isset($_REQUEST['side'])) {
			$this->mNavi->addExtra('side', xoops_getrequest('side'));
			$this->_mCriteria->add(new Criteria('side', xoops_getrequest('side')));
		}

		if (isset($_REQUEST['weight'])) {
			$this->mNavi->addExtra('weight', xoops_getrequest('weight'));
			$this->_mCriteria->add(new Criteria('weight', xoops_getrequest('weight')));
		}

		if (isset($_REQUEST['block_type'])) {
			$this->mNavi->addExtra('block_type', xoops_getrequest('block_type'));
			$this->_mCriteria->add(new Criteria('block_type', xoops_getrequest('block_type')));
		}

		if (isset($_REQUEST['c_type'])) {
			$this->mNavi->addExtra('c_type', xoops_getrequest('c_type'));
			$this->_mCriteria->add(new Criteria('c_type', xoops_getrequest('c_type')));
		}

		if (isset($_REQUEST['dirname']) and !$_REQUEST['dirname'] == 0) {
			if (intval($dirname) == -1){
			$this->_mCriteria->add(new Criteria('block_type', 'C'));
			$this->mModule = "cblock";
			}
			else {
			$this->_mCriteria->add(new Criteria('dirname', xoops_getrequest('dirname')));
			//wanikoo
			$handler =& xoops_gethandler('module');
			$this->mModule =& $handler->getByDirname($dirname);
			}
			$this->mNavi->addExtra('dirname', xoops_getrequest('dirname'));
		}


		if (isset($_REQUEST['search'])) {
			$this->mKeyword = $search;
			$this->mNavi->addExtra('search', $this->mKeyword);
			$this->_mCriteria->add(new Criteria('name', '%' . $this->mKeyword . '%', 'LIKE'));
		}

		if (isset($_REQUEST['option_field'])) {
			$this->mOptionField = $option_field;
			if ( $this->mOptionField != "all" ) {
			$this->_mCriteria->add(new Criteria('side', intval($this->mOptionField)));
			}
			$this->mNavi->addExtra('option_field', $this->mOptionField);
		}


		//
		$this->_mCriteria->add(new Criteria('visible', $this->_getVisible()));
		$this->_mCriteria->add(new Criteria('isactive', 1));

		//
		// Set sort conditions.
		//
		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());

		//
		// If the sort key is mid, set c_type to second sort key for list display.
		//
		if (abs($this->mSort) == NEWBLOCKS_SORT_KEY_MID) {
			$this->_mCriteria->addSort('c_type', $this->getOrder());
		}

		if (abs($this->mSort) != NEWBLOCKS_SORT_KEY_SIDE) {
			$this->_mCriteria->addSort('side', $this->getOrder());
		}

		if (abs($this->mSort) != NEWBLOCKS_SORT_KEY_WEIGHT) {
			$this->_mCriteria->addSort('weight', $this->getOrder());
		}
	}

	function _getVisible()
	{
		return 1;
	}
}

?>
