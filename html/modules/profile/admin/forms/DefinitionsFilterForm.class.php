<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/profile/class/AbstractFilterForm.class.php";

define('PROFILE_DEFINITIONS_SORT_KEY_FIELD_ID', 1);
define('PROFILE_DEFINITIONS_SORT_KEY_FIELD_NAME', 2);
define('PROFILE_DEFINITIONS_SORT_KEY_LABEL', 3);
define('PROFILE_DEFINITIONS_SORT_KEY_TYPE', 4);
define('PROFILE_DEFINITIONS_SORT_KEY_VALIDATION', 5);
define('PROFILE_DEFINITIONS_SORT_KEY_REQUIRED', 6);
define('PROFILE_DEFINITIONS_SORT_KEY_SHOW_FORM', 7);
define('PROFILE_DEFINITIONS_SORT_KEY_WEIGHT', 8);
define('PROFILE_DEFINITIONS_SORT_KEY_DESCRIPTION', 9);
define('PROFILE_DEFINITIONS_SORT_KEY_ACCESS', 10);
define('PROFILE_DEFINITIONS_SORT_KEY_OPTIONS', 11);
define('PROFILE_DEFINITIONS_SORT_KEY_DEFAULT', PROFILE_DEFINITIONS_SORT_KEY_FIELD_ID);

class Profile_Admin_DefinitionsFilterForm extends Profile_AbstractFilterForm
{
	var $mSortKeys = array(
		PROFILE_DEFINITIONS_SORT_KEY_FIELD_ID => 'field_id',
		PROFILE_DEFINITIONS_SORT_KEY_FIELD_NAME => 'field_name',
		PROFILE_DEFINITIONS_SORT_KEY_LABEL => 'label',
		PROFILE_DEFINITIONS_SORT_KEY_TYPE => 'type',
		PROFILE_DEFINITIONS_SORT_KEY_VALIDATION => 'validation',
		PROFILE_DEFINITIONS_SORT_KEY_REQUIRED => 'required',
		PROFILE_DEFINITIONS_SORT_KEY_SHOW_FORM => 'show_form',
		PROFILE_DEFINITIONS_SORT_KEY_WEIGHT => 'weight',
		PROFILE_DEFINITIONS_SORT_KEY_DESCRIPTION => 'description',
		PROFILE_DEFINITIONS_SORT_KEY_ACCESS => 'access',
		PROFILE_DEFINITIONS_SORT_KEY_OPTIONS => 'options'
	);

	/**
	 * @public
	 */
	function getDefaultSortKey()
	{
		return PROFILE_DEFINITIONS_SORT_KEY_DEFAULT;
	}

	/**
	 * @public
	 */
	function fetch()
	{
		parent::fetch();
	
		$root =& XCube_Root::getSingleton();
	
		if (($value = $root->mContext->mRequest->getRequest('field_id')) !== null) {
			$this->mNavi->addExtra('field_id', $value);
			$this->_mCriteria->add(new Criteria('field_id', $value));
		}
	
		if (($value = $root->mContext->mRequest->getRequest('field_name')) !== null) {
			$this->mNavi->addExtra('field_name', $value);
			$this->_mCriteria->add(new Criteria('field_name', $value));
		}
	
		if (($value = $root->mContext->mRequest->getRequest('label')) !== null) {
			$this->mNavi->addExtra('label', $value);
			$this->_mCriteria->add(new Criteria('label', $value));
		}
	
		if (($value = $root->mContext->mRequest->getRequest('type')) !== null) {
			$this->mNavi->addExtra('type', $value);
			$this->_mCriteria->add(new Criteria('type', $value));
		}
	
		if (($value = $root->mContext->mRequest->getRequest('validation')) !== null) {
			$this->mNavi->addExtra('validation', $value);
			$this->_mCriteria->add(new Criteria('validation', $value));
		}
	
		if (($value = $root->mContext->mRequest->getRequest('required')) !== null) {
			$this->mNavi->addExtra('required', $value);
			$this->_mCriteria->add(new Criteria('required', $value));
		}
	
		if (($value = $root->mContext->mRequest->getRequest('show_form')) !== null) {
			$this->mNavi->addExtra('show_form', $value);
			$this->_mCriteria->add(new Criteria('show_form', $value));
		}
	
		if (($value = $root->mContext->mRequest->getRequest('weight')) !== null) {
			$this->mNavi->addExtra('weight', $value);
			$this->_mCriteria->add(new Criteria('weight', $value));
		}
	
		$this->_mCriteria->addSort($this->getSort(), $this->getOrder());
	}
}

?>
