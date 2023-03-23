<?php
/**
 * NotifyCancelAction.class.php
 * @package    Legacy
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Legacy_NotifyCancelAction extends Legacy_Action
{
    public function getDefaultView(&$contoller, &$xoopsUser)
    {
        $contoller->executeForward(XOOPS_URL . '/');
    }

    public function execute(&$contoller, &$xoopsUser)
    {
        $contoller->executeForward(XOOPS_URL . '/');
    }
}
