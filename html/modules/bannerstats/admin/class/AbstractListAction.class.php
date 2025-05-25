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

require_once XOOPS_ROOT_PATH . '/core/XCube_PageNavigator.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/Action.class.php';

class Bannerstats_AbstractListAction extends Bannerstats_Action
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
        
        return BANNERSTATS_FRAME_VIEW_INDEX;
    }
}
