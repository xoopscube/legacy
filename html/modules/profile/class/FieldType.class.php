<?php
/**
 * @file
 * @package profile
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

class Profile_FormType
{
    const STRING = 'string';
    const TEXT = 'text';
    const INT = 'int';
    const FLOAT = 'float';
    const DATE = 'date';
    const CHECKBOX = 'checkbox';
    const SELECTBOX = 'selectbox';
    const URI = 'uri';
}

interface Profile_iFieldType
{
    public function showField(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** Profile_ActionType ***/ $option=0);
    public function getTableQuery();
    public function setInitVar(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** string ***/ $default);
    public function getDefault(/*** string ***/ $option);
    public function getXObjType();
    public function getFormPropertyClass();
//  public function getServiceType();
    public function getSearchFormString(/*** string ***/ $key);
    public function getOption(/*** Profile_DefinitionsObject ***/ $obj, /*** string ***/ $key=null);
}


/** --------------------------------------------------------
 *  String Type
**/
class Profile_FieldTypeString implements Profile_iFieldType
{
    const TYPE = 'string';

    public function showField(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** Profile_ActionType ***/ $option=0)
    {
        if($option==Profile_ActionType::NONE||$option==Profile_ActionType::VIEW){
            $value = $obj->getShow($key);
        }
        elseif($option==Profile_ActionType::EDIT){
            $value = $obj->get($key);
        }
    
        return $value;
    }

    public function getTableQuery()
    {
        return 'VARCHAR(255) NOT NULL';
    }

    public function setInitVar(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** string ***/ $default)
    {
        $obj->initVar($key, $this->getXObjType(), $default, false, 255);
    }

    public function getDefault(/*** string ***/ $option)
    {
        return isset($option) ? $option : '';
    }

    public function getXObjType()
    {
        return XOBJ_DTYPE_STRING;
    }

    public function getFormPropertyClass()
    {
        return 'XCube_StringProperty';
    }

    public function getSearchFormString(/*** string ***/ $key)
    {
        return '<dt></dt><dd><input type="text" value="" name="<{$key}>" /></dd>';
    }

    public function getOption(/*** Profile_DefinitionsObject ***/ $obj, /*** string ***/ $key=null)
    {
        return $obj->get('options');
    }
}


/** --------------------------------------------------------
 *  Text Type
**/
class Profile_FieldTypeText implements Profile_iFieldType
{
    const TYPE = 'text';

    public function showField(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** Profile_ActionType ***/ $option=0)
    {
        if($option==Profile_ActionType::NONE||$option==Profile_ActionType::VIEW){
        	switch($obj->get('option')){
        	case 'html':
        	case 'none':
	            $value = $obj->get($key);
	            break;
	        case 'bbcode':
	        default:
	        	$value = $obj->getShow($key);
	        	break;
	        }
        }
        elseif($option==Profile_ActionType::EDIT){
            $value = $obj->get($key);
        }
        return $value;
    }

    public function getTableQuery()
    {
        return 'text NOT NULL';
    }

    public function setInitVar(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** string ***/ $default)
    {
        $obj->initVar($key, $this->getXObjType(), $default, false);
    }

    public function getDefault(/*** string ***/ $option)
    {
        return '';
    }

    public function getXObjType()
    {
        return XOBJ_DTYPE_TEXT;
    }

    public function getFormPropertyClass()
    {
        return 'XCube_TextProperty';
    }

    public function getSearchFormString(/*** string ***/ $key)
    {
        return '<dt></dt><dd><input type="text" value="" name="<{$key}>" /> <input type="text" value="" name="<{$key}>" /></dd>';
    }

    public function getOption(/*** Profile_DefinitionsObject ***/ $obj, /*** string ***/ $key=null)
    {
        return $obj->get('options');
    }
}


/** --------------------------------------------------------
 *  Int Type
**/
class Profile_FieldTypeInt implements Profile_iFieldType
{
    const TYPE = 'int';

    public function showField(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** Profile_ActionType ***/ $option=0)
    {
        return $obj->get($key);
    }

    public function getTableQuery()
    {
        return 'INT(11) SIGNED NOT NULL';
    }

    public function setInitVar(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** string ***/ $default)
    {
        $obj->initVar($key, $this->getXObjType(), $default, false);
    }

    public function getDefault(/*** string ***/ $option)
    {
        return isset($option) ? $option : '';
    }

    public function getXObjType()
    {
        return XOBJ_DTYPE_INT;
    }

