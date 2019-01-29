<?php
/**
 *
 * @package Legacy
 * @version $Id: smiles.php,v 1.3 2008/09/25 15:11:25 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class LegacySmilesObject extends XoopsSimpleObject
{
    public function LegacySmilesObject()
    {
        self::__construct();
    }

    public function __construct()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->mVars = $initVars;
            return;
        }
        $this->initVar('id', XOBJ_DTYPE_INT, '', true);
        $this->initVar('code', XOBJ_DTYPE_STRING, '', true, 50);
        $this->initVar('smile_url', XOBJ_DTYPE_STRING, '', true, 100);
        $this->initVar('emotion', XOBJ_DTYPE_STRING, '', true, 75);
        $this->initVar('display', XOBJ_DTYPE_BOOL, '0', true);
        $initVars=$this->mVars;
    }
}

class LegacySmilesHandler extends XoopsObjectGenericHandler
{
    public $mTable = "smiles";
    public $mPrimary = "id";
    public $mClass = "LegacySmilesObject";
    
    public function delete(&$obj, $force=null)
    {
        @unlink(XOOPS_UPLOAD_PATH . "/" . $obj->get('smile_url'));
        
        return parent::delete($obj, $force);
    }
}
