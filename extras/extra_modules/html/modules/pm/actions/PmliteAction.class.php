<?php
/**
 * @package pm
 * @version $Id: PmliteAction.class.php,v 1.1 2007/05/15 02:35:27 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . "/pm/class/AbstractEditAction.class.php";
require_once XOOPS_MODULE_PATH . "/pm/forms/PmliteEditForm.class.php";

class Pm_PmliteAction extends Pm_AbstractEditAction
{
    public $_mSendType = 0;
    
    public function prepare(&$controller, &$xoopsUser, &$moduleConfig)
    {
        $this->_mSendType = $moduleConfig['send_type'];
        parent::prepare($controller, $xoopsUser, $moduleConfig);
        
        if (is_object($xoopsUser)) {
            $this->mObject->set('from_userid', $xoopsUser->get('uid'));
        }
    }
    
    public function _getId()
    {
        return 0;
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('priv_msgs', 'pm');
        return $handler;
    }

    public function _setupActionForm()
    {
        //
        // Create action form object by reply mode? or not?
        //
        $this->mActionForm = $this->_mSendType == 0 ? new Pm_PmliteComboEditForm() : new Pm_PmliteDirectEditForm();
        if (isset($_GET['reply']) && $_GET['reply'] == 1) {
            $this->mActionForm->changeStateReply();
        }

        $this->mActionForm->prepare();
    }

    public function _setupObject()
    {
        $this->mObjectHandler =& $this->_getHandler();
        $this->mObject =& $this->mObjectHandler->create();
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        //
        // Fetch request only (no validate).
        //
        $this->mActionForm->fetch();

        return PM_FRAME_VIEW_INPUT;
    }
    
    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName("pmlite.html");
        $render->setAttribute("actionForm", $this->mActionForm);
        $render->setAttribute("send_type", $this->_mSendType);

        //
        // If the request doesn't have uid, list up users to template.
        //
        if ($this->_mSendType == 0 && $this->mActionForm->getVar('to_userid') == 0) {
            $handler =& xoops_gethandler('user');
            $userObjectArr =& $handler->getObjectsByLevel(0);
            $render->setAttribute("userList", $userObjectArr);
        }
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName("pm_pmlite_success.html");
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect("pmlite.php", 3, _MD_PM_ERROR_MESSAGE_SEND);
    }
}
