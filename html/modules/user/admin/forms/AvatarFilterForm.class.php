<?php
/**
 * @package user
 * @author  Kazuhisa Minato aka minahito, Core developer
 * @version $Id: AvatarFilterForm.class.php,v 1.1 2007/05/15 02:34:39 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/user/class/AbstractFilterForm.class.php';

define('AVATAR_SORT_KEY_AVATAR_ID', 1);
define('AVATAR_SORT_KEY_AVATAR_FILE', 2);
define('AVATAR_SORT_KEY_AVATAR_NAME', 3);
define('AVATAR_SORT_KEY_AVATAR_MIMETYPE', 4);
define('AVATAR_SORT_KEY_AVATAR_CREATED', 5);
define('AVATAR_SORT_KEY_AVATAR_DISPLAY', 6);
define('AVATAR_SORT_KEY_AVATAR_WEIGHT', 7);
define('AVATAR_SORT_KEY_AVATAR_TYPE', 8);
define('AVATAR_SORT_KEY_MAXVALUE', 8);

define('AVATAR_SORT_KEY_DEFAULT', AVATAR_SORT_KEY_AVATAR_ID);

class User_AvatarFilterForm extends User_AbstractFilterForm
{
    public $mSortKeys = [
        AVATAR_SORT_KEY_AVATAR_ID => 'avatar_id',
        AVATAR_SORT_KEY_AVATAR_FILE => 'avatar_file',
        AVATAR_SORT_KEY_AVATAR_NAME => 'avatar_name',
        AVATAR_SORT_KEY_AVATAR_MIMETYPE => 'avatar_mimetype',
        AVATAR_SORT_KEY_AVATAR_CREATED => 'avatar_created',
        AVATAR_SORT_KEY_AVATAR_DISPLAY => 'avatar_display',
        AVATAR_SORT_KEY_AVATAR_WEIGHT => 'avatar_weight',
        AVATAR_SORT_KEY_AVATAR_TYPE => 'avatar_type'
    ];

    public $mKeyword = '';
    public $mOptionField = '';
    public $mOptionField2 = '';
    public $mOptionField3 = '';
    
    public function getDefaultSortKey()
    {
        return AVATAR_SORT_KEY_DEFAULT;
    }

    public function fetch()
    {
        parent::fetch();
    
        $root =& XCube_Root::getSingleton();
        $avatar_display = $root->mContext->mRequest->getRequest('avatar_display');
        $avatar_type = $root->mContext->mRequest->getRequest('avatar_type');
        $option_field = $root->mContext->mRequest->getRequest('option_field');
        $option_field2 = $root->mContext->mRequest->getRequest('option_field2');
        $option_field3 = $root->mContext->mRequest->getRequest('option_field3');
        $search = $root->mContext->mRequest->getRequest('search');

        if (isset($_REQUEST['avatar_display'])) {
            $this->mNavi->addExtra('avatar_display', xoops_getrequest('avatar_display'));
            $this->_mCriteria->add(new Criteria('avatar_display', [XOBJ_DTYPE_BOOL, xoops_getrequest('avatar_display')]));
        }
    
        if (isset($_REQUEST['avatar_type'])) {
            $this->mNavi->addExtra('avatar_type', xoops_getrequest('avatar_type'));
            $this->_mCriteria->add(new Criteria('avatar_type', [XOBJ_DTYPE_STRING, xoops_getrequest('avatar_type')]));
        }

        if (isset($_REQUEST['option_field'])) {
            $this->mNavi->addExtra('option_field', xoops_getrequest('option_field'));
            $this->mOptionField = $option_field;
            if ('system' == $this->mOptionField) {
                //only system avatar
            $this->_mCriteria->add(new Criteria('avatar_type', 'S'));
            } elseif ('custom' == $this->mOptionField) {
                //only custom avatar
            $this->_mCriteria->add(new Criteria('avatar_type', 'C'));
            } else {
                //all
            }
        }

        if (isset($_REQUEST['option_field2'])) {
            $this->mNavi->addExtra('option_field2', xoops_getrequest('option_field2'));
            $this->mOptionField2 = $option_field2;
            if ('visible' == $this->mOptionField2) {
                $this->_mCriteria->add(new Criteria('avatar_display', '1'));
            } elseif ('invisible' == $this->mOptionField2) {
                $this->_mCriteria->add(new Criteria('avatar_display', '0'));
            } else {
                //all
            }
        }

        if (isset($_REQUEST['option_field3'])) {
            $this->mNavi->addExtra('option_field3', xoops_getrequest('option_field3'));
            $this->mOptionField3 = $option_field3;
            if ('gif' == $this->mOptionField3) {
                $this->_mCriteria->add(new Criteria('avatar_mimetype', 'image/gif'));
            } elseif ('png' == $this->mOptionField3) {
                $this->_mCriteria->add(new Criteria('avatar_mimetype', 'image/png'));
            } elseif ('jpeg' == $this->mOptionField3) {
                $this->_mCriteria->add(new Criteria('avatar_mimetype', 'image/jpeg'));
            } else {
                //all
            }
        }

        //
        if (!empty($search)) {
            $this->mKeyword = $search;
            $this->mNavi->addExtra('search', $this->mKeyword);
            $this->_mCriteria->add(new Criteria('avatar_name', '%' . $this->mKeyword . '%', 'LIKE'));
        }
        
        $this->_mCriteria->addSort($this->getSort(), $this->getOrder());
    }
}
