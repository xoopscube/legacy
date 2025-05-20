<?php
/**
 * @package   Bannerstats
 * @version   $Id: BannerfinishEditAction.class.php,v 1.1 2024/05/19 Nuno Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/forms/BannerfinishAdminEditForm.class.php';

// Dependency: BannerEditAction for MIN_IMPRESSIONS constant
$bannerEditActionFile = XOOPS_MODULE_PATH . '/bannerstats/admin/actions/BannerEditAction.class.php';
if (file_exists($bannerEditActionFile)) {
    require_once $bannerEditActionFile;
} else {
    trigger_error('FATAL ERROR: BannerfinishEditAction - Required file BannerEditAction.class.php not found at: ' . $bannerEditActionFile, E_USER_ERROR);
    exit();
}

class Bannerstats_BannerfinishEditAction extends Bannerstats_AbstractEditAction
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
        $this->mActionForm = new Bannerstats_BannerfinishAdminEditForm();
        $this->mActionForm->prepare();
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('bannerfinish_edit.html');
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
        
        // Set properties for the new active banner
        $activeBanner->set('cid', $finishedBanner->get('cid'));
        
        // Get form values or use defaults
        $imptotal = $this->mActionForm->get('imptotal');
        if (empty($imptotal) || $imptotal < Bannerstats_BannerEditAction::MIN_IMPRESSIONS) {
            $imptotal = Bannerstats_BannerEditAction::MIN_IMPRESSIONS;
        }
        
        $activeBanner->set('imptotal', $imptotal);
        $activeBanner->set('impmade', 0);
        $activeBanner->set('clicks', 0);
        $activeBanner->set('date', time());
        
        // Set banner content properties
        $activeBanner->set('imageurl', $this->mActionForm->get('imageurl'));
        $activeBanner->set('clickurl', $this->mActionForm->get('clickurl'));
        $activeBanner->set('htmlbanner', $this->mActionForm->get('htmlbanner'));
        $activeBanner->set('htmlcode', $this->mActionForm->get('htmlcode'));
        
        // Insert the new active banner
        if ($bannerHandler->insert($activeBanner)) {
            // Delete the finished banner record
            $finishHandler =& xoops_getmodulehandler('bannerfinish', 'bannerstats');
            $finishHandler->delete($finishedBanner);
            
            // Redirect to the edit page for the new active banner
            $controller->executeForward('./index.php?action=BannerEdit&bid=' . $activeBanner->get('bid'));
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