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

define('LECAT_CAT_SORT_KEY_CAT_ID', 1);
define('LECAT_CAT_SORT_KEY_TITLE', 2);
define('LECAT_CAT_SORT_KEY_P_ID', 3);
define('LECAT_CAT_SORT_KEY_MODULES', 4);
define('LECAT_CAT_SORT_KEY_DESCRIPTION', 5);
define('LECAT_CAT_SORT_KEY_WEIGHT', 6);
define('LECAT_CAT_SORT_KEY_OPTIONS', 7);
define('LECAT_CAT_SORT_KEY_DEFAULT', LECAT_CAT_SORT_KEY_CAT_ID);

/**
 * Lecat_CatFilterForm
**/
class Lecat_CatFilterForm extends Lecat_AbstractFilterForm
{
    /**
     * @var  string[]
     * 
     * @public
    **/
    var $mSortKeys = array(
        LECAT_CAT_SORT_KEY_CAT_ID => 'cat_id',
        LECAT_CAT_SORT_KEY_TITLE => 'title',
        LECAT_CAT_SORT_KEY_P_ID => 'p_id',
        LECAT_CAT_SORT_KEY_MODULES => 'modules',
        LECAT_CAT_SORT_KEY_DESCRIPTION => 'description',
        LECAT_CAT_SORT_KEY_WEIGHT => 'weight',
        LECAT_CAT_SORT_KEY_OPTIONS => 'options'
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
        return LECAT_CAT_SORT_KEY_DEFAULT;
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
    
        if (($value = $root->mContext->mRequest->getRequest('cat_id')) !== null) {
            $this->mNavi->addExtra('cat_id', $value);
            $this->_mCriteria->add(new Criteria('cat_id', $value));
        }
    
        if (($value = $root->mContext->mRequest->getRequest('title')) !== null) {
            $this->mNavi->addExtra('title', $value);
            $this->_mCriteria->add(new Criteria('title', $value));
        }
    
        if (($value = $root->mContext->mRequest->getRequest('p_id')) !== null) {
            $this->mNavi->addExtra('p_id', $value);
            $this->_mCriteria->add(new Criteria('p_id', $value));
        }
    
        if (($value = $root->mContext->mRequest->getRequest('weight')) !== null) {
            $this->mNavi->addExtra('weight', $value);
            $this->_mCriteria->add(new Criteria('weight', $value));
        }
    
        $this->_mCriteria->addSort($this->getSort(), $this->getOrder());
    }
}

?>
