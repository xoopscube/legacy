<?php
/**
 * Bannerstats - Module for XCL
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/AbstractDeleteAction.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/forms/BannerAdminDeleteForm.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/Banner.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/handler/BannerFinish.class.php';


class Bannerstats_BannerDeleteAction extends Bannerstats_AbstractDeleteAction
{
    /**
     * Gets the ID of the banner to be deleted
     * @return int|null
     */
    public function _getId()
    {
        // Ensure it's an integer
        return xoops_getrequest('bid') ? (int)xoops_getrequest('bid') : null;
    }

    /**
     * Override the parent _doExecute method to implement custom deletion logic
     * that moves the banner to the bannerfinish table before deletion.
     * 
     * @return bool True on success
     */
    public function _doExecute()
    {
        // Get banner data before deletion
        $bannerHandler = xoops_getmodulehandler('banner', 'bannerstats');
        $banner = $this->mObject;
        
        if (!$banner) {
            $this->mActionForm->addErrorMessage(_AD_BANNERSTATS_ERROR_COULD_NOT_FIND_BANNER);
            return false;
        }
        
        // Copy banner to bannerfinish table
        $bannerfinishHandler = xoops_getmodulehandler('bannerfinish', 'bannerstats');
        $bannerfinish = $bannerfinishHandler->create();
        
        // Copy all relevant fields from banner to bannerfinish
        $bannerfinish->set('bid', $banner->get('bid'));
        $bannerfinish->set('cid', $banner->get('cid'));
        $bannerfinish->set('campaign_id', $banner->get('campaign_id'));
        $bannerfinish->set('name', $banner->get('name'));
        $bannerfinish->set('banner_type', $banner->get('banner_type'));
        $bannerfinish->set('imptotal_allocated', $banner->get('imptotal'));
        $bannerfinish->set('impressions_made', $banner->get('impmade'));
        $bannerfinish->set('clicks_made', $banner->get('clicks'));
        $bannerfinish->set('imageurl', $banner->get('imageurl'));
        $bannerfinish->set('clickurl', $banner->get('clickurl'));
        $bannerfinish->set('htmlcode', $banner->get('htmlcode'));
        $bannerfinish->set('width', $banner->get('width'));
        $bannerfinish->set('height', $banner->get('height'));
        $bannerfinish->set('datestart_original', $banner->get('start_date'));
        $bannerfinish->set('dateend_original', $banner->get('end_date'));
        $bannerfinish->set('timezone_original', $banner->get('timezone'));
        $bannerfinish->set('date_created_original', $banner->get('date_created'));
        $bannerfinish->set('date_finished', time());
        // Use the constant instead of hardcoded string
        $bannerfinish->set('finish_reason', BANNER_FINISH_REASON_ADMIN);
        // Store the admin user ID who performed the deletion
        $root = XCube_Root::getSingleton();
        $currentUser = $root->mContext->mXoopsUser;
        $finished_by_uid = $currentUser ? $currentUser->getVar('uid') : 0;
        $bannerfinish->set('finished_by_uid', $finished_by_uid);
        
        if ($bannerfinishHandler->insert($bannerfinish)) {
            // Successfully copied, now delete from banner table
            if ($bannerHandler->delete($banner)) {
                return true;
            } else {
                // Failed to delete from banner table
                $this->mActionForm->addErrorMessage(_AD_BANNERSTATS_ERROR_COULD_NOT_DELETE);
                return false;
            }
        } else {
            $this->mActionForm->addErrorMessage(_AD_BANNERSTATS_ERROR_COULD_NOT_COPY_TO_FINISH);
            return false;
        }
    }

    /**
     * Gets the handler for banner objects
     * @return Bannerstats_BannerHandler|false handler object
     */
    public function &_getHandler()
    {
        $handler = xoops_getmodulehandler('banner', 'bannerstats');
        return $handler;
    }

    /**
     * Sets up the action form for banner deletion
     * @return void
     */
    public function _setupActionForm(): void
    {
        $this->mActionForm = new Bannerstats_BannerAdminDeleteForm();
        $this->mActionForm->prepare();
    }

    /**
     * Prepares and sets data for the delete confirmation view
     *
     * @param XCube_Controller $controller
     * @param XoopsUser        $xoopsUser
     * @param XCube_RenderTarget $render
     * @return void
     */
    public function executeViewInput(&$controller, &$xoopsUser, &$render): void
    {
        $render->setTemplateName('banner_delete.html');
        $render->setAttribute('actionForm', $this->mActionForm);

        if (is_object($this->mObject) && $this->mObject instanceof Bannerstats_BannerObject) {
            $this->mObject->loadBannerclient();
        }
        $render->setAttribute('object', $this->mObject);
    }

    /**
     * Handles successful deletion
     *
     * @param XCube_Controller $controller
     * @param XoopsUser        $xoopsUser
     * @param XCube_RenderTarget $render
     * @return void
     */
    public function executeViewSuccess(&$controller, &$xoopsUser, &$render): void
    {
        $controller->executeForward('./index.php?action=BannerList');
    }

    /**
     * Handles errors during deletion
     *
     * @param XCube_Controller $controller
     * @param XoopsUser        $xoopsUser
     * @param XCube_RenderTarget $render
     * @return void
     */
    public function executeViewError(&$controller, &$xoopsUser, &$render): void
    {
        $controller->executeRedirect('./index.php?action=BannerList', 1, _AD_BANNERSTATS_ERROR_DBUPDATE_FAILED);
    }

    /**
     * Handles cancellation of the deletion
     *
     * @param XCube_Controller $controller
     * @param XoopsUser        $xoopsUser
     * @param XCube_RenderTarget $render
     * @return void
     */
    public function executeViewCancel(&$controller, &$xoopsUser, &$render): void
    {
        $controller->executeForward('./index.php?action=BannerList');
    }
}
