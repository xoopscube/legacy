<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

require_once LECAT_TRUST_PATH . '/class/AbstractFilterForm.class.php';

define('LECAT_SET_SORT_KEY_SET_ID', 1);
define('LECAT_SET_SORT_KEY_TITLE', 2);
define('LECAT_SET_SORT_KEY_LEVEL', 3);
define('LECAT_SET_SORT_KEY_ACTIONS', 4);
define('LECAT_SET_SORT_KEY_DEFAULT', LECAT_SET_SORT_KEY_SET_ID);

/**
 * Lecat_SetFilterForm
**/
class Lecat_SetFilterForm extends Lecat_AbstractFilterForm
{
    /**
     * @var  string[]
     * 
     * @public
    **/
    var $mSortKeys = array(
        LECAT_SET_SORT_KEY_SET_ID => 'set_id',
        LECAT_SET_SORT_KEY_TITLE => 'title',
        LECAT_SET_SORT_KEY_LEVEL => 'level',
        LECAT_SET_SORT_KEY_ACTIONS => 'actions'
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
        return LECAT_SET_SORT_KEY_DEFAULT;
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
    
        if (($value = $root->mContext->mRequest->getRequest('set_id')) !== null) {
            $this->mNavi->addExtra('set_id', $value);
            $this->_mCriteria->add(new Criteria('set_id', $value));
        }
    
        if (($value = $root->mContext->mRequest->getRequest('title')) !== null) {
            $this->mNavi->addExtra('title', $value);
            $this->_mCriteria->add(new Criteria('title', $value));
        }
    
        if (($value = $root->mContext->mRequest->getRequest('level')) !== null) {
            $this->mNavi->addExtra('level', $value);
            $this->_mCriteria->add(new Criteria('level', $value));
        }
    
        $this->_mCriteria->addSort($this->getSort(), $this->getOrder());
    }
}

?>
