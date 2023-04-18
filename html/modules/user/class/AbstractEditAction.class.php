<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class User_AbstractEditAction extends User_Action
{
    public $mObject = null;
    public $mObjectHandler = null;
    public $mActionForm = null;
    public $mConfig;

    /**
     * @access protected
     */
    public function _getId()
    { }

    /**
     * @access protected
     */
    public function &_getHandler()
    { }

    /**
     * @access protected
     */
    public function _setupActionForm()
    { }

    /**
     * @access protected
     */
    public function _setupObject()
    {
        $id = $this->_getId();

        $this->mObjectHandler = $this->_getHandler();

        $this->mObject = &$this->mObjectHandler->get($id);

        if (null == $this->mObject && $this->isEnableCreate()) {
            $this->mObject = &$this->mObjectHandler->create();
        }
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
        return _EDIT;
    }

    /**
     * @access protected
     */
    public function isEnableCreate()
    {
        return true;
    }

    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        $this->mConfig = $moduleConfig;

        $this->_setupActionForm();
        $this->_setupObject();
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        if (null == $this->mObject) {
            return USER_FRAME_VIEW_ERROR;
        }

        $this->mActionForm->load($this->mObject);

        return USER_FRAME_VIEW_INPUT;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        if (null == $this->mObject) {
            return USER_FRAME_VIEW_ERROR;
        }

        if (null != xoops_getrequest('_form_control_cancel')) {
            return USER_FRAME_VIEW_CANCEL;
        }

        $this->mActionForm->load($this->mObject);

        $this->mActionForm->fetch();
        $this->mActionForm->validate();

        if ($this->mActionForm->hasError()) {
            return USER_FRAME_VIEW_INPUT;
        }

        $this->mActionForm->update($this->mObject);

        return $this->_doExecute() ? USER_FRAME_VIEW_SUCCESS
            : USER_FRAME_VIEW_ERROR;
    }

    /**
     * @access protected
     */
    public function _doExecute()
    {
        return $this->mObjectHandler->insert($this->mObject);
    }
}
