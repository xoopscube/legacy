<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class LegacyRender_AbstractEditAction extends LegacyRender_Action
{
    public $mObject = null;
    public $mObjectHandler = null;
    public $mActionForm = null;

    public function _getId()
    {
    }

    public function &_getHandler()
    {
    }

    public function _setupActionForm()
    {
    }

    public function _setupObject()
    {
        $id = $this->_getId();
        
        $this->mObjectHandler = $this->_getHandler();
        
        $this->mObject =& $this->mObjectHandler->get($id);
    
        if ($this->mObject == null && $this->isEnableCreate()) {
            $this->mObject =& $this->mObjectHandler->create();
        }
    }

    public function isEnableCreate()
    {
        return true;
    }
    // !Fix compatibility with LegacyRender_Action::prepare(&$controller, &$xoopsUser, $moduleConfig)
    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    // public function prepare(&$controller, &$xoopsUser)
    {
        $this->_setupActionForm();
        $this->_setupObject();
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        if ($this->mObject == null) {
            return LEGACYRENDER_FRAME_VIEW_ERROR;
        }
    
        $this->mActionForm->load($this->mObject);
    
        return LEGACYRENDER_FRAME_VIEW_INPUT;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        if ($this->mObject == null) {
            return LEGACYRENDER_FRAME_VIEW_ERROR;
        }
        
        if (xoops_getrequest('_form_control_cancel') != null) {
            return LEGACYRENDER_FRAME_VIEW_CANCEL;
        }
    
        $this->mActionForm->load($this->mObject);
        
        $this->mActionForm->fetch();
        $this->mActionForm->validate();
    
        if ($this->mActionForm->hasError()) {
            return LEGACYRENDER_FRAME_VIEW_INPUT;
        }
    
        $this->mActionForm->update($this->mObject);
        
        return $this->_doExecute($this->mObject) ? LEGACYRENDER_FRAME_VIEW_SUCCESS
                                                 : LEGACYRENDER_FRAME_VIEW_ERROR;
    }

    public function _doExecute()
    {
        return $this->mObjectHandler->insert($this->mObject);
    }
}
