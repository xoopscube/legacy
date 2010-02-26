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

/**
 * Lecat_AbstractFilterForm
**/
abstract class Lecat_AbstractFilterForm
{
    /**
     * @brief   Enum
    **/
    public $mSort = 0;

    /**
     * @brief   string[]
    **/
    public $mSortKeys = array();

    /**
     * @brief   XCube_PageNavigator
    **/
    public $mNavi = null;

    /**
     * @brief   XoopsObjectGenericHandler
    **/
    protected $_mHandler = null;

    /**
     * @brief   Criteria
    **/
    protected $_mCriteria = null;

    /**
     * _getId
     * 
     * @param   void
     * 
     * @return  int
    **/
    protected function _getId()
    {
    }

    /**
     * &_getHandler
     * 
     * @param   void
     * 
     * @return  XoopsObjectGenericHandler
    **/
    protected function &_getHandler()
    {
    }

    /**
     * __construct
     * 
     * @param   void
     * 
     * @return  void
    **/
    public function __construct()
    {
        $this->_mCriteria =new CriteriaCompo();
    }

    /**
     * prepare
     * 
     * @param   XCube_PageNavigator  &$navi
     * @param   XoopsObjectGenericHandler  &$handler
     * 
     * @return  void
    **/
    public function prepare(/*** XCube_PageNavigator ***/ &$navi,/*** XoopsObjectGenericHandler ***/ &$handler)
    {
        $this->mNavi =& $navi;
        $this->_mHandler =& $handler;
    
        $this->mNavi->mGetTotalItems->add(array(&$this, 'getTotalItems'));
    }

    /**
     * getTotalItems
     * 
     * @param   int  &$total
     * 
     * @return  void
    **/
    public function getTotalItems(/*** int ***/ &$total)
    {
        $total = $this->_mHandler->getCount($this->getCriteria());
    }

    /**
     * fetchSort
     * 
     * @param   void
     * 
     * @return  void
    **/
    protected function fetchSort()
    {
        $root =& XCube_Root::getSingleton();
        $this->mSort = intval($root->mContext->mRequest->getRequest($this->mNavi->mPrefix . 'sort'));
    
        if(!isset($this->mSortKeys[abs($this->mSort)]))
        {
            $this->mSort = $this->getDefaultSortKey();
        }
    
        $this->mNavi->mSort[$this->mNavi->mPrefix . 'sort'] = $this->mSort;
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
        $this->mNavi->fetch();
        $this->fetchSort();
    }

    /**
     * getSort
     * 
     * @param   void
     * 
     * @return  Enum
    **/
    public function getSort()
    {
        $sortkey = abs($this->mSort);
        return $this->mSortKeys[$sortkey];
    }

    /**
     * getOrder
     * 
     * @param   void
     * 
     * @return  Enum
    **/
    public function getOrder()
    {
        return ($this->mSort < 0) ? 'desc' : 'asc';
    }

    /**
     * &getCriteria
     * 
     * @param   int  $start
     * @param   int  $limit
     * 
     * @return  Criteria
    **/
    public function &getCriteria(/*** int ***/ $start = null,/*** int ***/ $limit = null)
    {
        $t_start = ($start === null) ? $this->mNavi->getStart() : intval($start);
        $t_limit = ($limit === null) ? $this->mNavi->getPerpage() : intval($limit);
    
        $criteria = $this->_mCriteria;
    
        $criteria->setStart($t_start);
        $criteria->setLimit($t_limit);
        return $criteria;
    }
}

?>
