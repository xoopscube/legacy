<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . "/profile/class/AbstractViewAction.class.php";

class Profile_DataViewAction extends Profile_AbstractViewAction
{
    public $mFieldArr = array();

    /**
     * @public
     */
    public function _getId()
    {
        return intval(xoops_getrequest('uid'));
    }

    /**
     * @public
     */
    public function &_getHandler()
    {
        $handler =& $this->mAsset->load('handler', "data");
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
     */
    public function executeViewSuccess(&$render)
    {
        $render->setTemplateName("profile_data_view.html");
        $render->setAttribute('object', $this->mObject);
        $render->setAttribute('fields', $this->mFieldArr);
    }

    /**
     * @public
     */
    public function executeViewError(&$render)
    {
        $this->mRoot->mController->executeRedirect("./index.php?action=DataList", 1, _MD_PROFILE_ERROR_CONTENT_IS_NOT_FOUND);
    }
}
