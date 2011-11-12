<?php
/**
 * @package ShadePlus
 * @version $Id: SoapClient.class.php,v 1.3 2008/10/12 04:31:22 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/>
 * @license http://xoopscube.sourceforge.net/license/bsd_licenses.txt Modified BSD license
 *
 */
 // TODO prevent path disclosure, gigamaster
 error_reporting(0);

if (!XC_CLASS_EXISTS('XCube_AbstractServiceClient')) exit();

class ShadePlus_SoapClient extends XCube_AbstractServiceClient
{
	var $mClient = null;
	
	function ShadePlus_SoapClient(&$service)
	{
		parent::XCube_AbstractServiceClient($service);
		$this->mClient =new soap_client($service, true);
		$this->mClient->decodeUTF8(false);
	}
	
	function call($operation, $args)
	{
		$root =& XCube_Root::getSingleton();
		
		$args = $this->_encodeUTF8($args, $root->mLanguageManager);
		
		$retValue = $this->mClient->call($operation, $args);
		
		if (is_array($retValue)) {
			$retValue = $this->_decodeUTF8($retValue, $root->mLanguageManager);
		}
		else {
			$retValue = $root->mLanguageManager->decodeUTF8($retValue);
		}
		
		return $retValue;
	}
	
	function _encodeUTF8($arr, &$languageManager)
	{
		foreach (array_keys($arr) as $key) {
			if (is_array($arr[$key])) {
				$arr[$key] = $this->_encodeUTF8($arr[$key], $languageManager);
			}
			else {
				$arr[$key] = $languageManager->encodeUTF8($arr[$key]);
			}
		}
		
		return $arr;
	}

	function _decodeUTF8($arr, &$languageManager)
	{
		foreach (array_keys($arr) as $key) {
			if (is_array($arr[$key])) {
				$arr[$key] = $this->_decodeUTF8($arr[$key], $languageManager);
			}
			else {
				$arr[$key] = $languageManager->decodeUTF8($arr[$key]);
			}
		}
		
		return $arr;
	}
}

?>
