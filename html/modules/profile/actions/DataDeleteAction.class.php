<?php
/**
 * @package    profile
 * @version    2.4.0
 * @author     Original Author Kilica
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/profile/class/AbstractDeleteAction.class.php';

class Profile_DataDeleteAction extends Profile_AbstractDeleteAction
{
    /**
     * @protected
     */
    public function _getId()
    {
        return (int)xoops_getrequest('uid');
    }

    /**
     * @protected
     */
    public function &_getHandler()
    {
        $handler =& $this->mAsset->load('handler', 'data');
        return $handler;
    }

    /**
     * @protected
     */
    public function _setupActionForm()
    {
        // $this->mActionForm =new Profile_DataDeleteForm();
        $this->mActionForm =& $this->mAsset->create('form', 'delete_data');
        $this->mActionForm->prepare();
    }

    /**
     * @public
     * @param $render
     */
    public function executeViewInput(&$render)
    {
        $render->setTemplateName('profile_data_delete.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        #cubson::lazy_load('data', $this->mObject);
        $render->setAttribute('object', $this->mObject);
    }

    /**
     * @public
     * @param $render
     */
    public function executeViewSuccess(&$render)
    {
        $this->mRoot->mController->executeForward('./index.php?action=DataList');
    }

    /**
     * @public
     * @param $render
     */
    public function executeViewError(&$render)
    {
        $this->mRoot->mController->executeRedirect('./index.php?action=DataList', 1, _MD_PROFILE_ERROR_DBUPDATE_FAILED);
    }

    /**
     * @public
     * @param $render
     */
    public function executeViewCancel(&$render)
    {
        $this->mRoot->mController->executeForward('./index.php?action=DataList');
    }
}
