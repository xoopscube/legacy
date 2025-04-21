<?php
/**
 *  BlockUninstallForm.class.php
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacy/admin/forms/CustomBlockDeleteForm.class.php';

class Legacy_BlockUninstallForm extends Legacy_CustomBlockDeleteForm
{
    public function getTokenName()
    {
        return 'module.legacy.BlockUninstallForm.TOKEN' . $this->get('bid');
    }

    public function update(&$obj)
    {
        parent::update($obj);
        $obj->set('last_modified', time());
        $obj->set('visible', false);
    }
}
