<?php
/**
 * @package bannerstats
 * @version $Id: BannerReactivateAction.class.php,v 1.0 2024/05/19 Nuno Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/forms/BannerAdminEditForm.class.php';

class Bannerstats_BannerReactivateAction extends Bannerstats_AbstractEditAction
{
    public function _getId()
    {
        return xoops_getrequest('bid');
    }

    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('bannerfinish', 'bannerstats');
        return $handler;
    }

    public function _setupActionForm()
    {
        $this->mActionForm = new Bannerstats_BannerAdminEditForm();
        $this->mActionForm->prepare();
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('banner_reactivate.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $this->mObject->loadBannerclient();
        $render->setAttribute('object', $this->mObject);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        // Get the finished banner data
        $finishedBanner = $this->mObject;
        
        if (!is_object($finishedBanner)) {
            $controller->executeRedirect('./index.php?action=BannerfinishList', 3, _AD_BANNERSTATS_ERROR_OBJECT_IS_NOT_EXIST);
            return;
        }
        
        // Create a new active banner
        $bannerHandler =& xoops_getmodulehandler('banner', 'bannerstats');
        $activeBanner = $bannerHandler->create();
        
        if (!is_object($activeBanner)) {
            $controller->executeRedirect('./index.php?action=BannerfinishList', 3, '_AD_BANNERSTATS_ERROR_CREATE_FAILED');
            return;
        }
        $imptotal = $this->mActionForm->get('imptotal');
if (empty($imptotal) || !is_numeric($imptotal)) {
    $imptotal = 1000; // Default value
}
$activeBanner->set('imptotal', $imptotal);
        // Set properties for the new active banner
        $activeBanner->set('cid', $finishedBanner->get('cid'));
        $activeBanner->set('imptotal', $this->mActionForm->get('imptotal'));
        $activeBanner->set('impmade', 0);
        $activeBanner->set('clicks', 0);
        $activeBanner->set('date', time());
        
        // Copy the banner content properties from the finished banner
        // These fields should now be available in the bannerfinish table
        $activeBanner->set('imageurl', $finishedBanner->get('imageurl'));
        $activeBanner->set('clickurl', $finishedBanner->get('clickurl'));
        $activeBanner->set('htmlbanner', $finishedBanner->get('htmlbanner'));
        $activeBanner->set('htmlcode', $finishedBanner->get('htmlcode'));
        
        // Insert the new active banner
        if ($bannerHandler->insert($activeBanner)) {
            // Delete the finished banner record
            $finishHandler =& xoops_getmodulehandler('bannerfinish', 'bannerstats');
            $finishHandler->delete($finishedBanner);
            
            // Redirect to the banner list
            $controller->executeRedirect('./index.php?action=BannerList', 3, '_AD_BANNERSTATS_MESSAGE_BANNER_REACTIVATED');
        } else {
            $controller->executeRedirect('./index.php?action=BannerfinishList', 3, _AD_BANNERSTATS_ERROR_DBUPDATE_FAILED);
        }
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeRedirect('./index.php?action=BannerfinishList', 3, _AD_BANNERSTATS_ERROR_DBUPDATE_FAILED);
    }
    
    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=BannerfinishList');
    }
}
