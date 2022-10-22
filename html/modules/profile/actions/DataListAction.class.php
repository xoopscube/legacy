<?php
/**
 * @package    profile
 * @version    2.3.1
 * @author     Original Author Kilica
 * @copyright  2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/profile/class/AbstractListAction.class.php';

class Profile_DataListAction extends Profile_AbstractListAction
{
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
    public function &_getFilterForm()
    {
        // $filter =new Profile_DataFilterForm();
        $filter =& $this->mAsset->create('filter', 'data');
        $filter->prepare($this->_getPageNavi(), $this->_getHandler());
        return $filter;
    }

    /**
     * @protected
     */
    public function _getBaseUrl()
    {
        return './index.php?action=DataList';
    }

    /**
     * @public
     * @param $render
     */
    public function executeViewIndex(&$render)
    {
        $render->setTemplateName('profile_data_list.html');
        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);
        $handler = Legacy_Utils::getModuleHandler('definitions', 'profile');
        $render->setAttribute('definitions', $handler->getFields4DataShow(Legacy_Utils::getUid()));
    }
}
