<?php
/**
 * @package    profile
 * @version    2.3.1
 * @author     Nuno Luciano (aka gigamaster), 2020, XCL PHP7
 * @author     Kilica
 * @copyright  2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Profile_AbstractFilterForm
{
    public $mSort = 0;
    public $mSortKeys = [];
    public $mNavi = null;
    public $_mHandler = null;
    public $_mCriteria = null;
    public $mObjectHandler = null;

    /**
     * @protected
     */
    public function _getId()
    {
    }

    /**
     * @protected
     */
    public function &_getHandler()
    {
    }

    /**
     * @protected
     */
    public function __construct()
    {
        $this->_mCriteria =new CriteriaCompo();
    }

    /**
     * @protected
     * @param $navi
     * @param $handler
     */
    public function prepare(&$navi, &$handler)
    {
        $this->mNavi =& $navi;
        $this->_mHandler =& $handler;

        $this->mNavi->mGetTotalItems->add([&$this, 'getTotalItems']);
    }

    /**
     * @protected
     * @param $total
     */
    public function getTotalItems(&$total)
    {
        $total = $this->_mHandler->getCount($this->getCriteria());
    }

    /**
     * @protected
     */
    public function fetchSort()
    {
        $root =& XCube_Root::getSingleton();
        $this->mSort = (int)$root->mContext->mRequest->getRequest($this->mNavi->mPrefix . 'sort');

        if (!isset($this->mSortKeys[abs($this->mSort)])) {
            $this->mSort = $this->getDefaultSortKey();
        }

        $this->mNavi->mSort[$this->mNavi->mPrefix . 'sort'] = $this->mSort;
    }

    /**
     * @public
     */
    public function fetch()
    {
        $this->mNavi->fetch();
        $this->fetchSort();
    }

    /**
     * @public
     */
    public function getSort()
    {
        $sortkey = abs($this->mSort);
        return $this->mSortKeys[$sortkey];
    }

    /**
     * @public
     */
    public function getOrder()
    {
        return ($this->mSort < 0) ? 'DESC' : 'ASC';
    }

    /**
     * @public
     * @param null $start
     * @param null $limit
     * @return \CriteriaCompo|null
     */
    public function &getCriteria($start = null, $limit = null)
    {
        $t_start = (null === $start) ? $this->mNavi->getStart() : (int)$start;
        $t_limit = (null === $limit) ? $this->mNavi->getPerpage() : (int)$limit;

        $criteria = $this->_mCriteria;

        $criteria->setStart($t_start);
        $criteria->setLimit($t_limit);
        return $criteria;
    }
}
