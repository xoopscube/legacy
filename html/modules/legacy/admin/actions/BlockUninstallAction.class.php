<?php
/**
 * BlockUninstallAction.class.php
 * @package    Legacy
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/BlockUninstallForm.class.php';

class Legacy_BlockUninstallAction extends Legacy_AbstractEditAction
{
    public function _getId()
    {
        return isset($_REQUEST['bid']) ? xoops_getrequest('bid') : 0;
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('newblocks');
        return $handler;
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new Legacy_BlockUninstallForm();
        $this->mActionForm->prepare();
    }

    public function _isEditable()
    {
        if (is_object($this->mObject)) {
            return (1 == $this->mObject->get('visible'));
        } else {
            return false;
        }
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('block_uninstall.html');
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
        $controller->executeForward('./index.php?action=BlockList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect('./index.php?action=BlockList', 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=BlockList');
    }
}
