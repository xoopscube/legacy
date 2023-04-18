<?php
/**
 * @package    profile
 * @version    2.3.1
 * @author     Other Authors Nuno Luciano (aka gigamaster), 2020, XCL PHP7
 * @author     Original Author Kilica
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Profile_AbstractEditAction extends Profile_AbstractAction
{
    public $mObject = null;
    public $mObjectHandler = null;
    public $mActionForm = null;

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
    public function _setupActionForm()
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

        if (null == $this->mObject && $this->_isEnableCreate()) {
            $this->mObject =& $this->mObjectHandler->create();
        }
    }

    /**
     * @protected
     */
    public function _isEnableCreate()
    {
        return true;
    }

    /**
     * @public
     */
    public function prepare()
    {
        $this->_setupObject();
        $this->_setupActionForm();
    }

    /**
     * @public
     */
    public function getDefaultView()
    {
        if (null == $this->mObject) {
            return PROFILE_FRAME_VIEW_ERROR;
        }

        $this->mActionForm->load($this->mObject);

        return PROFILE_FRAME_VIEW_INPUT;
    }

    /**
     * @public
     */
    public function execute()
    {
        if (null == $this->mObject) {
            return PROFILE_FRAME_VIEW_ERROR;
        }

        if (null != xoops_getrequest('_form_control_cancel')) {
            return PROFILE_FRAME_VIEW_CANCEL;
        }

        $this->mActionForm->load($this->mObject);

        $this->mActionForm->fetch();
        $this->mActionForm->validate();

        if ($this->mActionForm->hasError()) {
            return PROFILE_FRAME_VIEW_INPUT;
        }

        $this->mActionForm->update($this->mObject);

        return $this->_doExecute();
    }

    /**
     * @protected
     */
    public function _doExecute()
    {
        if ($this->mObjectHandler->insert($this->mObject)) {
            return PROFILE_FRAME_VIEW_SUCCESS;
        }

        return PROFILE_FRAME_VIEW_ERROR;
    }
}
