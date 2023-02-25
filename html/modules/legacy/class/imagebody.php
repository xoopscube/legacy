<?php
/**
 *
 * @package Legacy
 * @version $Id: imagebody.php,v 1.3 2008/09/25 15:11:24 kilica Exp $
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class LegacyImagebodyObject extends XoopsSimpleObject
{
    public function __construct()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->mVars = $initVars;
            return;
        }
        $this->initVar('image_id', XOBJ_DTYPE_INT, '', false);
        $this->initVar('image_body', XOBJ_DTYPE_TEXT, '', true);
        $initVars=$this->mVars;
    }
}

class LegacyImagebodyHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'imagebody';
    public $mPrimary = 'image_id';
    public $mClass = 'LegacyImagebodyObject';
}
