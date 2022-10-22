<?php
/**
 *
 * @package Legacy
 * @version $Id: AbstractEditAction.class.php,v 1.3 2008/09/25 15:11:27 kilica Exp $
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Legacy_AbstractEditAction extends Legacy_Action
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

        if (null === $this->mObject && $this->isEnableCreate()) {
            $this->mObject =& $this->mObjectHandler->create();
        }
    }

    public function isEnableCreate()
    {
        return true;
    }

    public function prepare(&$controller, &$xoopsUser)
    {
        $this->_setupObject();
        $this->_setupActionForm();
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        if (null === $this->mObject) {
            return LEGACY_FRAME_VIEW_ERROR;
        }

        $this->mActionForm->load($this->mObject);

        return LEGACY_FRAME_VIEW_INPUT;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        if (null === $this->mObject) {
            return LEGACY_FRAME_VIEW_ERROR;
        }

        if (null !== xoops_getrequest('_form_control_cancel')) {
            return LEGACY_FRAME_VIEW_CANCEL;
        }

        $this->mActionForm->load($this->mObject);

        $this->mActionForm->fetch();
        $this->mActionForm->validate();

        if ($this->mActionForm->hasError()) {
            return LEGACY_FRAME_VIEW_INPUT;
        }

        $this->mActionForm->update($this->mObject);

        return $this->_doExecute($this->mObject) ? LEGACY_FRAME_VIEW_SUCCESS
                                                 : LEGACY_FRAME_VIEW_ERROR;
    }

    public function _doExecute()
    {
        return $this->mObjectHandler->insert($this->mObject);
    }
}
