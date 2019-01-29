<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class XoopsTimezone extends XoopsObject
{
    public function __construct()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->vars = $initVars;
            return;
        }
        $this->initVar('offset', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('zone_name', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $initVars = $this->vars;
    }
    public function XoopsTimezone()
    {
        return self::__construct();
    }
}

class XoopsTimezoneHandler extends XoopsObjectHandler
{
    public $_mResult;
    
    public function __construct(&$db)
    {
        parent::__construct($db);

        $root =& XCube_Root::getSingleton();

        //
        // Because abstract language style is not decided, we load directly. But we must fix.
        //
        $root->mLanguageManager->loadPageTypeMessageCatalog('timezone');

        //
        // This handler not connects to database.
        //
        $this->_mResult = array(
            "-12"  => _TZ_GMTM12,
            "-11"  => _TZ_GMTM11,
            "-10"  => _TZ_GMTM10,
            "-9"   => _TZ_GMTM9,
            "-8"   => _TZ_GMTM8,
            "-7"   => _TZ_GMTM7,
            "-6"   => _TZ_GMTM6,
            "-5"   => _TZ_GMTM5,
            "-4.5" => _TZ_GMTM45,
            "-4"   => _TZ_GMTM4,
            "-3.5" => _TZ_GMTM35,
            "-3"   => _TZ_GMTM3,
            "-2"   => _TZ_GMTM2,
            "-1"   => _TZ_GMTM1,
            "0"    => _TZ_GMT0,
            "1"    => _TZ_GMTP1,
            "2"    => _TZ_GMTP2,
            "3"    => _TZ_GMTP3,
            "3.5"  => _TZ_GMTP35,
            "4"    => _TZ_GMTP4,
            "4.5"  => _TZ_GMTP45,
            "5"    => _TZ_GMTP5,
            "5.5"  => _TZ_GMTP55,
            "5.75" => _TZ_GMTP575,
            "6"    => _TZ_GMTP6,
            "6.5"  => _TZ_GMTP65,
            "7"    => _TZ_GMTP7,
            "8"    => _TZ_GMTP8,
            "9"    => _TZ_GMTP9,
            "9.5"  => _TZ_GMTP95,
            "10"   => _TZ_GMTP10,
            "11"   => _TZ_GMTP11,
            "12"   => _TZ_GMTP12,
            "13"   => _TZ_GMTP13
        );
    }
    public function XoopsTimezoneHandler(&$db)
    {
        return self::__construct($db);
    }
    
    public function &create()
    {
        $ret =new XoopsTimezone();
        return $ret;
    }
    
    public function &get($offset)
    {
        $ret = null;
        
        foreach ($this->_mResult as $index => $zone_name) {
            if ((float)$index == (float)$offset) {
                $obj =new XoopsTimezone();
                $obj->set('offset', $index);
                $obj->set('zone_name', $zone_name);

                return $obj;
            }
        }
        
        return $ret;
    }

    public function &getObjects($criteria = null, $key_as_id = false)
    {
        $ret = array();
        
        foreach ($this->_mResult as $offset => $zone_name) {
            $obj =new XoopsTimezone();
            $obj->setVar('offset', $offset);
            $obj->setVar('zone_name', $zone_name);
            if ($key_as_id) {
                $ret[$offset] =& $obj;
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
