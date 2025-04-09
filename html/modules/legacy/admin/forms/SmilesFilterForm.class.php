<?php
/**
 * SmilesFilterForm.class.php
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/class/AbstractFilterForm.class.php';

define('SMILES_SORT_KEY_ID', 1);
define('SMILES_SORT_KEY_CODE', 2);
define('SMILES_SORT_KEY_SMILE_URL', 3);
define('SMILES_SORT_KEY_EMOTION', 4);
define('SMILES_SORT_KEY_DISPLAY', 5);

define('SMILES_SORT_KEY_DEFAULT', -SMILES_SORT_KEY_DISPLAY);
define('SMILES_SORT_KEY_MAXVALUE', 5);

class Legacy_SmilesFilterForm extends Legacy_AbstractFilterForm
{
    public $mSortKeys = [
        SMILES_SORT_KEY_ID => 'id',
        SMILES_SORT_KEY_CODE => 'code',
        SMILES_SORT_KEY_SMILE_URL => 'smile_url',
        SMILES_SORT_KEY_EMOTION => 'emotion',
        SMILES_SORT_KEY_DISPLAY => 'display'
    ];

    public $mKeyword = '';
    public $mOptionField = '';

    public function getDefaultSortKey()
    {
        return SMILES_SORT_KEY_DEFAULT;
    }

    public function fetch()
    {
        parent::fetch();

        $root =& XCube_Root::getSingleton();
        $code = $root->mContext->mRequest->getRequest('code');
        $smile_url = $root->mContext->mRequest->getRequest('smile_url');
        $emotion = $root->mContext->mRequest->getRequest('emotion');
        $display = $root->mContext->mRequest->getRequest('display');
        $option_field = $root->mContext->mRequest->getRequest('option_field');
        $search = $root->mContext->mRequest->getRequest('search');

        if (isset($_REQUEST['code'])) {
            $this->mNavi->addExtra('code', xoops_getrequest('code'));
            $this->_mCriteria->add(new Criteria('code', xoops_getrequest('code')));
        }

        if (isset($_REQUEST['smile_url'])) {
            $this->mNavi->addExtra('smile_url', xoops_getrequest('smile_url'));
            $this->_mCriteria->add(new Criteria('smile_url', xoops_getrequest('smile_url')));
        }

        if (isset($_REQUEST['emotion'])) {
            $this->mNavi->addExtra('emotion', xoops_getrequest('emotion'));
            $this->_mCriteria->add(new Criteria('emotion', xoops_getrequest('emotion')));
        }

        if (isset($_REQUEST['display'])) {
            $this->mNavi->addExtra('display', xoops_getrequest('display'));
            $this->_mCriteria->add(new Criteria('display', xoops_getrequest('display')));
        }

        if (isset($_REQUEST['option_field'])) {
            $this->mNavi->addExtra('option_field', xoops_getrequest('option_field'));
            $this->mOptionField = $option_field;
            if ('visible' == $this->mOptionField) {
                $this->_mCriteria->add(new Criteria('display', '1'));
            } elseif ('invisible' == $this->mOptionField) {
                $this->_mCriteria->add(new Criteria('display', '0'));
            } else {
                //all
            }
        }

        //
        if (!empty($search)) {
            $this->mKeyword = $search;
            $this->mNavi->addExtra('search', $this->mKeyword);
            $search_criteria = new CriteriaCompo(new Criteria('code', '%' . $this->mKeyword . '%', 'LIKE'));
            $search_criteria->add(new Criteria('emotion', '%' . $this->mKeyword . '%', 'LIKE'), $condition='OR');
            $this->_mCriteria->add($search_criteria);
        }

        $this->_mCriteria->addSort($this->getSort(), $this->getOrder());
    }
}
