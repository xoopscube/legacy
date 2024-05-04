<?php
/**
 * AbstractModuleInstallAction.class.php
 * This is abstract class for 3 action classes : Install, Update and Uninstall.
 * @package    Legacy
 * @version    XCL 2.4.0
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

 if (!defined('XOOPS_ROOT_PATH')) {
     exit();
 }

class Legacy_AbstractModuleInstallAction extends Legacy_Action
{
    /**
     * XoopsModule instance specified.
     */
    public $mModuleObject = null;
    public $mLog = null;

    public $mActionForm = null;

    public function prepare(&$controller, &$xoopsUser)
    {
        $this->_setupActionForm();
    }

    public function _setupActionForm()
    {
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $dirname = trim(xoops_getrequest('dirname'));

        $installer =& $this->_getInstaller($dirname);

        $this->mModuleObject =& $installer->loadModuleObject($dirname);

        if (!is_object($this->mModuleObject)) {
            $this->mLog =& $installer->getLog();
            return LEGACY_FRAME_VIEW_ERROR;
        }

        $this->mActionForm->load($this->mModuleObject);

        $this->mModuleObject->loadAdminMenu();
        $this->mModuleObject->loadInfo($dirname);

        return LEGACY_FRAME_VIEW_INDEX;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        if (isset($_REQUEST['_form_control_cancel'])) {
            return LEGACY_FRAME_VIEW_CANCEL;
        }

        $this->mActionForm->fetch();
        $this->mActionForm->validate();

        $installer =& $this->_getInstaller($this->mActionForm->get('dirname'));
        $this->mModuleObject =& $installer->loadModuleObject($this->mActionForm->get('dirname'));

        if ($installer->hasAgree()) {
            $this->_loadAgreement();
        }

        if ($this->mActionForm->hasError()) {
            //
            // Normal modules doesn't have licence.txt. If it has licence.txt
            // return 'INPUT' view.
            //
            if ($installer->hasAgree()) {
                return LEGACY_FRAME_VIEW_INPUT;
            } else {
                return LEGACY_FRAME_VIEW_INDEX;
            }
        }

        if (!is_object($this->mModuleObject)) {
            return LEGACY_FRAME_VIEW_ERROR;
        }

        $installer->setForceMode($this->mActionForm->get('force'));
        $installer->execute($this->mActionForm->get('dirname'));

        $this->mLog =& $installer->getLog();

        return LEGACY_FRAME_VIEW_SUCCESS;
    }

    /**
     * Return a procedure for this process.
     * @param $dirname
     */
    public function &_getInstaller($dirname)
    {
    }

    public function _loadAgreement()
    {
    }

    public function executeViewError(&$controller, &$xoopsUser, &$renderer)
    {
        $renderer->setTemplateName('install_wizard_error.html');
        $renderer->setAttribute('log', $this->mLog);
    }
}
