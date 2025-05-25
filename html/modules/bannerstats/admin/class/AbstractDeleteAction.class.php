<?php
/**
 * Bannerstats - Module for XCL
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/Action.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/AbstractEditAction.class.php';

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
