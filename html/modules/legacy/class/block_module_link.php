<?php
/**
 *
 * @package Legacy
 * @version $Id: block_module_link.php,v 1.3 2008/09/25 15:11:21 kilica Exp $
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class LegacyBlock_module_linkObject extends XoopsSimpleObject
{
    public function __construct()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->mVars = $initVars;
            return;
        }
        $this->initVar('block_id', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('module_id', XOBJ_DTYPE_INT, '0', true);
        $initVars=$this->mVars;
    }
}

class LegacyBlock_module_linkHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'block_module_link';
    public $mPrimary = 'block_id';
    public $mClass = 'LegacyBlock_module_linkObject';
}
