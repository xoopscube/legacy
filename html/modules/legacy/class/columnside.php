<?php
/**
 *
 * @package Legacy
 * @version $Id: columnside.php,v 1.3 2008/09/25 15:11:23 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/include/comment_constants.php';

class LegacyColumnsideObject extends XoopsSimpleObject
{
    public function LegacyColumnsideObject()
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
        $this->initVar('name', XOBJ_DTYPE_STRING, '', true, 255);
        $initVars=$this->mVars;
    }
}

class LegacyColumnsideHandler extends XoopsObjectHandler
{
    public $_mResults = array();
    
    public function LegacyColumnsideHandler(&$db)
    {
        self::__construct($db);
    }

    public function __construct(&$db)
    {
        $t_arr = array(
                0 => _AD_LEGACY_LANG_SIDE_BLOCK_LEFT,
                1 => _AD_LEGACY_LANG_SIDE_BLOCK_RIGHT,
                3 => _AD_LEGACY_LANG_CENTER_BLOCK_LEFT,
                4 => _AD_LEGACY_LANG_CENTER_BLOCK_RIGHT,
                5 => _AD_LEGACY_LANG_CENTER_BLOCK_CENTER
            );
            
        foreach ($t_arr as $id => $name) {
            $this->_mResults[$id] =& $this->create();
            $this->_mResults[$id]->setVar('id', $id);
            $this->_mResults[$id]->setVar('name', $name);
        }
    }
    
    public function &create()
    {
        $ret =new LegacyColumnsideObject();
        return $ret;
    }
    
    public function &get($id)
    {
        if (isset($this->_mResults[$id])) {
            return $this->_mResults[$id];
        }
        
        $ret = null;
        return $ret;
    }
    
    public function &getObjects($criteria = null, $id_as_key = false)
    {
        if ($id_as_key) {
            return $this->_mResults;
        } else {
            $ret = array();
        
            foreach (array_keys($this->_mResults) as $key) {
                $ret[] =& $this->_mResults[$key];
            }
            
            return $ret;
        }
    }
    
    public function insert(&$obj)
    {
        return false;
    }

    public function delete(&$obj)
    {
        return false;
    }
}
