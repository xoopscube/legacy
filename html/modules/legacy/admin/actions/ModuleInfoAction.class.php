<?php
/**
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

/**
 * This action will show the information of a module specified to user.
 */
class Legacy_ModuleInfoAction extends Legacy_Action
{
    /**
     * XoopsModule instance specified.
     */
    public $mModuleObject = null;
    public $mInstalledFlag = false;

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $dirname = xoops_getrequest('dirname');
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $dirname)) {
            return LEGACY_FRAME_VIEW_ERROR;
        }

        if (!is_dir(XOOPS_MODULE_PATH . '/' . $dirname)) {
            return LEGACY_FRAME_VIEW_ERROR;
        }

        $moduleHandler =& xoops_gethandler('module');
        $this->mModuleObject =& $moduleHandler->getByDirname($dirname);

        //
        // If a module is installed, load modinfo and go to special displaying.
        //
        if (is_object($this->mModuleObject)) {
            $this->mModuleObject->loadAdminMenu();
            $this->mModuleObject->loadInfo($dirname);
            if (!is_string($this->mModuleObject->modinfo['adminmenu'])) {
                $this->mModuleObject->modinfo['adminmenu'] = '';
            }
            $this->mInstalledFlag = true;
        } else {
            $this->mModuleObject =& $moduleHandler->create();
            $this->mModuleObject->loadInfoAsVar($dirname);
            $this->mInstalledFlag = false;
        }

        return LEGACY_FRAME_VIEW_SUCCESS;
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$renderer)
    {
        $renderer->setTemplateName('module_information.html');
        $renderer->setAttribute('module', $this->mModuleObject);
        $renderer->setAttribute('installed', $this->mInstalledFlag);
    }

    public function executeViewError(&$controller, &$xoopsUser, &$renderer)
    {
        $controller->executeRedirect('./index.php?action=ModuleList', 1, _AD_LEGACY_ERROR_MODULE_NOT_FOUND);
    }
}
