<?php
/**
 * BlockFilterForm.class.php
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/class/AbstractFilterForm.class.php';

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
    public $mSortKeys = [
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
    ];
    //wanikoo
    public $mKeyword = '';
    public $mModule = null;
    public $mOptionField = 'all';

    public function getDefaultSortKey()
    {
        return NEWBLOCKS_SORT_KEY_DEFAULT;
    }

    public function fetch()
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

        if (isset($_REQUEST['dirname']) and 0 == !$_REQUEST['dirname']) {
            if (-1 == (int)$dirname) {
                $this->_mCriteria->add(new Criteria('block_type', 'C'));
                $this->mModule = 'cblock';
            } else {
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
            if ('all' != $this->mOptionField) {
                $this->_mCriteria->add(new Criteria('side', (int)$this->mOptionField));
            }
            $this->mNavi->addExtra('option_field', $this->mOptionField);
        }

        // added criteria of block module link
        $selectedMid = (int) $root->mContext->mRequest->getRequest('selmid');
        if (0 !== $selectedMid) {
            $handler =& xoops_getmodulehandler('block_module_link');
            $criteria = new CriteriaCompo(new Criteria('module_id', $selectedMid));
            $criteria->add(new Criteria('module_id', 0), 'OR');
            $selmodArrObj = $handler -> getObjects($criteria);
            $selmodArr = [];
            if (isset($selmodArrObj)) {
                foreach ($selmodArrObj as $selmodObj) {
                    $selmodArr[] = $selmodObj->getShow('block_id');
                }
            }
        }

        // added criteria of group permissions
        $selectedGid = (int) $root->mContext->mRequest->getRequest('selgid');
        if (0 !== $selectedGid) {
            $handler =& xoops_gethandler('groupperm');
            $criteria =new CriteriaCompo();
            $criteria->add(new Criteria('gperm_modid', 1));
            $criteria->add(new Criteria('gperm_groupid', $selectedGid));
            $criteria->add(new Criteria('gperm_name', 'block_read'));
            $selgrpArrObj =&  $handler ->getObjects($criteria);
            $selgrpArr = [];
            if (isset($selgrpArrObj)) {
                foreach ($selgrpArrObj as $selgrpObj) {
                    $selgrpArr[] = $selgrpObj->getShow('gperm_itemid');
                }
            }
        }

        $selModGrpArr = null;
        if (isset($selmodArr) && isset($selgrpArr)) {
            $selModGrpArr = array_intersect($selmodArr, $selgrpArr);
        } elseif (isset($selmodArr)) {
            $selModGrpArr = $selmodArr;
        } elseif (isset($selgrpArr)) {
            $selModGrpArr = $selgrpArr;
        }
        if (is_array($selModGrpArr)) {
            $this->_mCriteria->add(new Criteria('bid', $selModGrpArr, 'IN'));
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
        if (NEWBLOCKS_SORT_KEY_MID == abs($this->mSort)) {
            $this->_mCriteria->addSort('c_type', $this->getOrder());
        }

        if (NEWBLOCKS_SORT_KEY_SIDE != abs($this->mSort)) {
            $this->_mCriteria->addSort('side', $this->getOrder());
        }

        if (NEWBLOCKS_SORT_KEY_WEIGHT != abs($this->mSort)) {
            $this->_mCriteria->addSort('weight', $this->getOrder());
        }
    }

    public function _getVisible()
    {
        return 1;
    }
}