    public function getFormPropertyClass()
    {
        return 'XCube_IntProperty';
    }

    public function getSearchFormString(/*** string ***/ $key)
    {
        return '<dt></dt><dd><input type="text" value="" name="<{$key}>" /></dd>';
    }

    public function getOption(/*** Profile_DefinitionsObject ***/ $obj, /*** string ***/ $key=null)
    {
        return $obj->get('options');
    }
}



/** --------------------------------------------------------
 *  Float Type
**/
class Profile_FieldTypeFloat implements Profile_iFieldType
{
    const TYPE = 'float';

    public function showField(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** Profile_ActionType ***/ $option=0)
    {
        return $obj->get($key);
    }

    public function getTableQuery()
    {
        return 'decimal(10,4) UNSIGNED NOT NULL';
    }

    public function setInitVar(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** string ***/ $default)
    {
        $obj->initVar($key, $this->getXObjType(), $default, false);
    }

    public function getDefault(/*** string ***/ $option)
    {
        return isset($option) ? $option : 0;
    }

    public function getXObjType()
    {
        return XOBJ_DTYPE_FLOAT;
    }

    public function getFormPropertyClass()
    {
        return 'XCube_FloatProperty';
    }

    public function getSearchFormString(/*** string ***/ $key)
    {
        return '<dt></dt><dd><input type="text" value="" name="<{$key}>" /></dd>';
    }

    public function getOption(/*** Profile_DefinitionsObject ***/ $obj, /*** string ***/ $key=null)
    {
        return $obj->get('options');
    }
}


/** --------------------------------------------------------
 *  Date Type
**/
class Profile_FieldTypeDate implements Profile_iFieldType
{
    const TYPE = 'date';

    public function showField(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** Profile_ActionType ***/ $option=0)
    {
        if($option==Profile_ActionType::NONE){
            $value = $obj->get($key);
        }
        elseif($option==Profile_ActionType::EDIT){
            $value = date(_PHPDATEPICKSTRING, $obj->get($key));
        }
        elseif($option==Profile_ActionType::VIEW){
            $value = ($obj->get($key)) ? formatTimestamp($obj->get($key), "s") : "";
        }
        return $value;
    }

    public function getTableQuery()
    {
        return 'INT(11) UNSIGNED NOT NULL';
    }

    public function setInitVar(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** string ***/ $default)
    {
        $obj->initVar($key, $this->getXObjType(), $default, false);
    }

    public function getDefault(/*** string ***/ $option)
    {
        return time();
    }

    public function getXObjType()
    {
        return XOBJ_DTYPE_INT;
    }

    public function getFormPropertyClass()
    {
        return 'XCube_StringProperty';
    }

    public function getSearchFormString(/*** string ***/ $key)
    {
        return '<dt></dt><dd><input type="text" value="" name="<{$key}>" class="datePicker" /> <input type="text" value="" name="<{$key}>" class="datePicker" /></dd>';
    }

    public function getOption(/*** Profile_DefinitionsObject ***/ $obj, /*** string ***/ $key=null)
    {
        return $obj->get('options');
    }
}


/** --------------------------------------------------------
 *  Checkbox Type
**/
class Profile_FieldTypeCheckbox implements Profile_iFieldType
{
    const TYPE = 'checkbox';

    public function showField(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** Profile_ActionType ***/ $option=0)
    {
        if($option==Profile_ActionType::NONE||$option==Profile_ActionType::EDIT){
            $value = $obj->get($key);
        }
        elseif($option==Profile_ActionType::VIEW){
            $handler = Legacy_Utils::getModuleHandler('definitions', 'profile');
            $objs = $handler->getObjects(new Criteria('field_name', $key));
            if(count($objs)>0){
                $def = array_shift($objs);
                $optArr = $def->mFieldType->getOption($def);
            }
            $yes = $optArr[0] ? $optArr[0] : _YES;
            $no = $optArr[1] ? $optArr[1] : _NO;
            $value = $obj->get($key)==true ? $yes : $no;
        }
        return $value;
    }

    public function getTableQuery()
    {
        return 'TINYINT(1) UNSIGNED NOT NULL';
    }

    public function setInitVar(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** string ***/ $default)
    {
        $obj->initVar($key, $this->getXObjType(), $default, false);
    }

    public function getDefault(/*** string ***/ $option)
    {
        $optionArr = explode('|', $option);
        return isset($optionArr[2]) ? $optionArr[2] : 0;
    }

