<?php
/**
 * PreferenceListAction.class.php
 * @package    Legacy
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/PreferenceEditForm.class.php';

class Legacy_PreferenceListAction extends Legacy_Action
{
    public $mObjects = [];

    public function prepare(&$controller, &$xoopsUser)
    {
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $handler =& xoops_gethandler('configcategory');
        $this->mObjects =& $handler->getObjects();

        return LEGACY_FRAME_VIEW_INDEX;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        return $this->getDefaultView($controller, $xoopsUser);
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('preference_list.html');
        $render->setAttribute('objects', $this->mObjects);
    }
}
