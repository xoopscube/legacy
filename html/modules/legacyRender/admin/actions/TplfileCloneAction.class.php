<?php
/**
 * @package legacyRender
 * @version $Id: TplfileCloneAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacyRender/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacyRender/admin/forms/TplfileCloneForm.class.php';

class LegacyRender_TplfileCloneAction extends LegacyRender_AbstractEditAction
{
    public $mTargetObject = null;
    
    public function _getId()
    {
        return xoops_getrequest('tpl_id');
    }
    
    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('tplfile');
        return $handler;
    }

    public function _setupObject()
    {
        $id = $this->_getId();
        
        $this->mObjectHandler =& $this->_getHandler();
        $obj =& $this->mObjectHandler->get($id);
        
        //
        // The following code uses 'tpl_tplset' directly. This input value will
        // be checked by ActionForm.
        //
        if (is_object($obj) && 'default' == $obj->get('tpl_tplset')) {
            $this->mObject =& $obj->createClone(xoops_getrequest('tpl_tplset'));
        }
    }
    
    public function isEnableCreate()
    {
        return false;
    }
    
    public function _setupActionForm()
    {
        $this->mActionForm =new LegacyRender_TplfileCloneForm();
        $this->mActionForm->prepare();
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('tplfile_clone.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);
        $render->setAttribute('tpl_id', xoops_getrequest('tpl_id'));
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $tplset = $this->mObject->get('tpl_tplset');
        $module = $this->mObject->get('tpl_module');
        $controller->executeForward("./index.php?action=TplfileList&tpl_tplset={$tplset}&tpl_module={$module}");
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect('./index.php?action=TplsetList', 1, _AD_LEGACYRENDER_ERROR_DBUPDATE_FAILED);
    }
}
