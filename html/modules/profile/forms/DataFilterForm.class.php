<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/profile/class/AbstractFilterForm.class.php";

define('PROFILE_DATA_SORT_KEY_UID', 1);
define('PROFILE_DATA_SORT_KEY_DEFAULT', PROFILE_DATA_SORT_KEY_UID);

class Profile_DataFilterForm extends Profile_AbstractFilterForm
{
	var $mSortKeys = array(
		PROFILE_DATA_SORT_KEY_UID => 'uid'
	);

	/**
	 * @public
	 */
	function getDefaultSortKey()
	{
		return PROFILE_DATA_SORT_KEY_DEFAULT;
	}

	/**
	 * @public
	 */
	function fetch()
	{
		parent::fetch();
	
		$root =& XCube_Root::getSingleton();
	
		if (($value = $root->mContext->mRequest->getRequest('uid')) !== null) {
			$this->mNavi->addExtra('uid', $value);
			$this->_mCriteria->add(new Criteria('uid', $value));
		}
	
		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
	}
}

?>
