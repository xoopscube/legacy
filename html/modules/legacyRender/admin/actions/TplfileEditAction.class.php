<?php
/**
 * @package legacyRender
 * @version $Id: TplfileEditAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacyRender/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacyRender/admin/forms/TplfileEditForm.class.php';

class LegacyRender_TplfileEditAction extends LegacyRender_AbstractEditAction
{
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
        parent::_setupObject();
        
        if (is_object($this->mObject) && 'default' == $this->mObject->get('tpl_tplset')) {
            $this->mObject = null;
        }
    }
    
    public function _setupActionForm()
    {
        $this->mActionForm =new LegacyRender_TplfileEditForm();
        $this->mActionForm->prepare();
    }

    public function isEnableCreate()
    {
        return false;
    }
    
    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('tplfile_edit.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        //
        // This class knows the db template mechanism, because this is in
        // LegacyRender.
        //
        require_once XOOPS_ROOT_PATH . '/class/template.php';

        $xoopsTpl =new XoopsTpl();
        $xoopsTpl->clear_cache('db:' . $this->mObject->get('tpl_file'));
        $xoopsTpl->clear_compiled_tpl('db:' . $this->mObject->get('tpl_file'));

        $tplset = $this->mObject->get('tpl_tplset');
        $module = $this->mObject->get('tpl_module');
        $controller->executeForward("./index.php?action=TplfileList&tpl_tplset=${tplset}&tpl_module=${module}");
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect('./index.php?action=TplsetList', 1, _AD_LEGACYRENDER_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $tplset = $this->mObject->get('tpl_tplset');
        $module = $this->mObject->get('tpl_module');
        $controller->executeForward("./index.php?action=TplfileList&tpl_tplset=${tplset}&tpl_module=${module}");
    }
}
