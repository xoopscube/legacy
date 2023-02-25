<?php
/**
 * @package    profile
 * @version    2.3.1
 * @author     Other Authors Nuno Luciano (aka gigamaster), 2020, XCL PHP7
 * @author     Original Author Kilica
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/profile/class/AbstractViewAction.class.php';

class Profile_Admin_DefinitionsViewAction extends Profile_AbstractViewAction
{
    /**
     * @public
     */
    public function _getId()
    {
        return (int)xoops_getrequest('field_id');
    }

    /**
     * @public
     */
    public function &_getHandler()
    {
        $handler =& $this->mAsset->load('handler', 'definitions');
        return $handler;
    }

    /**
     * @public
     * @param $controller
     * @param $render
     */
    public function executeViewSuccess(&$controller, &$render)
    {
        $render->setTemplateName('definitions_view.html');
        #cubson::lazy_load('definitions', $this->mObject);
        $render->setAttribute('object', $this->mObject);
    }

    /**
     * @public
     * @param $render
     */
    public function executeViewError(&$render)
    {
        $this->mRoot->mController->executeRedirect('./index.php?action=DefinitionsList', 1, _MD_PROFILE_ERROR_CONTENT_IS_NOT_FOUND);
    }
}
