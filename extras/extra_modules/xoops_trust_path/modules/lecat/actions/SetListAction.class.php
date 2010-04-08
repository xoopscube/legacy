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

require_once LECAT_TRUST_PATH . '/class/AbstractListAction.class.php';

/**
 * Lecat_SetListAction
**/
class Lecat_SetListAction extends Lecat_AbstractListAction
{
    /**
     * &_getHandler
     * 
     * @param   void
     * 
     * @return  Lecat_SetHandler
    **/
    protected function &_getHandler()
    {
        $handler =& $this->mAsset->getObject('handler', 'set');
        return $handler;
    }

    /**
     * &_getFilterForm
     * 
     * @param   void
     * 
     * @return  Lecat_SetFilterForm
    **/
    protected function &_getFilterForm()
    {
        // $filter =new Lecat_SetFilterForm();
        $filter =& $this->mAsset->getObject('filter', 'set',false);
        $filter->prepare($this->_getPageNavi(), $this->_getHandler());
        return $filter;
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
        return './index.php?action=SetList';
    }

    /**
     * executeViewIndex
     * 
     * @param   XCube_RenderTarget  &$render
     * 
     * @return  void
    **/
    public function executeViewIndex(/*** XCube_RenderTarget ***/ &$render)
    {
        $render->setTemplateName($this->mAsset->mDirname . '_set_list.html');
        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);
    }
}

?>
