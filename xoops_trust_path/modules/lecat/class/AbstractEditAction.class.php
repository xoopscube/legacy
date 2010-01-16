<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

/**
 * Lecat_AbstractEditAction
**/
abstract class Lecat_AbstractEditAction extends Lecat_AbstractAction
{
    /**
     * @brief   XoopsSimpleObject
    **/
    public $mObject = null;

    /**
     * @brief   XoopsObjectGenericHandler
    **/
    public $mObjectHandler = null;

    /**
     * @brief   XCube_ActionForm
    **/
    public $mActionForm = null;

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
            return LECAT_FRAME_VIEW_ERROR;
        }
    
        $this->mActionForm->load($this->mObject);
    
        return LECAT_FRAME_VIEW_INPUT;
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
            return LECAT_FRAME_VIEW_ERROR;
        }
    
        if ($this->mRoot->mContext->mRequest->getRequest('_form_control_cancel') != null)
        {
            return LECAT_FRAME_VIEW_CANCEL;
        }
    
        $this->mActionForm->load($this->mObject);
    
        $this->mActionForm->fetch();
        $this->mActionForm->validate();
    
        if ($this->mActionForm->hasError())
        {
            return LECAT_FRAME_VIEW_INPUT;
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
            return LECAT_FRAME_VIEW_SUCCESS;
        }
    
        return LECAT_FRAME_VIEW_ERROR;
    }
}

?>
