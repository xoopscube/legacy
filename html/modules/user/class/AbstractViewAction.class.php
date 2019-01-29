<?php
/**
 * @package User
 * @version $Id: AbstractViewAction.class.php,v 1.1 2007/05/15 02:34:49 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class User_AbstractViewAction extends User_Action
{
    public $mObject = null;
    public $mObjectHandler = null;
    // !Fix PHP7
    public function __construct()
    //public function User_AbstractViewAction()
    {
    }

    public function _getId()
    {
    }

    public function &_getHandler()
    {
    }

    public function _setupObject()
    {
        $id = $this->_getId();
        
        $this->mObjectHandler =& $this->_getHandler();
        
        $this->mObject =& $this->mObjectHandler->get($id);
    }

    /**
     * _getPageAction
     * 
     * @param	void
     * 
     * @return	string
    **/
    protected function _getPageAction()
    {
        return _VIEW;
    }

    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        $this->_setupObject();
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        if ($this->mObject == null) {
            return USER_FRAME_VIEW_ERROR;
        }
    
        return USER_FRAME_VIEW_SUCCESS;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        return $this->getDefaultView($controller, $xoopsUser);
    }
}
