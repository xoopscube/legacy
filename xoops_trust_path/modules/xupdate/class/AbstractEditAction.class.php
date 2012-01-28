<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

/**
 * Xupdate_AbstractEditAction
**/
abstract class Xupdate_AbstractEditAction extends Xupdate_AbstractAction
{
    public /*** XoopsSimpleObject ***/ $mObject = null;

    public /*** XoopsObjectGenericHandler ***/ $mObjectHandler = null;

    public /*** XCube_ActionForm ***/ $mActionForm = null;

    /**
     * _getId
     * 
     * @param   void
     * 
     * @return  int
    **/
    protected function _getId()
    {
    }

    /**
     * &_getHandler
     * 
     * @param   void
     * 
     * @return  XoopsObjectGenericHandler
    **/
    protected function &_getHandler()
    {
    }

    /**
     * _getActionName
     * 
     * @param   void
     * 
     * @return  string
    **/
    protected function _getActionName()
    {
        return _EDIT;
    }

    /**
     * _setupActionForm
     * 
     * @param   void
     * 
     * @return  void
    **/
    protected function _setupActionForm()
    {
    }

    /**
     * _setupObject
     * 
     * @param   void
     * 
     * @return  void
    **/
    protected function _setupObject()
    {
        $id = $this->_getId();
    
        $this->mObjectHandler =& $this->_getHandler();
    
        $this->mObject =& $this->mObjectHandler->get($id);
    
        if($this->mObject == null && $this->_isEnableCreate())
        {
            $this->mObject =& $this->mObjectHandler->create();
        }
    }

    /**
     * _isEnableCreate
     * 
     * @param   void
     * 
     * @return  bool
    **/
    protected function _isEnableCreate()
    {
        return true;
    }

    /**
     * prepare
     * 
     * @param   void
     * 
     * @return  bool
    **/
    public function prepare()
    {
        $this->_setupObject();
        $this->_setupActionForm();
    
        return true;
    }

    /**
     * getDefaultView
     * 
     * @param   void
     * 
     * @return  Enum
    **/
    public function getDefaultView()
    {
        if($this->mObject == null)
        {
            return XUPDATE_FRAME_VIEW_ERROR;
        }
    
        $this->mActionForm->load($this->mObject);
    
        return XUPDATE_FRAME_VIEW_INPUT;
    }

    /**
     * execute
     * 
     * @param   void
     * 
     * @return  Enum
    **/
    public function execute()
    {
        if ($this->mObject == null)
        {
            return XUPDATE_FRAME_VIEW_ERROR;
        }
    
        if ($this->mRoot->mContext->mRequest->getRequest('_form_control_cancel') != null)
        {
            return XUPDATE_FRAME_VIEW_CANCEL;
        }
    
        $this->mActionForm->load($this->mObject);
    
        $this->mActionForm->fetch();
        $this->mActionForm->validate();
    
        if ($this->mActionForm->hasError())
        {
            return XUPDATE_FRAME_VIEW_INPUT;
        }
    
        $this->mActionForm->update($this->mObject);
    
        return $this->_doExecute();
    }

    /**
     * _doExecute
     * 
     * @param   void
     * 
     * @return  Enum
    **/
    protected function _doExecute()
    {
        if($this->mObjectHandler->insert($this->mObject))
        {
            return XUPDATE_FRAME_VIEW_SUCCESS;
        }
    
        return XUPDATE_FRAME_VIEW_ERROR;
    }
}

?>
