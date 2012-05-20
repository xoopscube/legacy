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

require_once XUPDATE_TRUST_PATH . '/class/AbstractViewAction.class.php';

/**
 * Xupdate_StoreViewAction
**/
class Xupdate_StoreViewAction extends Xupdate_AbstractViewAction
{
    /**
     * _getId
     * 
    **/
    protected function _getId()
    {
        return $this->mRoot->mContext->mRequest->getRequest('sid');
    }

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
     * executeViewSuccess
     * 
     * @param   XCube_RenderTarget  &$render
     * 
     * @return  void
    **/
    public function executeViewSuccess(/*** XCube_RenderTarget ***/ &$render)
    {
        $render->setTemplateName($this->mAsset->mDirname . '_store_view.html');
        $render->setAttribute('object', $this->mObject);
    }

    /**
     * executeViewError
     * 
     * @param   XCube_RenderTarget  &$render
     * 
     * @return  void
    **/
    public function executeViewError(/*** XCube_RenderTarget ***/ &$render)
    {
        $this->mRoot->mController->executeRedirect('./index.php?action=StoreList', 1, _MD_XUPDATE_ERROR_CONTENT_IS_NOT_FOUND);
    }
}

?>
