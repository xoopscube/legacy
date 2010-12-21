<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Profile_DataObject extends XoopsSimpleObject
{
	public $mDef = null;

	/**
	 * @public
	 */
	public function Profile_DataObject()
	{
		$handler = Legacy_Utils::getModuleHandler('definitions', 'profile');
		$this->mDef = $handler->getDefinitionsArr(false);
	
		$this->initVar('uid', XOBJ_DTYPE_INT, '', false);
		foreach(array_keys($this->mDef) as $key){
			$type = $this->mDef[$key]['type'];
			switch($type){
				case Profile_FormType::INT:
				case Profile_FormType::FLOAT:
				case Profile_FormType::CHECKBOX:
				$this->initVar($key, Profile_FormType::getXObjType($type), 0, false);
				break;
				case Profile_FormType::STRING:
				case Profile_FormType::SELECTBOX:
				$this->initVar($key, Profile_FormType::getXObjType($type), '', false, 255);
				break;
				case Profile_FormType::TEXT:
				$this->initVar($key, Profile_FormType::getXObjType($type), '', false);
				break;
				case Profile_FormType::DATE:
				$this->initVar($key, Profile_FormType::getXObjType($type), time(), false);
				break;
			}
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
		$value = null;
	
		$type = $this->mDef[$key]['type'];
		switch ($type) {
			case Profile_FormType::INT:
			///TODO case FormType::FLOAT:
			case Profile_FormType::STRING:
			case Profile_FormType::SELECTBOX:
			case Profile_FormType::TEXT:
				if($option==Profile_ActionType::NONE||Profile_ActionType::VIEW){
					$value = $this->getShow($key);
				}
				elseif($option==Profile_ActionType::EDIT){
					$value = $this->get($key);
				}
				break;
			case Profile_FormType::DATE:
				if($option==Profile_ActionType::NONE){
					$value = $this->get($key);
				}
				elseif($option==Profile_ActionType::EDIT){
					$value = date(_PHPDATEPICKSTRING, $this->get($key));
				}
				elseif($option==Profile_ActionType::VIEW){
					$value = ($this->get($key)) ? formatTimestamp($this->get($key), "m") : "";
				}
				break;
			case Profile_FormType::CHECKBOX:
				if($option==Profile_ActionType::NONE||$option==Profile_ActionType::EDIT){
					$value = $this->get($key);
				}
				elseif($option==Profile_ActionType::VIEW){
					$value = $this->get($key)==true ? _YES : _NO;
				}
				break;
		}
		
		return $value;
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
		$type = $this->mDef[$key]['type'];
		switch ($type) {
			case Profile_FormType::DATE:
				$dateArr = explode('-', $value);
				$date = mktime(0,0,0,$dateArr[1],$dateArr[2],$dateArr[0]);
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
	var $mTable = 'profile_data';
	var $mPrimary = 'uid';
	var $mClass = 'Profile_DataObject';

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
