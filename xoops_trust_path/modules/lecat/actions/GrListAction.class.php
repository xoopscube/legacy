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
 * Lecat_GrListAction
**/
class Lecat_GrListAction extends Lecat_AbstractListAction
{
    /**
     * &_getHandler
     * 
     * @param   void
     * 
     * @return  Lecat_GrHandler
    **/
    protected function &_getHandler()
    {
        $handler =& $this->mAsset->getObject('handler', 'gr');
        return $handler;
    }

    /**
     * &_getFilterForm
     * 
     * @param   void
     * 
     * @return  Lecat_GrFilterForm
    **/
    protected function &_getFilterForm()
    {
        // $filter =& new Lecat_GrFilterForm();
        $filter =& $this->mAsset->getObject('filter', 'gr',false);
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
        return './index.php?action=GrList';
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
        $render->setTemplateName($this->mAsset->mDirname . '_gr_list.html');
        #cubson::lazy_load_array('gr', $this->mObjects);
        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);
    
    /*
        $jQuery = $this->mRoot->mContext->getAttribute('jQuery');
        $jQuery->appendCss('/modules/'. $this->mAsset->mDirname .'/lecat.css');
        $this->mRoot->mContext->setAttribute('jQuery', $jQuery);
    */
    }
}

?>
