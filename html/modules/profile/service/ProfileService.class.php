<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class Profile_DataObj extends XCube_Object
{
	/**
	 * @public
	 */
	function getPropertyDefinition()
	{
		$handler =& xoops_getmodulehandler('definitions', 'profile');
		$defArr =& $handler->getObjects();
		$ret = array(
			S_PUBLIC_VAR("int uid")
		);
		foreach(array_keys($defArr) as $key){
			$ret[] = S_PUBLIC_VAR($defArr->getServiceField());
		}
	
		return $ret;
	}
}

class Profile_DataObjArray extends XCube_ObjectArray
{
	/**
	 * @public
	 */
	function getClassName()
	{
		return "Profile_DataObj";
	}
}

/**
 * @brief options for select box
 * @public
 */
class Profile_OptionsObj extends XCube_Object
{
	/**
	 * @public
	 */
	function getPropertyDefinition()
	{
		$ret = array(
			S_PUBLIC_VAR("string option_name"),
		);
		
		return $ret;
	}
}

class Profile_OptionsObjArray extends XCube_ObjectArray
{
	/**
	 * @public
	 */
	function getClassName()
	{
		return "Profile_OptionsObj";
	}
}

class Profile_DefinitionsObj extends XCube_Object
{
	/**
	 * @public
	 */
	function getPropertyDefinition()
	{
		$ret = array(
			S_PUBLIC_VAR("int field_id"),
			S_PUBLIC_VAR("string field_name"),
			S_PUBLIC_VAR("string label"),
			S_PUBLIC_VAR("string type"),
			S_PUBLIC_VAR("int weight"),
			S_PUBLIC_VAR("text description"),
			S_PUBLIC_VAR("text access"),
			S_PUBLIC_VAR("Profile_OptionsObjArray options"),
		);
		
		return $ret;
	}
}

class Profile_DefinitionsObjArray extends XCube_ObjectArray
{
	/**
	 * @public
	 */
	function getClassName()
	{
		return "Profile_DefinitionsObj";
	}
}

class Profile_Service extends XCube_Service
{
	var $mServiceName = "Profile_Service";
	var $mNameSpace = "Profile";
	var $mClassName = "Profile_Service";

	/**
	 * @public
	 */
	function prepare()
	{
		$this->addType('Profile_DataObj');
		$this->addType('Profile_DataObjArray');
		$this->addType('Profile_OptionsObj');
		$this->addType('Profile_OptionsObjArray');
		$this->addType('Profile_DefinitionsObj');
		$this->addType('Profile_DefinitionsObjArray');
	
		$this->addFunction(S_PUBLIC_FUNC('Profile_DefinitionsObjArray getDefinitions(int uid, int groupid, bool show_form)'));
		$this->addFunction(S_PUBLIC_FUNC('Profile_DataObj getProfile(int uid)'));
		$this->addFunction(S_PUBLIC_FUNC('Profile_DataObjArr getProfileArr(string field_name, string value)'));
		$this->addFunction(S_PUBLIC_FUNC('bool setProfile(string field_name, string value, int uid)'));
	
		$handler =& xoops_getmodulehandler('definitions', 'profile');
		$defArr =& $handler->getObjects();
		$fieldDef = "";
		foreach(array_keys($defArr) as $key){
			$fieldDef .= $defArr[$key]->getServiceField() .',';
		}
		$fieldDef = rtrim($fieldDef, ',');
		$this->addFunction(S_PUBLIC_FUNC('bool setProfiles(int uid'. $fieldDef .')'));
	}

