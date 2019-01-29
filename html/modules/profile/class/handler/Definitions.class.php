<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Profile_DefinitionsObject extends XoopsSimpleObject
{
    public $mFieldType = null;    //Profile_FieldType

    public function Profile_DefinitionsObject()
    {
        self::__construct();
    }

    /**
     * @public
     */
    public function __construct()
    {
        $this->initVar('field_id', XOBJ_DTYPE_INT, '', false);
        $this->initVar('field_name', XOBJ_DTYPE_STRING, '', false, 32);
        $this->initVar('label', XOBJ_DTYPE_STRING, '', false, 255);
        $this->initVar('type', XOBJ_DTYPE_STRING, '', false, 32);
        $this->initVar('validation', XOBJ_DTYPE_STRING, '', false, 255);
        $this->initVar('required', XOBJ_DTYPE_BOOL, 0, false);
        $this->initVar('show_form', XOBJ_DTYPE_BOOL, 1, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 10, false);
        $this->initVar('description', XOBJ_DTYPE_TEXT, '', false);
        $this->initVar('access', XOBJ_DTYPE_TEXT, '', false);
        $this->initVar('options', XOBJ_DTYPE_TEXT, '', false);
    }

    /**
     * @public
     */
    public function getFields()
    {
        $cri = new Criteria('1', '1');
        $cri->setSort('weight');
    
        return $this->getObjects($cri);
    }

    public function setFieldTypeObject()
    {
        if (! $this->mFieldType) {
            $className = 'Profile_FieldType'.ucfirst($this->get('type'));
            $this->mFieldType = new $className();
        }
    }

    public function getDefault()
    {
        return $this->mFieldType->getDefault($this->get('options'));
    }
}

class Profile_DefinitionsHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'profile_definitions';
    public $mPrimary = 'field_id';
    public $mClass = 'Profile_DefinitionsObject';

    /**
     * @public
     */
    public function getFields4DataEdit()
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('show_form', '1'));
        $criteria->setSort('weight');
    
        return $this->getObjects($criteria);
    }

    /**
     * @public
     */
    public function getFields4DataShow($uid=0)
    {
        $uid = ($uid>0) ? $uid : Legacy_Utils::getUid();
        $lHandler =& xoops_getmodulehandler('groups_users_link', 'user');
    
        $criteria = new CriteriaCompo();
        $criteria->setSort('weight');
        $fieldArr = $this->getObjects($criteria);
        foreach (array_keys($fieldArr) as $keyF) {
            $flag = false;
            $accessArr = explode(',', $fieldArr[$keyF]->get('access'));
            if ($uid===0) {    //guest
                if (in_array(XOOPS_GROUP_ANONYMOUS, $accessArr)) {
                    $flag = true;
                }
            } else {
                foreach (array_keys($accessArr) as $keyA) {
                    if ($lHandler->isUserOfGroup($uid, $accessArr[$keyA])) {
                        $flag = true;
                    }
                }
            }
            if (! $flag) {
                unset($fieldArr[$keyF]);
            }
        }
    
        return $fieldArr;
    }

    /**
     * @public
     */
    public function insert(&$obj, $force = false)
    {
        global $xoopsDB;
        $obj->setFieldTypeObject();
        if ($obj->isNew()) {
            $sql = 'ALTER TABLE '. $xoopsDB->prefix('profile_data') .' ADD `'. $obj->get('field_name') .'` '. $obj->mFieldType->getTableQuery();
            $xoopsDB->query($sql);
        } else {
            $oldObj = $this->get($obj->get('field_id'));
            if ($oldObj->get('field_name')!=$obj->get('field_name')) {
                $sql = 'ALTER TABLE '. $xoopsDB->prefix('profile_data') .' CHANGE `'. $oldObj->get('field_name') .'` `'. $obj->get('field_name') .'` '. $oldObj->mFieldType->getTableQuery();
                $xoopsDB->query($sql);
            }
        }
    
        return parent::insert($obj, $force);
    }

    /**
     * @public
     */
    public function delete(&$obj, $force = false)
    {
        global $xoopsDB;
        $sql = 'ALTER TABLE '. $xoopsDB->prefix('profile_data') .' DROP `'. $obj->get('field_name') .'`';
        $xoopsDB->query($sql);
        
        return parent::delete($obj, $force);
    }

    public function getDefinitions($show_form=true)
    {
        $criteria = new CriteriaCompo();
        $criteria->setSort('weight', 'ASC');
        if ($show_form==true) {
            $criteria->add(new Criteria('show_form', 1));
        }
        $definitions = $this->getObjects($criteria);
        $defArr = array();
        foreach ($definitions as $def) {
            $defArr[$def->get('field_name')] = $def;
        }
        return $defArr;
    }

    /**
     * @public
     */
    public function getTypeList()
    {
        return array(
            Profile_FormType::STRING,
            Profile_FormType::TEXT,
            Profile_FormType::INT,
            Profile_FormType::FLOAT,
            Profile_FormType::DATE,
            Profile_FormType::CHECKBOX,
            Profile_FormType::SELECTBOX,
            Profile_FormType::URI
        );
    }

    public static function getReservedNameList()
    {
        return array('uid','name','uname','email','url','user_avatar','user_regdate','user_icq','user_from','user_sig','user_viewemail','actkey','user_aim','user_yim','user_msnm','pass','posts','attachsig','rank','level','theme','timezone_offset','last_login','umode','uorder','notify_method','notify_mode','user_occ','bio','user_intrest','user_mailok', 'user_name');
    }

    /**
     * @public
     */
    public function getValidationList()
    {
        return array("email");
    }

    public function &getObjects($criteria = null, $limit = null, $start = null, $id_as_key = false)
    {
        $objs = parent::getObjects($criteria, $limit, $start, $id_as_key);
    
        foreach (array_keys($objs) as $key) {
            $objs[$key]->setFieldTypeObject();
        }
        return $objs;
    }
}
