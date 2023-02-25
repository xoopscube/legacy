<?php
/**
 * CustomBlockDeleteAction.class.php
 * @package    Legacy
 * @version    XCL 2.3.1
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/class/AbstractDeleteAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/CustomBlockDeleteForm.class.php';

class Legacy_CustomBlockDeleteAction extends Legacy_AbstractDeleteAction
{
    public function _getId()
    {
        return isset($_REQUEST['bid']) ? $_REQUEST['bid'] : 0;
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('newblocks');
        return $handler;
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new Legacy_CustomBlockDeleteForm();
        $this->mActionForm->prepare();
    }

    public function _isDeletable()
    {
        if (is_object($this->mObject)) {
            return ('C' == $this->mObject->get('block_type') && 0 == $this->mObject->get('visible'));
        } else {
            return false;
        }
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        if (!$this->_isDeletable()) {
            return LEGACY_FRAME_VIEW_ERROR;
        }

        return parent::getDefaultView($controller, $xoopsUser);
    }

    public function execute(&$controller, &$xoopsUser)
    {
        if (!$this->_isDeletable()) {
            return LEGACY_FRAME_VIEW_ERROR;
        }

        return parent::execute($controller, $xoopsUser);
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('customblock_delete.html');
        $render->setAttribute('actionForm', $this->mActionForm);

        //
        // lazy loading
        //
        $this->mObject->loadModule();
        $this->mObject->loadColumn();
        $this->mObject->loadCachetime();

        $render->setAttribute('object', $this->mObject);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=BlockInstallList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect('./index.php?action=BlockInstallList', 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        if ($this->mObject->isNew()) {
            $controller->executeForward('./index.php?action=BlockInstallList');
        } else {
            $controller->executeForward('./index.php?action=BlockList');
        }
    }
}
