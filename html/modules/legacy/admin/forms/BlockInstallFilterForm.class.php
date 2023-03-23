<?php
/**
 * BlockInstallFilterForm.class.php
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

require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/BlockFilterForm.class.php';

class Legacy_BlockInstallFilterForm extends Legacy_BlockFilterForm
{
    public function _getVisible()
    {
        return 0;
    }
}
