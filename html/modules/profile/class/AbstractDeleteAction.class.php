<?php
/**
 * @package    profile
 * @version    2.5.0
 * @author     Other Authors Nuno Luciano (aka gigamaster), 2020, XCL PHP7
 * @author     Original Author Kilica
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once __DIR__ . '/AbstractEditAction.class.php';

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
