<?php
/**
 * @package Bannerstats
 * @version $Id: BannerfinishDeleteAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/AbstractDeleteAction.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/forms/BannerfinishAdminDeleteForm.class.php';

class Bannerstats_BannerfinishDeleteAction extends Bannerstats_AbstractDeleteAction
{
    public function _getId()
    {
        return xoops_getrequest('bid');
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('bannerfinish');
        return $handler;
    }

    public function _setupActionForm()
    {
        $this->mActionForm =new Bannerstats_BannerfinishAdminDeleteForm();
        $this->mActionForm->prepare();
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('bannerfinish_delete.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $this->mObject->loadBannerclient();
        $render->setAttribute('object', $this->mObject);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=BannerfinishList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect('./index.php?action=BannerfinishList', 1, _AD_BANNERSTATS_ERROR_DBUPDATE_FAILED);
    }
    
    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=BannerfinishList');
    }
}
