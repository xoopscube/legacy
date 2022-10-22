<?php
/**
 * @package legacyRender
 * @version $Id: TplsetCloneAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacyRender/admin/actions/TplsetEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/legacyRender/admin/forms/TplsetCloneForm.class.php';

class LegacyRender_TplsetCloneAction extends LegacyRender_TplsetEditAction
{
    public $mCloneObject = null;
    
    public function _setupObject()
    {
        parent::_setupObject();
        $this->mCloneObject =& $this->mObjectHandler->create();
    }
    
    public function _setupActionForm()
    {
        $this->mActionForm =new LegacyRender_TplsetCloneForm();
        $this->mActionForm->prepare();
    }

    public function isAllowDefault()
    {
        return true;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        if (null == $this->mObject) {
            return LEGACYRENDER_FRAME_VIEW_ERROR;
        }

        if (null != xoops_getrequest('_form_control_cancel')) {
            return LEGACYRENDER_FRAME_VIEW_CANCEL;
        }

        //
        // If image is no, the data has to continue to keep his value.
        //
        $this->mActionForm->load($this->mCloneObject);

        $this->mActionForm->fetch();
        $this->mActionForm->validate();

        if ($this->mActionForm->hasError()) {
            return LEGACYRENDER_FRAME_VIEW_INPUT;
        }
            
        $this->mActionForm->update($this->mCloneObject);
        
        return $this->mObjectHandler->insertClone($this->mObject, $this->mCloneObject) ? LEGACYRENDER_FRAME_VIEW_SUCCESS
                                                             : LEGACYRENDER_FRAME_VIEW_ERROR;
    }
    
    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('tplset_clone.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=TplsetList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect('./index.php?action=TplsetList', 1, _AD_LEGACYRENDER_ERROR_DBUPDATE_FAILED);
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=TplsetList');
    }
}
