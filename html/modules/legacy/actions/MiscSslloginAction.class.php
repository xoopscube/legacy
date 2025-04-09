<?php
/**
 * MiscSslloginAction.class.php
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
 * @internal
 * @public
 * @todo This action should be implemented by base. We must move it to user.
 */
class Legacy_MiscSslloginAction extends Legacy_Action
{
    public function execute(&$controller, &$xoopsUser)
    {
        return LEGACY_FRAME_VIEW_INDEX;
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        //
        // Because this action's template uses USER message catalog, load it.
        //
        $root =& $controller->mRoot;

        $config_handler =& xoops_gethandler('config');
        $moduleConfigUser =& $config_handler->getConfigsByDirname('user');
    
        if (1 == $moduleConfigUser['use_ssl'] && ! empty($_POST[$moduleConfigUser['sslpost_name']])) {
            if (!isset($_SESSION)) {
                session_id($_POST[$moduleConfigUser['sslpost_name']]);
              }
        }
    
        $render->setTemplateName('legacy_misc_ssllogin.html');
        $render->setAttribute('message', XCube_Utils::formatString(_MD_LEGACY_MESSAGE_LOGIN_SUCCESS, $xoopsUser->get('uname')));
    }
}
