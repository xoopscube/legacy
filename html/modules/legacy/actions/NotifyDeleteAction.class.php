<?php
/**
 * NotifyDeleteAction.class.php
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

require_once XOOPS_ROOT_PATH . '/include/notification_functions.php';

require_once XOOPS_MODULE_PATH . '/legacy/forms/NotifyDeleteForm.class.php';

/***
 * @internal
 * List up notifications. This action is like notifications.php (when $op is
 * 'list').
 */
class Legacy_NotifyDeleteAction extends Legacy_Action
{
    public $mModules = [];
    public $mActionForm = null;

    public $mErrorMessage = null;

    public function prepare(&$controller, &$xoopsUser)
    {
        $controller->mRoot->mLanguageManager->loadPageTypeMessageCatalog('notification');
        $controller->mRoot->mLanguageManager->loadModuleMessageCatalog('legacy');

        $this->mActionForm =new Legacy_NotifyDeleteForm();
        $this->mActionForm->prepare();
    }

    public function hasPermission(&$controller, &$xoopsUser)
    {
        return is_object($xoopsUser);
    }

    /**
     * This member function is a special case. Because the confirm is must, it
     * uses token error for displaying confirm.
     * @param $contoller
     * @param $xoopsUser
     * @return int
     */
    public function execute(&$contoller, &$xoopsUser)
    {
        $this->mActionForm->fetch();
        $this->mActionForm->validate();

        //
        // If input values are error, the action form returns fatal error flag.
        // If it's not fatal, display confirm form.
        //
        if ($this->mActionForm->hasError()) {
            return $this->mActionForm->mFatalError ? LEGACY_FRAME_VIEW_ERROR : LEGACY_FRAME_VIEW_INPUT;
        }

        //
        // Execute deleting.
        //
        $successFlag = true;
        $handler =& xoops_gethandler('notification');
        foreach ($this->mActionForm->mNotifiyIds as $t_idArr) {
            $t_notify =& $handler->get($t_idArr['id']);
            if (is_object($t_notify) && $t_notify->get('not_uid') == $xoopsUser->get('uid') && $t_notify->get('not_modid') == $t_idArr['modid']) {
                $successFlag = $successFlag & $handler->delete($t_notify);
            }
        }

        return $successFlag ? LEGACY_FRAME_VIEW_SUCCESS : LEGACY_FRAME_VIEW_ERROR;
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('legacy_notification_delete.html');
        $render->setAttribute('actionForm', $this->mActionForm);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward(XOOPS_URL . '/notifications.php');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect(XOOPS_URL . '/notifications.php', 2, _NOT_NOTHINGTODELETE);
    }
}
