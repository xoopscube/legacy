<?php
/**
 * @package    profile
 * @version    XCL 2.4.0
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Original Author Kilica
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/profile/class/AbstractListAction.class.php';

class Profile_Admin_DefinitionsListAction extends Profile_AbstractListAction
{
    /**
     * @protected
     */
    public function &_getHandler()
    {
        $handler =& $this->mAsset->load('handler', 'definitions');
        return $handler;
    }

    /**
     * @protected
     */
    public function &_getFilterForm()
    {
        // $filter =new Profile_Admin_DefinitionsFilterForm();
        $filter =& $this->mAsset->create('filter', 'admin.definitions');
        $filter->prepare($this->_getPageNavi(), $this->_getHandler());
        return $filter;
    }

    /**
     * @protected
     */

    public function &_getBaseUrl()
    {
        $baseUrl = './index.php?action=DefinitionsList';
        return $baseUrl;
    }

    /**
     * @public
     * @param $render
     */
    public function executeViewIndex(&$render)
    {
        $render->setTemplateName('definitions_list.html');
        #cubson::lazy_load_array('definitions', $this->mObjects);
        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);
    }
}
