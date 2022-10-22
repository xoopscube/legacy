<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/user/class/AbstractListAction.class.php';
require_once XOOPS_MODULE_PATH . '/user/admin/forms/GroupFilterForm.class.php';

class User_GroupListAction extends User_AbstractListAction
{
    public function &_getHandler()
    {
        $handler =& xoops_getmodulehandler('groups');
        return $handler;
    }

    public function &_getFilterForm()
    {
        $filter =new User_GroupFilterForm($this->_getPageNavi(), $this->_getHandler());
        return $filter;
    }

    public function _getBaseUrl()
    {
        return './index.php?action=GroupList';
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('group_list.html');
        $render->setAttribute('objects', $this->mObjects);
        $render->setAttribute('pageNavi', $this->mFilter->mNavi);
    }
}
