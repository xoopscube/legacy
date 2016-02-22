<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Profile_AbstractViewAction extends Profile_AbstractAction
{
    public $mObject = null;
    public $mObjectHandler = null;

    /**
     * @protected
     */
    public function _getId()
    {
    }

    /**
     * @protected
     */
    public function &_getHandler()
    {
    }

    /**
     * @protected
     */
    public function _setupObject()
    {
        $id = $this->_getId();
    
        $this->mObjectHandler =& $this->_getHandler();
    
        $this->mObject =& $this->mObjectHandler->get($id);
    }

    /**
     * @public
     */
    public function prepare()
    {
        $this->_setupObject();
        return is_object($this->mObject);
    }

    /**
     * @public
     */
    public function getDefaultView()
    {
        if ($this->mObject == null) {
            return PROFILE_FRAME_VIEW_ERROR;
        }
    
        return PROFILE_FRAME_VIEW_SUCCESS;
    }

    /**
     * @public
     */
    public function execute()
    {
        return $this->getDefaultView($controller, $xoopsUser, $moduleConfig);
    }
}
