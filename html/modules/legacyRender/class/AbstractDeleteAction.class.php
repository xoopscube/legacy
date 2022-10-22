<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacyRender/class/AbstractEditAction.class.php';

class LegacyRender_AbstractDeleteAction extends LegacyRender_AbstractEditAction
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
