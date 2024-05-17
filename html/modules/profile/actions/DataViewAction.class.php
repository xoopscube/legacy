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

require_once XOOPS_MODULE_PATH . '/profile/class/AbstractViewAction.class.php';

class Profile_DataViewAction extends Profile_AbstractViewAction
{
    public $mFieldArr = [];

    /**
     * @public
     */
    public function _getId()
    {
        return (int)xoops_getrequest('uid');
    }

    /**
     * @public
     */
    public function &_getHandler()
    {
        $handler =& $this->mAsset->load('handler', 'data');
        return $handler;
    }

    public function prepare()
    {
        parent::prepare();
        $dHandler =& xoops_getmodulehandler('definitions');
        $this->mFieldArr = $dHandler->getFields4DataShow($this->_getId());
    }

    /**
     * @public
     * @param $render
     */
    public function executeViewSuccess(&$render)
    {
        $render->setTemplateName('profile_data_view.html');
        $render->setAttribute('object', $this->mObject);
        $render->setAttribute('fields', $this->mFieldArr);
    }

    /**
     * @public
     * @param $render
     */
    public function executeViewError(&$render)
    {
        $this->mRoot->mController->executeRedirect('./index.php?action=DataList', 1, _MD_PROFILE_ERROR_CONTENT_IS_NOT_FOUND);
    }
}
