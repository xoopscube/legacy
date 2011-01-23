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

require_once XOOPS_ROOT_PATH . '/core/XCube_PageNavigator.class.php';

/**
 * Lecat_AbstractListAction
**/
abstract class Lecat_AbstractListAction extends Lecat_AbstractAction
{
    /**
     * @brief   XoopsSimpleObject[]
    **/
    public $mObjects = null;

    /**
     * @brief   Lecat_AbstractFilterForm
    **/
    public $mFilter = null;

    /**
     * &_getHandler
     * 
     * @param   void
     * 
     * @return  &XoopsObjectGenericHandler
    **/
    protected function &_getHandler()
    {
    }

    /**
     * _getActionTitle
     * 
     * @param   void
     * 
     * @return  string
    **/
    protected function _getActionTitle()
    {
        return _LIST;
    }

    /**
     * &_getFilterForm
     * 
     * @param   void
     * 
     * @return  &LecatAbstractFilterForm
    **/
    protected function &_getFilterForm()
    {
    }

    /**
     * _getBaseUrl
     * 
     * @param   void
     * 
     * @return  string
    **/
    protected function _getBaseUrl()
    {
    }

    /**
     * &_getPageNavi
     * 
     * @param   void
     * 
     * @return  &XCube_PageNavigator
    **/
    protected function &_getPageNavi()
    {
        $navi =new XCube_PageNavigator($this->_getBaseUrl(), XCUBE_PAGENAVI_START);
        return $navi;
    }

    /**
     * getDefaultView
     * 
     * @param   void
     * 
     * @return  Enum
    **/
    public function getDefaultView()
    {
        $this->mFilter =& $this->_getFilterForm();
        $this->mFilter->fetch();
    
        $handler =& $this->_getHandler();
        $this->mObjects =& $handler->getObjects($this->mFilter->getCriteria());
    
        return LECAT_FRAME_VIEW_INDEX;
    }

    /**
     * execute
     * 
     * @param   void
     * 
     * @return  Enum
    **/
    public function execute()
    {
        return $this->getDefaultView();
    }
}

?>
