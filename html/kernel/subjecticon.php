<?php
/**
 * timezone class object
 * @package    kernel
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito
 * @copyright  (c) 2000-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */


if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class XoopsSubjecticon extends XoopsObject
{
    public function __construct()
    {
        $this->initVar('filename', XOBJ_DTYPE_TXTBOX, null, true, 191);
    }
}

class XoopsSubjecticonHandler extends XoopsObjectHandler
{
    public $_mResult;

    public function __construct(&$db)
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
        $this->_mResult =& XoopsLists::getSubjectsList();
    }

    public function &create()
    {
        $ret =new XoopsSubjecticon();
        return $ret;
    }

    public function &get($filename)
    {
        if (isset($this->_mResult[$filename])) {
            $obj =new XoopsSubjecticon();
            $obj->setVar('filename', $this->_mResult[$filename]);

            return $obj;
        }

        $ret = null;
        return $ret;
    }

    public function &getObjects($criteria = null, $key_as_id = false)
    {
        $ret = [];

        foreach ($this->_mResult as $filename => $value) {
            $obj =new XoopsSubjecticon();
            $obj->setVar('filename', $filename);
            if ($key_as_id) {
                $ret[$filename] =& $obj;
            } else {
                $ret[] =& $obj;
            }
            unset($obj);
        }

        return $ret;
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
