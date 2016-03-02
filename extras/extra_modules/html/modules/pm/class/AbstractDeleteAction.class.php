<?php
/**
 * @package pm
 * @version $Id: AbstractDeleteAction.class.php,v 1.1 2007/05/15 02:35:26 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . "/pm/class/AbstractEditAction.class.php";

class Pm_AbstractDeleteAction extends Pm_AbstractEditAction
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
