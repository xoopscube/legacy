<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class XoopsCachetime extends XoopsObject
{
    public function __construct()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->vars = $initVars;
            return;
        }
        $this->initVar('cachetime', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('label', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $initVars = $this->vars;
    }
    public function XoopsCachetime()
    {
        return self::__construct();
    }
}

class XoopsCachetimeHandler extends XoopsObjectHandler
{
    public $_mResult;
    
    public function __construct(&$db)
    {
        parent::__construct($db);

        //
        // This handler not connects to database.
        //
        $this->_mResult = array(
            "0"       => _NOCACHE,
            "30"      => sprintf(_SECONDS, 30),
            "60"      => _MINUTE,
            "300"     => sprintf(_MINUTES, 5),
            "1800"    => sprintf(_MINUTES, 30),
            "3600"    => _HOUR,
            "18000"   => sprintf(_HOURS, 5),
            "86400"   => _DAY,
            "259200"  => sprintf(_DAYS, 3),
            "604800"  => _WEEK,
            "2592000" => _MONTH
        );
    }
    public function XoopsCachetimeHandler(&$db)
    {
        return self::__construct($db);
    }
    
    public function &create()
    {
        $ret =new XoopsCachetime();
        return $ret;
    }
    
    public function &get($cachetime)
    {
        if (isset($this->_mResult[$cachetime])) {
            $obj =new XoopsCachetime();
            $obj->setVar('cachetime', $cachetime);
            $obj->setVar('label', $this->_mResult[$cachetime]);

            return $obj;
        }
        
        $ret = null;
        return $ret;
    }

    public function &getObjects($criteria = null, $key_as_id = false)
    {
        $ret = array();
        
        foreach ($this->_mResult as $cachetime => $label) {
            $obj =new XoopsCachetime();
            $obj->setVar('cachetime', $cachetime);
            $obj->setVar('label', $label);
            if ($key_as_id) {
                $ret[$cachetime] =& $obj;
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
