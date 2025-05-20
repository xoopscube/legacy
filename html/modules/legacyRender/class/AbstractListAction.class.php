<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_PageNavigator.class.php';

class LegacyRender_AbstractListAction extends LegacyRender_Action
{
    public $mObjects = [];
    public $mFilter = null;

    public function &_getHandler()
    {
    }

    public function &_getFilterForm()
    {
    }

    public function _getBaseUrl()
    {
    }
    
    public function &_getPageNavi()
    {
        $navi =new XCube_PageNavigator($this->_getBaseUrl(), XCUBE_PAGENAVI_START);
        return $navi;
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $this->mFilter =& $this->_getFilterForm();
        if ($this->mFilter !== null) {
            $this->mFilter->fetch();
            
            $handler =& $this->_getHandler();
            if ($handler !== null) {
                $this->mObjects =& $handler->getObjects($this->mFilter->getCriteria());
            }
        }
        
        return LEGACYRENDER_FRAME_VIEW_INDEX;
    }
}
