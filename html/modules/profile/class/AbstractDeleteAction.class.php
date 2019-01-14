<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once dirname(__FILE__) . "/AbstractEditAction.class.php";

class Profile_AbstractDeleteAction extends Profile_AbstractEditAction
{
    /**
     * @protected
     */
    public function _isEnableCreate()
    {
        return false;
    }

    /**
     * @public
     */
    public function prepare()
    {
        parent::prepare();
        return is_object($this->mObject);
    }

    /**
     * @protected
     */
    public function _doExecute()
    {
        if ($this->mObjectHandler->delete($this->mObject)) {
            return PROFILE_FRAME_VIEW_SUCCESS;
        }
    
        return PROFILE_FRAME_VIEW_ERROR;
    }
}
