<?php
/**
 * @package bannerstats
 * @version $Id: BannerEditAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/forms/BannerAdminEditForm.class.php';

class Bannerstats_BannerEditAction extends Bannerstats_AbstractEditAction
{
    // MIN_IMPRESSIONS constant should move this to Bannerstats_BannerObject
    const MIN_IMPRESSIONS = 1;

    public function _getId()
    {
        return xoops_getrequest('bid');
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('banner');
        return $handler;
    }

    public function _setupObject()
    {
        parent::_setupObject();
        if (is_object($this->mObject) && $this->mObject->isNew()) {
            $this->mObject->set('cid', xoops_getrequest('cid'));
            // Set default imptotal
            $this->mObject->set('imptotal', self::MIN_IMPRESSIONS);
        }
    }
    
    public function _setupActionForm()
    {
        $this->mActionForm =new Bannerstats_BannerAdminEditForm();
        $this->mActionForm->prepare();
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('banner_edit.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        
        //$this->mObject->loadBannerclient();
        if (is_object($this->mObject)) { // Check if object is loaded
            $this->mObject->loadBannerclient(); // This method should exist in Bannerstats_BannerObject
        }
        
        
        $render->setAttribute('object', $this->mObject);
        
        $bannerclientHandler =& xoops_getmodulehandler('bannerclient');
        $bannerclientArr =& $bannerclientHandler->getObjects();
 // Consider adding criteria if list is large
        // foreach (array_keys($bannerclientArr) as $key) {
        //     $bannerclientArr[$key]->loadBanner(); // This might be resource-intensive if not needed
        // }
        $render->setAttribute('bannerclientArr', $bannerclientArr);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=BannerList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect('./index.php?action=BannerList', 1, '_AD_BANNERSTATS_ERROR_DBUPDATE_FAILED');
    }
    
    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=BannerList');
    }
}
