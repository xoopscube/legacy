<?php
/**
 * @version legacyRender
 * @version $Id: TplsetEditAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . "/legacyRender/class/AbstractEditAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacyRender/admin/forms/TplsetEditForm.class.php";

class LegacyRender_TplsetEditAction extends LegacyRender_AbstractEditAction
{
    public function _getId()
    {
        return xoops_getrequest('tplset_id');
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('tplset');
        return $handler;
    }

    public function _setupObject()
    {
        parent::_setupObject();

        if ($this->isAllowDefault() == false) {
            if (is_object($this->mObject) && $this->mObject->get('tplset_name') == 'default') {
                $this->mObject = null;
            }
        }
    }
    
    public function _setupActionForm()
    {
        $this->mActionForm =new LegacyRender_TplsetEditForm();
        $this->mActionForm->prepare();
    }

    public function isEnableCreate()
    {
        return false;
    }

    public function isAllowDefault()
    {
        return false;
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName("tplset_edit.html");
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward("./index.php?action=TplsetList");
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect("./index.php?action=TplsetList", 1, _AD_LEGACYRENDER_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward("./index.php?action=TplsetList");
    }
}
