<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/user/class/AbstractEditAction.class.php';

class User_AbstractDeleteAction extends User_AbstractEditAction
{
    public function isEnableCreate()
    {
        return false;
    }

    public function _doExecute()
    {
        return $this->mObjectHandler->delete($this->mObject);
    }
}
