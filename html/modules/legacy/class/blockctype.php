<?php
/**
 *
 * @package Legacy
 * @version $Id: blockctype.php,v 1.3 2008/09/25 15:11:21 kilica Exp $
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class LegacyBlockctypeObject extends XoopsSimpleObject
{
    public function __construct()
    {
        $this->initVar('type', XOBJ_DTYPE_STRING, '', true);
        $this->initVar('label', XOBJ_DTYPE_STRING, '', true, 255);
    }
}

class LegacyBlockctypeHandler extends XoopsObjectHandler
{
    public $_mResults = [];

    public function __construct(&$db)
    {
        $t_arr = [
                'H' => _AD_LEGACY_LANG_CTYPE_HTML,
                'P' => _AD_LEGACY_LANG_CTYPE_PHP,
                'S' => _AD_LEGACY_LANG_CTYPE_WITH_SMILIES,
                'T' => _AD_LEGACY_LANG_CTYPE_WITHOUT_SMILIES
        ];

        foreach ($t_arr as $id => $name) {
            $this->_mResults[$id] =& $this->create();
            $this->_mResults[$id]->setVar('type', $id);
            $this->_mResults[$id]->setVar('label', $name);
        }
    }

    public function &create()
    {
        $ret =new LegacyBlockctypeObject();
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
            $ret = [];

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
