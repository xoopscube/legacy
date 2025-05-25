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
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/forms/BannerfinishAdminDeleteForm.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/handler/BannerFinish.class.php';

class Bannerstats_BannerfinishDeleteAction extends Bannerstats_AbstractDeleteAction
{
    /**
     * Gets the ID of the finished banner to be deleted
     * @return int|null
     */
    public function _getId()
    {
        // Ensure it's an integer
        return xoops_getrequest('bid') ? (int)xoops_getrequest('bid') : null;
    }

    /**
     * Gets the handler for finished banner objects
     * @return Bannerstats_BannerfinishHandler|false
     */
    public function &_getHandler()
    {
        $handler = xoops_getmodulehandler('bannerfinish', 'bannerstats');
        return $handler;
    }

    /**
     * Sets up the action form for finished banner deletion
     * @return void
     */
    public function _setupActionForm(): void
    {
        $this->mActionForm = new Bannerstats_BannerfinishAdminDeleteForm();
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
        $render->setTemplateName('bannerfinish_delete.html');
        $render->setAttribute('actionForm', $this->mActionForm);

        if (is_object($this->mObject) && $this->mObject instanceof Bannerstats_BannerfinishObject) {
            $this->mObject->loadBannerclient();
        }
        $render->setAttribute('object', $this->mObject);
    }

    /**
     * Handles successful deletion.
     *
     * @param XCube_Controller $controller
     * @param XoopsUser        $xoopsUser
     * @param XCube_RenderTarget $render
     * @return void
     */
    public function executeViewSuccess(&$controller, &$xoopsUser, &$render): void
    {
        $controller->executeForward('./index.php?action=BannerfinishList');
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
        $controller->executeRedirect('./index.php?action=BannerfinishList', 1, _AD_BANNERSTATS_ERROR_DBUPDATE_FAILED);
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
        $controller->executeForward('./index.php?action=BannerfinishList');
    }
}
