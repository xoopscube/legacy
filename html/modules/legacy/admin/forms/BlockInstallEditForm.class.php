<?php
/**
 * BlockInstallEditForm.class.php
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/modules/legacy/admin/forms/BlockEditForm.class.php';

class Legacy_BlockInstallEditForm extends Legacy_BlockEditForm
{
    public function getTokenName()
    {
        return 'module.legacy.BlockInstallEditForm.TOKEN' . $this->get('bid');
    }

    public function update(&$obj)
    {
        parent::update($obj);
        $obj->set('visible', true);
    }
}
