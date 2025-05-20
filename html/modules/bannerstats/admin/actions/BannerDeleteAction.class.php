<?php
/**
 * @package Bannerstats
 * @version $Id: BannerDeleteAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/AbstractDeleteAction.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/forms/BannerAdminDeleteForm.class.php';

class Bannerstats_BannerDeleteAction extends Bannerstats_AbstractDeleteAction
{
    public function _getId()
    {
        return xoops_getrequest('bid');
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('banner');
        return $handler;
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new Bannerstats_BannerAdminDeleteForm();
        $this->mActionForm->prepare();
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('banner_delete.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $this->mObject->loadBannerclient();
        $render->setAttribute('object', $this->mObject);
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
