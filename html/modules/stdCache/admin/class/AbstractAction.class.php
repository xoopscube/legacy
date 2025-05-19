<?php
/**
 * Standard cache - Module for XCL
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL/PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    2.5.0 Release: XCL
 * @link       http://github.com/xoopscube/
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class stdCache_AbstractAction extends Legacy_Action
{
    public function hasPermission(&$controller, &$xoopsUser)
    {
        return true;
    }

    public function prepare(&$controller, &$xoopsUser)
    {
        return true;
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        return STDCACHE_FRAME_VIEW_SUCCESS;
    }
}
