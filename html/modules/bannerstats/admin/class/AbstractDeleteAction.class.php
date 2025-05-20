<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/Action.class.php'; // Or whatever you name the file containing Bannerstats_Action

class Bannerstats_AbstractDeleteAction extends Bannerstats_AbstractEditAction
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
