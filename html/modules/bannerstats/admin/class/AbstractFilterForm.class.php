<?php
/**
 * Bannerstats - Module for XCL
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Bannerstats_AbstractFilterForm
{
    public $mSort = 0;
    public $mSortKeys = [];
    public $_mCriteria = null;
    public $mNavi = null;

    public $_mHandler = null;

    public function __construct($navi, $handler)
    {
        $this->mNavi =& $navi;
        $this->_mHandler =& $handler;

        $this->_mCriteria =new CriteriaCompo();

        $this->mNavi->mGetTotalItems->add([&$this, 'getTotalItems']);
    }

    public function getDefaultSortKey()
    {
    }

    public function getTotalItems(&$total)
    {
        $total = $this->_mHandler->getCount($this->getCriteria());
    }

    public function fetchSort()
    {
        $root =& XCube_Root::getSingleton();
        $this->mSort = (int)$root->mContext->mRequest->getRequest('sort');

        if (!isset($this->mSortKeys[abs($this->mSort)])) {
            $this->mSort = $this->getDefaultSortKey();
        }

        $this->mNavi->mSort['sort'] = $this->mSort;
    }

    public function fetch()
    {
        $this->mNavi->fetch();
        $this->fetchSort();
    }

    public function getSort()
    {
        $sortkey = abs($this->mNavi->mSort['sort']);
        return $this->mSortKeys[$sortkey] ?? null;
    }

    public function getOrder()
    {
        return ($this->mSort < 0) ? 'DESC' : 'ASC';
    }

    public function getCriteria($start = null, $limit = null)
    {
        $t_start = (null === $start) ? $this->mNavi->getStart() : (int)$start;
        $t_limit = (null === $limit) ? $this->mNavi->getPerpage() : (int)$limit;

        $criteria = $this->_mCriteria;

        $criteria->setStart($t_start);
        $criteria->setLimit($t_limit);

        return $criteria;
    }
}
