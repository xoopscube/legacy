<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Profile_DataObject extends XoopsSimpleObject
{
    public $mDef = null;

    public function Profile_DataObject()
    {
        self::__construct();
    }

    /**
     * @public
     */
    public function __construct()
    {
        $handler = Legacy_Utils::getModuleHandler('definitions', 'profile');
        $this->mDef = $handler->getDefinitions(false);
    
        $this->initVar('uid', XOBJ_DTYPE_INT, '', false);
        foreach (array_keys($this->mDef) as $key) {
            $this->mDef[$key]->mFieldType->setInitVar($this, $this->mDef[$key]->getShow('field_name'), $this->mDef[$key]->getDefault());
        }
    }

    /**
     * showField
     * 
     * @param	string	$key
     * @param	Enum  $option
     * 
     * @return	mixed
    **/
    public function showField(/*** string ***/ $key, /*** Enum ***/ $option=2)
    {
        return $this->mDef[$key]->mFieldType->showField($this, $key, $option);
    }

    /**
     * setField
     * 
     * @param	string	$key
     * @param	mixed	$value
     * 
     * @return	void
    **/
    public function setField(/*** string ***/ $key, /*** mixed ***/ $value)
    {
        $type = $this->mDef[$key]->get('type');
        switch ($type) {
            case Profile_FormType::TEXT:
                if ($this->mDef[$key]->get('options')=='html') {
                    $value = XCube_Root::getSingleton()->mTextFilter->purifyHtml($value);
                }
                $this->set($key, $value);
                break;
            case Profile_FormType::DATE:
                $dateArr = explode('-', $value);
                $date = mktime(0, 0, 0, $dateArr[1], $dateArr[2], $dateArr[0]);
                $this->set($key, $date);
                break;
            default:
                $this->set($key, $value);
                break;
        }
        
        return $value;
    }
}

class Profile_DataHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'profile_data';
    public $mPrimary = 'uid';
    public $mClass = 'Profile_DataObject';

    public function insert(&$obj, $force = false)
    {
        if (count($obj->mDef)===0) {
            return true;
        }
        return parent::insert($obj, $force = false);
    }
}