	/**
	 * @public
	 * get the profile field list.
	 * @params int uid: access limit by user id. prior to groupid's.
	 * @params int groupid: access limit by groupid.
	 * @params bool show_form: get defs if "show_form" is true.
	 */
	function getDefinitions()
	{
		$definitionsArr = array();
	
		$root =& XCube_Root::getSingleton();
		$uid = ($root->mContext->mXoopsUser) ? $root->mContext->mXoopsUser->get('uid') : 0;
	
		//get parameters
		//access limit by uid
		$request_uid = $root->mContext->mRequest->getRequest('uid');
		if($request_uid>0){
			$gHandler =& xoops_gethandler('member');
			$groupArr =& $gHandler->getGroupsByUser($request_uid);
		}
		//access limit by group id
		$groupid = $root->mContext->mRequest->getRequest('groupid');
		//show_form
		$show_form = $root->mContext->mRequest->getRequest('show_form');
	
		$criteria = new CriteriaCompo();
		if($show_form==true){
			$criteria->add(new Criteria('show_form', '1'));
		}
		$handler =& xoops_getmodulehandler('definitions', 'profile');
		$definitions =& $handler->getObjects($criteria);
		foreach(array_keys($definitions) as $key){
			if($uid>0 && $request_uid==$uid){
				$def = $definitions[$key]->gets();
			}
			elseif($request_uid>0){
				$flag = false;
				$accessArr = explode(',', $definitions[$key]->get('access'));
				foreach(array_keys($groupArr) as $keyG){
					if(in_array($groupArr[$keyG], $accessArr)){
						$flag = true;
					}
				}
				if($flag==true){
					$def = $definitions[$key]->gets();
				}
			}
			elseif($groupid>0){
				if(in_array($groupid, explode(',', $definitions[$key]->get('access')))){
					$def = $definitions[$key]->gets();
				}
			}
			else{
				$def = $definitions[$key]->gets();
			}
			$def['options'] = ($def['options']) ? explode('|', $def['options']) : array();
			$definitionsArr[] = $def;
		}
		unset($handler);
		return $definitionsArr;
	}

	/**
	 * @public
	 * get the given user's profile
	 */
	function getProfile()
	{
		$root =& XCube_Root::getSingleton();
		$uid = $root->mContext->mRequest->getRequest('uid');
		$handler =& xoops_getmodulehandler('data', 'profile');

		$dataObj =& $handler->get($uid);
		if(! $dataObj){
			$dataObj =& $handler->create();
		}
		$dataArr = $dataObj->gets();
		return $dataArr;
	}

	/**
	 * @public
	 * get the given user's profile
	 */
	function getProfileArr()
	{
		$dataList = array();
	
		$root =& XCube_Root::getSingleton();
		$field_name = $root->mContext->mRequest->getRequest('field_name');
		$value = $root->mContext->mRequest->getRequest('value');
	
		$handler =& xoops_getmodulehandler('data', 'profile');
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria($field_name, $value));
		$dataArr =& $handler->getObjects($criteria);
		foreach(array_keys($dataArr) as $key){
			$dataList[$key] = $dataArr[$key]->gets();
		}
		return $dataList;
	}

	/**
	 * @public
	 * set profile into the given user.
	 */
	function setProfile()
	{
		$root =& XCube_Root::getSingleton();
		$field_name = $root->mContext->mRequest->getRequest('field_name');
		$value = $root->mContext->mRequest->getRequest('value');
		$uid = $root->mContext->mRequest->getRequest('uid');
	
		$handler =& xoops_getmodulehandler('data', 'profile');
		$obj =& $handler->get($uid);
		
		if (! $obj) {
			return false;
		}
		
		$obj->set($field_name, $value);
		if($handler->insert($obj)){
			return true;
		}
		else{
			return false;
		}
	}

	/**
	 * @public
	 * set profiles into the given user.
	 */
	function setProfiles()
	{
		$root =& XCube_Root::getSingleton();
		$uid = $root->mContext->mRequest->getRequest('uid');
	
		$defHandler =& xoops_getmodulehandler('definitions', 'profile');
		$defArr =& $defHandler->getObjects();
	
		$dataHandler =& xoops_getmodulehandler('data', 'profile');
		$dataObj =& $dataHandler->get($uid);

		if(! $dataObj){
			$dataObj = $dataHandler->create();
		}

		$dataObj->set("uid", $uid);
	
		foreach(array_keys($defArr) as $key){
			$dataObj->set($defArr[$key]->getShow('field_name'), $root->mContext->mRequest->getRequest($defArr[$key]->getShow('field_name')));
		}
	
		if($dataHandler->insert($dataObj)){
			return true;
		}
		else{
			return false;
		}
	}
}


?>
