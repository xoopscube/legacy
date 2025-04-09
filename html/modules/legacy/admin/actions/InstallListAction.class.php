<?php
/**
 * InstallListAction.class.php
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/***
 * @public
 * @internal
 * List up non-installation modules.
 */
class Legacy_InstallListAction extends Legacy_Action
{
    public $mModuleObjects = null;

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $handler =& xoops_getmodulehandler('non_installation_module');

        $this->mModuleObjects =& $handler->getObjects();

        return LEGACY_FRAME_VIEW_INDEX;
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$renderer)
    {
        $renderer->setTemplateName('install_list.html');
        $renderer->setAttribute('moduleObjects', $this->mModuleObjects);
    }
}
