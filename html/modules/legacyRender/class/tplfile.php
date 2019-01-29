<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class LegacyRenderTplfileObject extends XoopsSimpleObject
{
    /**
     * @access public
     * @todo mSource
     */
    public $Source = null;
    
    public $mOverride = null;
       
    // !Fix deprecated constructor for php 7.x
    public function __construct() 
    // public function LegacyRenderTplfileObject()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->mVars = $initVars;
            return;
        }
        $this->initVar('tpl_id', XOBJ_DTYPE_INT, '', true);
        $this->initVar('tpl_refid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('tpl_module', XOBJ_DTYPE_STRING, '', true, 25);
        $this->initVar('tpl_tplset', XOBJ_DTYPE_STRING, '', true, 50);
        $this->initVar('tpl_file', XOBJ_DTYPE_STRING, '', true, 50);
        $this->initVar('tpl_desc', XOBJ_DTYPE_STRING, '', true, 255);
        $this->initVar('tpl_lastmodified', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('tpl_lastimported', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('tpl_type', XOBJ_DTYPE_STRING, '', true, 20);
        $initVars=$this->mVars;
    }
    
    public function loadSource()
    {
        if (!is_object($this->Source)) {
            $handler =& xoops_getmodulehandler('tplsource', 'legacyRender');
            $this->Source =& $handler->get($this->get('tpl_id'));
            if (!is_object($this->Source)) {
                $this->Source =& $handler->create();
            }
        }
    }
    
    /**
     * Create the clone with source for the template set that is specified by $tplsetName.
     * 
     * @param $tplsetName string
     * @return object LegacyRenderTplfileObject
     */
    public function &createClone($tplsetName)
    {
        $this->loadSource();
        
        $obj =new LegacyRenderTplfileObject();

        $obj->set('tpl_refid', $this->get('tpl_refid'));
        $obj->set('tpl_module', $this->get('tpl_module'));

        $obj->set('tpl_tplset', $tplsetName);

        $obj->set('tpl_file', $this->get('tpl_file'));
        $obj->set('tpl_desc', $this->get('tpl_desc'));
        $obj->set('tpl_lastmodified', $this->get('tpl_lastmodified'));
        $obj->set('tpl_lastimported', $this->get('tpl_lastimported'));
        $obj->set('tpl_type', $this->get('tpl_type'));
        
        $handler =& xoops_getmodulehandler('tplsource', 'legacyRender');
        $obj->Source =& $handler->create();
        
        $obj->Source->set('tpl_source', $this->Source->get('tpl_source'));
        
        return $obj;
    }
    
    /**
     * Load override template file object by $tplset that is the name of template-set specified.
     * And, set it to mOverride.
     */
    public function loadOverride($tplset)
    {
        if ($tplset == 'default' || $this->mOverride != null) {
            return;
        }
        
        $handler =& xoops_getmodulehandler('tplfile', 'legacyRender');
        
        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('tpl_tplset', $tplset));
        $criteria->add(new Criteria('tpl_file', $this->get('tpl_file')));
        
        $objs =& $handler->getObjects($criteria);
        if (count($objs) > 0) {
            $this->mOverride =& $objs[0];
        }
    }
}

class LegacyRenderTplfileHandler extends XoopsObjectGenericHandler
{
    public $mTable = "tplfile";
    public $mPrimary = "tpl_id";
    public $mClass = "LegacyRenderTplfileObject";
    
    public function insert(&$obj, $force = false)
    {
        if (!parent::insert($obj, $force)) {
            return false;
        }
        
        $obj->loadSource();
        
        if (!is_object($obj->Source)) {
            return true;
        } else {
            $handler =& xoops_getmodulehandler('tplsource', 'legacyRender');

            if ($obj->Source->isNew()) {
                $obj->Source->set('tpl_id', $obj->get('tpl_id'));
            }

            return $handler->insert($obj->Source, $force);
        }
    }
    
    /**
     * This method load objects of two template sets by $criteria. Then, build 
     * the return value from the objects of 'default', and set the objects of
     * $tplset to object->mOverride.
     */
    public function &getObjectsWithOverride($criteria, $tplset)
    {
        $objs =& $this->getObjects($criteria);
        
        $ret = array();
        $dobjs = array();
        foreach ($objs as $obj) {
            $set = $obj->get('tpl_tplset');
            if ($set == 'default') {
                $ret[] = $obj;
            }
            if ($set == $tplset) {
                $dobjs[$obj->get('tpl_file')] = $obj;
            }
        }
        
        foreach ($ret as $obj) {
            $obj->mOverride = $dobjs[$obj->get('tpl_file')];
        }
        
        return $ret;
    }
    
    public function delete(&$obj, $force = false)
    {
        $obj->loadSource();
        
        if (is_object($obj->Source)) {
            $handler =& xoops_getmodulehandler('tplsource', 'legacyRender');
            if (!$handler->delete($obj->Source, $force)) {
                return false;
            }
        }

        return parent::delete($obj, $force);
    }
    
    /**
     * This is a kind of getObjects(). Return objects that were modified recently.
     * 
     * @param $limit int
     * @return array array of the object
     */
    public function &getRecentModifyFile($limit = 10)
    {
        $criteria = new Criteria('tpl_id', 0, '>');

        $criteria->setLimit($limit);

        $criteria->setSort('tpl_lastmodified');
        $criteria->setOrder('DESC');
        
        $objs =& $this->getObjects($criteria);
        
        return $objs;
    }
    
    /**
     * This is a kind of getObjects(). Call getObjects() by 5 parameters and return
     * the result. Parameters are guaranteed Type Safe because these are used by
     * getObjects() for XoopsSimpleObject.
     * 
     * @param $tplsetName string
     * @param $type       string
     * @param $refId      int
     * @param $module     string
     * @param $file       string
     * @return array      array of the object.
     */
    public function &find($tplsetName, $type = null, $refId = null, $module = null, $file = null)
    {
        $criteria =new CriteriaCompo();
        $criteria->add(new Criteria('tpl_tplset', $tplsetName));
        if ($type != null) {
            $criteria->add(new Criteria('tpl_type', $type));
        }
        if ($refId != null) {
            $criteria->add(new Criteria('tpl_refid', $refId));
        }
        if ($module != null) {
            $criteria->add(new Criteria('tpl_module', $module));
        }
        if ($file != null) {
            $criteria->add(new Criteria('tpl_file', $file));
        }
        
        $objs =& $this->getObjects($criteria);
        return $objs;
    }
}