    public function getXObjType()
    {
        return XOBJ_DTYPE_BOOL;
    }

    public function getFormPropertyClass()
    {
        return 'XCube_BoolProperty';
    }

    public function getSearchFormString(/*** string ***/ $key)
    {
        return '<dt></dt><dd><input type="checkbox" value="" name="<{$key}>" /></dd>';
    }

    public function getOption(/*** Profile_DefinitionsObject ***/ $obj, /*** string ***/ $key=null)
    {
        return explode("|", $obj->get('options'));
    }
}


/** --------------------------------------------------------
 *  Selectbox Type
**/
class Profile_FieldTypeSelectbox implements Profile_iFieldType
{
    const TYPE = 'selectbox';

    public function showField(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** Profile_ActionType ***/ $option=0)
    {
        if($option==Profile_ActionType::NONE||$option==Profile_ActionType::VIEW){
            $value = $obj->getShow($key);
        }
        elseif($option==Profile_ActionType::EDIT){
            $value = $obj->get($key);
        }
        return $value;
    }

    public function getTableQuery()
    {
        return 'VARCHAR(60) NOT NULL';
    }

    public function setInitVar(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** string ***/ $default)
    {
        $obj->initVar($key, $this->getXObjType(), $default, false);
    }

    public function getDefault(/*** string ***/ $option)
    {
        return '';
    }

    public function getXObjType()
    {
        return XOBJ_DTYPE_STRING;
    }

    public function getFormPropertyClass()
    {
        return 'XCube_StringProperty';
    }

    public function getSearchFormString(/*** string ***/ $key)
    {
        return '<dt></dt><dd></dd>';
    }

    public function getOption(/*** Profile_DefinitionsObject ***/ $obj, /*** string ***/ $key=null)
    {
        return explode("|", $obj->get('options'));
    }
}


/** --------------------------------------------------------
 *  Category Type
**/
class Profile_FieldTypeCategory implements Profile_iFieldType
{
    const TYPE = 'category';

    public function showField(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** Profile_ActionType ***/ $option=0)
    {
        return $obj->get($key);
    }

    public function getTableQuery()
    {
        return 'INT(11) SIGNED NOT NULL';
    }

    public function setInitVar(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** string ***/ $default)
    {
        $obj->initVar($key, $this->getXObjType(), $default, false);
    }

    public function getDefault(/*** string ***/ $option)
    {
        return 0;
    }

    public function getXObjType()
    {
        return XOBJ_DTYPE_INT;
    }

    public function getFormPropertyClass()
    {
        return 'XCube_IntProperty';
    }

    public function getSearchFormString(/*** string ***/ $key)
    {
        return '<dt></dt><dd><input type="text" value="" name="<{$key}>" /></dd>';
    }

    public function getOption(/*** Profile_DefinitionsObject ***/ $obj, /*** string ***/ $key=null)
    {
        return $obj->get('options');
    }
}


/** --------------------------------------------------------
 *  Uri Type
**/
class Profile_FieldTypeUri implements Profile_iFieldType
{
    const TYPE = 'uri';

    public function showField(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** Profile_ActionType ***/ $option=0)
    {
        if($option==Profile_ActionType::NONE||$option==Profile_ActionType::VIEW){
            $value = $obj->getShow($key);
        }
        elseif($option==Profile_ActionType::EDIT){
            $value = $obj->get($key);
        }
        return $value;
    }

    public function getTableQuery()
    {
        return 'text NOT NULL';
    }

    public function setInitVar(/*** Profile_DataObject ***/ $obj, /*** string ***/ $key, /*** string ***/ $default)
    {
        $obj->initVar($key, $this->getXObjType(), $default, false);
    }

    public function getDefault(/*** string ***/ $option)
    {
        return '';
    }

    public function getXObjType()
    {
        return XOBJ_DTYPE_TEXT;
    }

    public function getFormPropertyClass()
    {
        return 'XCube_TextProperty';
    }

    public function getSearchFormString(/*** string ***/ $key)
    {
        return '<dt></dt><dd></dd>';
    }

    public function getOption(/*** Profile_DefinitionsObject ***/ $obj, /*** string ***/ $key=null)
    {
        return $obj->get('options');
    }
}



/**
 * Profile_ActionType
**/
class Profile_ActionType
{
    const NONE = 0;
    const EDIT = 1;
    const VIEW = 2;
}

?>
