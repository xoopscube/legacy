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
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/forms/BannerclientAdminDeleteForm.class.php';

class Bannerstats_BannerclientDeleteAction extends Bannerstats_AbstractDeleteAction
{
    public function _getId()
    {
        return xoops_getrequest('cid');
    }

    /**
     * Gets the handler for banner client objects
     * @return Bannerstats_BannerclientHandler|false
     */
    public function &_getHandler()
    {
        $handler = xoops_getmodulehandler('bannerclient', 'bannerstats');
        return $handler;
    }

    public function _setupActionForm()
    {
        $this->mActionForm = new Bannerstats_BannerclientAdminDeleteForm();
        $this->mActionForm->prepare();
    }

    /**
     * Overrides the parent _doExecute to also delete associated banners
     * @return bool True on successful deletion of client and associated data
     */
    public function _doExecute(): bool
    {
        if (!is_object($this->mObject) || !($this->mObject instanceof Bannerstats_BannerclientObject)) {
            $this->mActionForm->addErrorMessage(_AD_BANNERSTATS_ERROR_OBJECT_IS_NOT_EXIST);
            return false;
        }

        $clientToDelete = $this->mObject;
        $clientId = $clientToDelete->get('cid');

        // Get handlers for banner and bannerfinish
        $bannerHandler = xoops_getmodulehandler('banner', 'bannerstats');
        $bannerfinishHandler = xoops_getmodulehandler('bannerfinish', 'bannerstats');

        if (!$bannerHandler || !$bannerfinishHandler) {
            $this->mActionForm->addErrorMessage(_AD_BANNERSTATS_ERROR_HANDLER_NOT_FOUND);
            error_log("Bannerstats_BannerclientDeleteAction: Failed to get banner or bannerfinish handler.");
            return false;
        }

        // Create criteria for finding associated banners
        $criteria = new CriteriaCompo(new Criteria('cid', $clientId));

        // Delete active banners associated with this client
        if (!$bannerHandler->deleteAll($criteria, true)) {
            $this->mActionForm->addErrorMessage(
                sprintf(
                    _AD_BANNERSTATS_ERROR_DELETE_ACTIVE_FAILED,
                    $clientId
                )
            );
            return false;
        }

        // Delete finished banners associated with this client
        if (!$bannerfinishHandler->deleteAll($criteria, true)) {
            $this->mActionForm->addErrorMessage(
                sprintf(
                    _AD_BANNERSTATS_ERROR_DELETE_FINISHED_FAILED,
                    $clientId
                )
            );
            return false;
        }

        if (parent::_doExecute()) {
            return true;
        } else {
            return false;
        }
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('bannerclient_delete.html');
        $render->setAttribute('actionForm', $this->mActionForm);

        // Load counts for display on the confirmation page
        if (is_object($this->mObject) && ($this->mObject instanceof Bannerstats_BannerclientObject)) {

            $this->mObject->loadBannerCount();
            $this->mObject->loadFinishBannerCount();
        }
        $render->setAttribute('object', $this->mObject);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=BannerclientList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {

        if ($this->mActionForm->hasError()) {

            $controller->executeRedirect('./index.php?action=BannerclientList', 1, _AD_BANNERSTATS_ERROR_DELETE_CLIENT_FAILED);
        } else {
 
            $controller->executeRedirect('./index.php?action=BannerclientList', 1, _AD_BANNERSTATS_ERROR_DBUPDATE_FAILED);
        }
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=BannerclientList');
    }
}
