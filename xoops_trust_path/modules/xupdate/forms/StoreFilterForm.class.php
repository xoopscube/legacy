<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Naoki Sawada, Naoki Okino, Gigamaster (XCL 2.3)
 * @copyright Copyright 2005-2023 The XOOPSCube Project
 * @license GPL v2.0
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

require_once XUPDATE_TRUST_PATH . '/class/AbstractFilterForm.class.php';

define( 'XUPDATE_STORE_SORT_KEY_SID', 1 );
define( 'XUPDATE_STORE_SORT_KEY_UID', 2 );
define( 'XUPDATE_STORE_SORT_KEY_VALID', 3 );
define( 'XUPDATE_STORE_SORT_KEY_NAME', 4 );
define( 'XUPDATE_STORE_SORT_KEY_ADDON_URL', 5 );
define( 'XUPDATE_STORE_SORT_KEY_THEME_URL', 6 );
define( 'XUPDATE_STORE_SORT_KEY_REG_UNIXTIME', 7 );

define( 'XUPDATE_STORE_SORT_KEY_DEFAULT', XUPDATE_STORE_SORT_KEY_SID );

/**
 * Xupdate_StoreFilterForm
 **/
class Xupdate_StoreFilterForm extends Xupdate_AbstractFilterForm {
	/*** string[] ***/
	public $mSortKeys = [
		XUPDATE_STORE_SORT_KEY_SID          => 'sid',
		XUPDATE_STORE_SORT_KEY_UID          => 'uid',
		XUPDATE_STORE_SORT_KEY_VALID        => 'valid',
		XUPDATE_STORE_SORT_KEY_NAME         => 'name',
		XUPDATE_STORE_SORT_KEY_ADDON_URL    => 'addon_url',
		XUPDATE_STORE_SORT_KEY_THEME_URL    => 'theme_url',
		XUPDATE_STORE_SORT_KEY_REG_UNIXTIME => 'reg_unixtime',

	];

	/**
	 * getDefaultSortKey
	 *
	 * @return int
	 */
	public function getDefaultSortKey() {
		return XUPDATE_STORE_SORT_KEY_DEFAULT;
	}

	/**
	 * fetch
	 *
	 * @param void
	 *
	 * @return  void
	 **/
	public function fetch() {
		parent::fetch();

		$root =& XCube_Root::getSingleton();

		if ( null !== ( $value = $root->mContext->mRequest->getRequest( 'sid' ) ) ) {
			$this->mNavi->addExtra( 'sid', $value );
			$this->_mCriteria->add( new Criteria( 'sid', $value ) );
		}
		if ( null !== ( $value = $root->mContext->mRequest->getRequest( 'uid' ) ) ) {
			$this->mNavi->addExtra( 'uid', $value );
			$this->_mCriteria->add( new Criteria( 'uid', $value ) );
		}
		if ( null !== ( $value = $root->mContext->mRequest->getRequest( 'valid' ) ) ) {
			$this->mNavi->addExtra( 'valid', $value );
			$this->_mCriteria->add( new Criteria( 'valid', $value ) );
		}
		if ( null !== ( $value = $root->mContext->mRequest->getRequest( 'name' ) ) ) {
			$this->mNavi->addExtra( 'name', $value );
			$this->_mCriteria->add( new Criteria( 'name', $value ) );
		}
		if ( null !== ( $value = $root->mContext->mRequest->getRequest( 'addon_url' ) ) ) {
			$this->mNavi->addExtra( 'addon_url', $value );
			$this->_mCriteria->add( new Criteria( 'addon_url', $value ) );
		}
		if ( null !== ( $value = $root->mContext->mRequest->getRequest( 'theme_url' ) ) ) {
			$this->mNavi->addExtra( 'theme_url', $value );
			$this->_mCriteria->add( new Criteria( 'theme_url', $value ) );
		}
		if ( null !== ( $value = $root->mContext->mRequest->getRequest( 'reg_unixtime' ) ) ) {
			$this->mNavi->addExtra( 'reg_unixtime', $value );
			$this->_mCriteria->add( new Criteria( 'reg_unixtime', $value ) );
		}


		$this->_mCriteria->addSort( $this->getSort(), $this->getOrder() );
	}
}
