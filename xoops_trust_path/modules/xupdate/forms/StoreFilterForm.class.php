<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

require_once XUPDATE_TRUST_PATH . '/class/AbstractFilterForm.class.php';

define('XUPDATE_STORE_SORT_KEY_SID', 1);
define('XUPDATE_STORE_SORT_KEY_UID', 2);
define('XUPDATE_STORE_SORT_KEY_VALID', 3);
define('XUPDATE_STORE_SORT_KEY_NAME', 4);
define('XUPDATE_STORE_SORT_KEY_ADDON_URL', 5);
define('XUPDATE_STORE_SORT_KEY_THEME_URL', 6);
define('XUPDATE_STORE_SORT_KEY_REG_UNIXTIME', 7);

define('XUPDATE_STORE_SORT_KEY_DEFAULT', XUPDATE_STORE_SORT_KEY_SID);

/**
 * Xupdate_StoreFilterForm
**/
class Xupdate_StoreFilterForm extends Xupdate_AbstractFilterForm
{
    public /*** string[] ***/ $mSortKeys = array(
 	   XUPDATE_STORE_SORT_KEY_SID => 'sid',
 	   XUPDATE_STORE_SORT_KEY_UID => 'uid',
 	   XUPDATE_STORE_SORT_KEY_VALID => 'valid',
 	   XUPDATE_STORE_SORT_KEY_NAME => 'name',
 	   XUPDATE_STORE_SORT_KEY_ADDON_URL => 'addon_url',
 	   XUPDATE_STORE_SORT_KEY_THEME_URL => 'theme_url',
 	   XUPDATE_STORE_SORT_KEY_REG_UNIXTIME => 'reg_unixtime',

    );

    /**
     * getDefaultSortKey
     * 
     * @param   void
     * 
     * @return  void
    **/
    public function getDefaultSortKey()
    {
        return XUPDATE_STORE_SORT_KEY_DEFAULT;
    }

    /**
     * fetch
     * 
     * @param   void
     * 
     * @return  void
    **/
    public function fetch()
    {
        parent::fetch();
    
        $root =& XCube_Root::getSingleton();
    
		if (($value = $root->mContext->mRequest->getRequest('sid')) !== null) {
			$this->mNavi->addExtra('sid', $value);
			$this->_mCriteria->add(new Criteria('sid', $value));
		}
		if (($value = $root->mContext->mRequest->getRequest('uid')) !== null) {
			$this->mNavi->addExtra('uid', $value);
			$this->_mCriteria->add(new Criteria('uid', $value));
		}
		if (($value = $root->mContext->mRequest->getRequest('valid')) !== null) {
			$this->mNavi->addExtra('valid', $value);
			$this->_mCriteria->add(new Criteria('valid', $value));
		}
		if (($value = $root->mContext->mRequest->getRequest('name')) !== null) {
			$this->mNavi->addExtra('name', $value);
			$this->_mCriteria->add(new Criteria('name', $value));
		}
		if (($value = $root->mContext->mRequest->getRequest('addon_url')) !== null) {
			$this->mNavi->addExtra('addon_url', $value);
			$this->_mCriteria->add(new Criteria('addon_url', $value));
		}
		if (($value = $root->mContext->mRequest->getRequest('theme_url')) !== null) {
			$this->mNavi->addExtra('theme_url', $value);
			$this->_mCriteria->add(new Criteria('theme_url', $value));
		}
		if (($value = $root->mContext->mRequest->getRequest('reg_unixtime')) !== null) {
			$this->mNavi->addExtra('reg_unixtime', $value);
			$this->_mCriteria->add(new Criteria('reg_unixtime', $value));
		}

    
        $this->_mCriteria->addSort($this->getSort(), $this->getOrder());
    }
}

?>
