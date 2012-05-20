<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

require_once XUPDATE_TRUST_PATH . '/class/AbstractListAction.class.php';

/**
 * Xupdate_StoreListAction
**/
class Xupdate_StoreListAction extends Xupdate_AbstractListAction
{
    /**
     * &_getHandler
     *
     * @param   void
     *
     * @return  Xupdate_StoreHandler
    **/
    protected function &_getHandler()
    {
        $handler =& $this->mAsset->getObject('handler', 'Store');
        return $handler;
    }

    /**
     * &_getFilterForm
     *
     * @param   void
     *
     * @return  Xupdate_StoreFilterForm
    **/
    protected function &_getFilterForm()
    {
        $filter =& $this->mAsset->getObject('filter', 'Store',false);
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
        return './index.php?action=StoreList';
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
        $render->setTemplateName($this->mAsset->mDirname . '_store_list.html');
        #cubson::lazy_load_array('store', $this->mObjects);
        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);
    }
}

?>
