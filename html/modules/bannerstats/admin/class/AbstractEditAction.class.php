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

require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/Action.class.php';

class Bannerstats_AbstractEditAction extends Bannerstats_Action
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

        $this->mObjectHandler =& $this->_getHandler();

        $this->mObject =& $this->mObjectHandler->get($id);

        if (null == $this->mObject && $this->isEnableCreate()) {
            $this->mObject =& $this->mObjectHandler->create();
        }
    }

    public function isEnableCreate()
    {
        return true;
    }

    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        $this->_setupActionForm();
        $this->_setupObject();
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        if (null == $this->mObject) {
            return BANNERSTATS_FRAME_VIEW_ERROR;
        }

        $this->mActionForm->load($this->mObject);

        return BANNERSTATS_FRAME_VIEW_INPUT;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        if (null == $this->mObject) {
            return BANNERSTATS_FRAME_VIEW_ERROR;
        }

        if (null != xoops_getrequest('_form_control_cancel')) {
            return BANNERSTATS_FRAME_VIEW_CANCEL;
        }

        $this->mActionForm->load($this->mObject);

        $this->mActionForm->fetch();
        $this->mActionForm->validate();

        if ($this->mActionForm->hasError()) {
            return BANNERSTATS_FRAME_VIEW_INPUT;
        }

        $this->mActionForm->update($this->mObject);
        
        return $this->_doExecute() ? BANNERSTATS_FRAME_VIEW_SUCCESS
                                   : BANNERSTATS_FRAME_VIEW_ERROR;
    }

    public function _doExecute()
    {
        return $this->mObjectHandler->insert($this->mObject);
    }
}
