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

require_once XOOPS_MODULE_PATH . '/profile/class/AbstractEditAction.class.php';

class Profile_DataEditAction extends Profile_AbstractEditAction
{
    public $mFields = [];
    public $mOptions = [];

    /**
     * @protected
     */
    public function _getId()
    {
        if ($this->mRoot->mContext->mXoopsUser) {
            return $this->mRoot->mContext->mXoopsUser->get('uid');
        } else {
            $this->mRoot->mController->executeRedirect(XOOPS_URL . '/user.php', 1, _MD_PROFILE_ERROR_REGISTER_REQUIRED);
        }
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
        // $this->mActionForm =new Profile_DataEditForm();
        $this->mActionForm =& $this->mAsset->create('form', 'edit_data');
        $this->mActionForm->prepare();
    }

    /**
     * @public
     */
    public function prepare()
    {
        parent::prepare();
        $this->mObject->set('uid', $this->_getId());

        $defHandler =& xoops_getmodulehandler('definitions');
        $this->mFields =& $defHandler->getFields4DataEdit();
    }

    /**
     * @public
     * @param $render
     */
    public function executeViewInput(&$render)
    {
        $render->setTemplateName('profile_data_edit.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);
        $render->setAttribute('fields', $this->mFields);
        $headerScript = $this->mRoot->mContext->getAttribute('headerScript');
        $headerScript->addScript('$(".datepicker").each(function(){$(this).datepicker({dateFormat: "'._JSDATEPICKSTRING.'"});});');
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
