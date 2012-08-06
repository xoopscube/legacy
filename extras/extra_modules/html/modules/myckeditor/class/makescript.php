<?php
/**
 * @file
 * @package myckeditor
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class MyckeditorMakescriptHandler
{
	/**
	 *	 makeheader
	*/
	function makeheader($params)
	{
		$root = XCube_Root::getSingleton();
		$jQuery = $root->mContext->getAttribute('headerScript');

		$this->addScriptCommon($params);

		$ckconfig = $this->getckconfig($params);
		$ckconfig_var = "";
		$ckconfig_var .=  "var ckconfig_".$params['id']." = {".$ckconfig."};\n";
		$jQuery->addScript($ckconfig_var,false);

		$ckExec = $this->getckExec($params);
		$jQuery->addScript($ckExec,false);
		if (isset($params['myckeditor'])){
			if ($params['myckeditor'] === 'display'){
				$ckStart = $this->getckStart($params);
				$jQuery->addScript($ckStart,false);
			}
		}

	}

	/**
	 *	addScriptCommon
	*/
	function addScriptCommon($params)
	{
		if (!defined('_MYCKEDITOR_COMMON_LOADED')) {
			$root = XCube_Root::getSingleton();
			$jQuery = $root->mContext->getAttribute('headerScript');

			$mydirname = basename(dirname(dirname(__FILE__)));

			$jQuery->addLibrary(XOOPS_MODULE_URL.'/'.$mydirname.'/ckeditor/ckeditor.js',false);
//			$jQuery->addLibrary(XOOPS_MODULE_URL.'/'.$mydirname.'/ckeditor/adapters/jquery.js',false);

			define('_MYCKEDITOR_COMMON_LOADED', 1);
		}
	}
	/**
	 *	getckconfig
	*/
	function getckconfig($params)
	{

		$mydirname = basename(dirname(dirname(__FILE__)));
		$mydirPath = dirname(dirname(__FILE__));

//add ckconfig start
		$ckconfig = '';
		//-- set CKEdtior Option customConfig --//
		$root = XCube_Root::getSingleton();
		$ckconfigdirname = $root->mContext->mXoopsConfig['language'];
		if ( !empty($ckconfigdirname) && ! file_exists($mydirPath.'/language/'.$ckconfigdirname.'/config.js') ) {
			$ckconfigdirname = '';
		}
		if ( !empty($ckconfigdirname) ) {
			$ckconfig .= "customConfig:'".XOOPS_MODULE_URL."/".$mydirname."/language/".$ckconfigdirname."/config.js'";
		}else{
			$ckconfig .= "customConfig:''";
		}
		if (isset($params['toolbar']) ){
			if ( !empty($params['toolbar']) ){
				$ckconfig .= ",toolbar:'".$params['toolbar']."'";
			}
		}
		//--------------------------------------//
		//set CKEdtior Option start from second
		//-- set CKEdtior Option Smailey for XoopsSmailey --//
		$ckconfig .= $this->_getCkconfig4XoopsSmailey();

		return $ckconfig;
//add EXTconfig end

	}
	/**
	 *	 getckExec
	*/
	function getckExec($params)
	{
		$ckExec = "";
		$ckExec .=  "var editor".$params['id'].";\n";
		$ckExec .= "function ".$params['id']."_myckeditor_exec(){\n";
		$ckExec .=  " if (! editor".$params['id']." ){\n";
		$ckExec .=  "   editor".$params['id']." = CKEDITOR.replace('".$params['id'] ."', ckconfig_".$params['id'].");\n";
		$ckExec .=  " }\n";
		$ckExec .= "}\n";
		$ckExec .= "function ".$params['id']."_myckeditor_remove(){\n";
		$ckExec .=  " if ( editor".$params['id']." ){\n";
		$ckExec .=  "   editor".$params['id'].".destroy();\n";
		$ckExec .=  "   editor".$params['id']."=null;\n";
		$ckExec .=  " }\n";
		$ckExec .= "}\n";
		return $ckExec;
	}

	/**
	 *	 getckStart
	*/
	function getckStart($params)
	{
		//initila display
		$ckEmain = "";
		$ckEmain .= "window.onload = function(){\n";
		$ckEmain .=  $params['id']."_myckeditor_exec();\n";
		$ckEmain .= "};\n";

		return $ckEmain;
	}

	/**
	 *	@protected _getCkconfig4XoopsSmailey
	 *
	 * @return	string for $ckconfig
	*/
	function _getCkconfig4XoopsSmailey()
	{
		$ckconfig ="";
		$ckconfig .=",smiley_path : '".XOOPS_URL."/uploads/'";
		$smileys_array = array();
		$db =& Database::getInstance();
		if ($getsmiles = $db->query("SELECT * FROM ".$db->prefix("smiles")." WHERE display=1" )){
			while ($smile = $db->fetchArray($getsmiles)) {
				$smileys_array[] = $smile;
			}
		}
		$cof_smiley_images = "";
		$cof_smiley_descriptions = "";
		foreach ($smileys_array as $value){
			if ($cof_smiley_images != ""){
				$cof_smiley_images .= ",";
				$cof_smiley_descriptions .= ",";
			}
			$cof_smiley_images .= "'".$value['smile_url']."'";
			$cof_smiley_descriptions .= "'".$value['emotion']."'";
		}
		$ckconfig .=",smiley_images : [".$cof_smiley_images."]";
		$ckconfig .=",smiley_descriptions : [".$cof_smiley_descriptions."]";

		return $ckconfig;
	}


//END CLASS
}

?>