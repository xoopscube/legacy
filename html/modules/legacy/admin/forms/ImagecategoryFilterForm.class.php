<?php
/**
 * ImagecategoryFilterForm.class.php
 * @package    Legacy
 * @version    XCL 2.3.1
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/class/AbstractFilterForm.class.php';

define('IMAGECATEGORY_SORT_KEY_IMGCAT_ID', 1);
define('IMAGECATEGORY_SORT_KEY_IMGCAT_NAME', 2);
define('IMAGECATEGORY_SORT_KEY_IMGCAT_MAXSIZE', 3);
define('IMAGECATEGORY_SORT_KEY_IMGCAT_MAXWIDTH', 4);
define('IMAGECATEGORY_SORT_KEY_IMGCAT_MAXHEIGHT', 5);
define('IMAGECATEGORY_SORT_KEY_IMGCAT_DISPLAY', 6);
define('IMAGECATEGORY_SORT_KEY_IMGCAT_WEIGHT', 7);
define('IMAGECATEGORY_SORT_KEY_IMGCAT_TYPE', 8);
define('IMAGECATEGORY_SORT_KEY_IMGCAT_STORETYPE', 9);

define('IMAGECATEGORY_SORT_KEY_DEFAULT', IMAGECATEGORY_SORT_KEY_IMGCAT_WEIGHT);
define('IMAGECATEGORY_SORT_KEY_MAXVALUE', 9);

class Legacy_ImagecategoryFilterForm extends Legacy_AbstractFilterForm
{
    public $mSortKeys = [
        IMAGECATEGORY_SORT_KEY_IMGCAT_ID => 'imgcat_id',
        IMAGECATEGORY_SORT_KEY_IMGCAT_NAME => 'imgcat_name',
        IMAGECATEGORY_SORT_KEY_IMGCAT_MAXSIZE => 'imgcat_maxsize',
        IMAGECATEGORY_SORT_KEY_IMGCAT_MAXWIDTH => 'imgcat_maxwidth',
        IMAGECATEGORY_SORT_KEY_IMGCAT_MAXHEIGHT => 'imgcat_maxheight',
        IMAGECATEGORY_SORT_KEY_IMGCAT_DISPLAY => 'imgcat_display',
        IMAGECATEGORY_SORT_KEY_IMGCAT_WEIGHT => 'imgcat_weight',
        IMAGECATEGORY_SORT_KEY_IMGCAT_TYPE => 'imgcat_type',
        IMAGECATEGORY_SORT_KEY_IMGCAT_STORETYPE => 'imgcat_storetype'
    ];

    public $mKeyword = '';
    public $mOptionField = '';
    public $mOptionField2 = '';

    public function getDefaultSortKey()
    {
        return IMAGECATEGORY_SORT_KEY_DEFAULT;
    }

    public function fetch()
    {
        parent::fetch();

        $root =& XCube_Root::getSingleton();
        $imgcat_name = $root->mContext->mRequest->getRequest('imgcat_name');
        $imgcat_display = $root->mContext->mRequest->getRequest('imgcat_display');
        $imgcat_type = $root->mContext->mRequest->getRequest('imgcat_type');
        $imgcat_storetype = $root->mContext->mRequest->getRequest('imgcat_storetype');
        $option_field = $root->mContext->mRequest->getRequest('option_field');
        $option_field2 = $root->mContext->mRequest->getRequest('option_field2');
        $search = $root->mContext->mRequest->getRequest('search');


        if (isset($_REQUEST['imgcat_name'])) {
            $this->mNavi->addExtra('imgcat_name', xoops_getrequest('imgcat_name'));
            $this->_mCriteria->add(new Criteria('imgcat_name', xoops_getrequest('imgcat_name')));
        }

        if (isset($_REQUEST['imgcat_display'])) {
            $this->mNavi->addExtra('imgcat_display', xoops_getrequest('imgcat_display'));
            $this->_mCriteria->add(new Criteria('imgcat_display', xoops_getrequest('imgcat_display')));
        }

        if (isset($_REQUEST['imgcat_type'])) {
            $this->mNavi->addExtra('imgcat_type', xoops_getrequest('imgcat_type'));
            $this->_mCriteria->add(new Criteria('imgcat_type', xoops_getrequest('imgcat_type')));
        }

        if (isset($_REQUEST['imgcat_storetype'])) {
            $this->mNavi->addExtra('imgcat_storetype', xoops_getrequest('imgcat_storetype'));
            $this->_mCriteria->add(new Criteria('imgcat_storetype', xoops_getrequest('imgcat_storetype')));
        }

        if (isset($_REQUEST['option_field'])) {
            $this->mNavi->addExtra('option_field', xoops_getrequest('option_field'));
            $this->mOptionField = $option_field;
            if ('visible' == $this->mOptionField) {
                $this->_mCriteria->add(new Criteria('imgcat_display', xoops_getrequest('1')));
            } elseif ('invisible' == $this->mOptionField) {
                $this->_mCriteria->add(new Criteria('imgcat_display', xoops_getrequest('0')));
            } else {
                //all
            }
        }

        if (isset($_REQUEST['option_field2'])) {
            $this->mNavi->addExtra('option_field2', xoops_getrequest('option_field2'));
            $this->mOptionField2 = $option_field2;
            if ('file' == $this->mOptionField2) {
                $this->_mCriteria->add(new Criteria('imgcat_storetype', xoops_getrequest('file')));
            } elseif ('db' == $this->mOptionField2) {
                $this->_mCriteria->add(new Criteria('imgcat_storetype', xoops_getrequest('db')));
            } else {
                //all
            }
        }

        //
        if (!empty($search)) {
            $this->mKeyword = $search;
            $this->mNavi->addExtra('search', $this->mKeyword);
            $this->_mCriteria->add(new Criteria('imgcat_name', '%' . $this->mKeyword . '%', 'LIKE'));
        }

        //
        // Set sort conditions.
        //
        $this->_mCriteria->addSort($this->getSort(), $this->getOrder());
        if (IMAGECATEGORY_SORT_KEY_IMGCAT_WEIGHT != abs($this->mSort)) {
            $this->_mCriteria->addSort($this->mSortKeys[IMAGECATEGORY_SORT_KEY_IMGCAT_WEIGHT], $this->getOrder());
        }
    }
}
