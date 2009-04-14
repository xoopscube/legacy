<?php
if (!defined('XOOPS_ROOT_PATH')) exit();
require_once XOOPS_ROOT_PATH.'/core/XCube_PageNavigator.class.php';

class MyPageNavi
{
  var $_mCriteria = null;
  var $_mHandler = null;
  
  var $mNavi = null;
  var $_mPagenum = 10;
  var $_mUrl = 'index.php';
  var $_Total = 0;
  
  public function __construct($handler, $criteria = null)
  {
    $this->_mUrl = XOOPS_URL.'/modules/message/index.php';
    $this->_mHandler = $handler;
    if ( is_object($criteria) ) {
      $this->_mCriteria = $criteria;
    } else {
      $this->_mCriteria = new CriteriaCompo();
    }
  }
  
  public function setPagenum($num)
  {
    $this->_mPagenum = $num;
  }
  
  public function setUrl($url)
  {
    $this->_mUrl = $url;
  }
  
  public function addSort($sort, $order = 'ASC')
  {
    $this->_mCriteria->setSort($sort, $order);
  }
  
  public function addCriteria($criteria)
  {
    $this->_mCriteria->add($criteria);
  }
  
  public function getTotalItems(&$total)
  {
    $total = $this->_Total;
  }
  
  public function fetch()
  {
    $this->_Total = $this->_mHandler->getCount($this->_mCriteria);
    $this->mNavi = new XCube_PageNavigator($this->_mUrl);
    $this->mNavi->mGetTotalItems->add(array($this, 'getTotalItems'));
    $this->mNavi->setPerpage($this->_mPagenum);
    $this->mNavi->fetch();
  }

  public function getCriteria()
  {
    $this->_mCriteria->setStart($this->mNavi->getStart());
    $this->_mCriteria->setLimit($this->mNavi->getPerpage());
    return $this->_mCriteria;
  }
}
?>
