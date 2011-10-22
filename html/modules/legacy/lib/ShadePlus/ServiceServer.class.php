<?php
/**
 * @package ShadePlus
 * @version $Id: ServiceServer.class.php,v 1.3 2008/10/12 04:31:22 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/>
 * @license http://xoopscube.sourceforge.net/license/bsd_licenses.txt Modified BSD license
 *
 */

class ShadePlus_ServiceServer
{
	var $_mService;
	
	var $_mServer;
	
	function ShadePlus_ServiceServer(&$service)
	{
		$this->_mService =& $service;
		$this->_mServer =new ShadeSoap_NusoapServer();
		
		$this->_mServer->configureWSDL($this->_mService->mServiceName, $this->_mService->mNameSpace);
		$this->_mServer->wsdl->schemaTargetNamespace = $this->_mService->mNameSpace;
	}
	
	function prepare()
	{
		$this->_parseType();
		$this->_parseFunction();
	}
	
	function _parseType()
	{
		//
		// FIXME
		//
		foreach ($this->_mService->_mTypes as $className) {
			if (XC_CLASS_EXISTS($className)) {
				if (call_user_func(array($className, 'isArray')) == true) {
					$targetClassName = call_user_func(array($className, 'getClassName'));
					
					if (XCube_ServiceUtils::isXSD($targetClassName)) {
						$targetClassName = 'xsd:' . $targetClassName;
					}
					else {
						$targetClassName = 'tns:' . $targetClassName;
					}
					
					$this->_mServer->wsdl->addComplexType(
						$className,
						'complexType',
						'array',
						'',
						'SOAP-ENC:Array',
						array(),
						array(
							array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => $targetClassName . '[]')
						),
						$targetClassName
					);
				}
				else {
					$t_fieldArr = call_user_func(array($className, 'getPropertyDefinition'));
					$t_arr = array();
					foreach ($t_fieldArr as $t_field) {
						$name = $t_field['name'];
						$type = $t_field['type'];
					
						if (XCube_ServiceUtils::isXSD($t_field['type'])) {
							$type = 'xsd:' . $type;
						}
						else {
							$type = 'tns:' . $type;
						}
					
						$t_arr[$name] = array('name' => $name, 'type' => $type);
					}
				
					$this->_mServer->wsdl->addComplexType(
						$className,
						'complexType',
						'struct',
						'all',
						'',
						$t_arr
					);
				}
			}
		}
	}
	
	function _parseFunction()
	{
		//
		// FIXME
		//
		foreach ($this->_mService->_mFunctions as $func) {
			if (XCube_ServiceUtils::isXSD($func['out'])) {
				$t_out = 'xsd:' . $func['out'];
			}
			else {
				$t_out = 'tns:' . $func['out'];
			}
			
			$out['return'] = $t_out;
			
			//
			// Parse IN
			//
			$in = array();
			foreach ($func['in'] as $name => $type) {
				if (XCube_ServiceUtils::isXSD($type)) {
					$t_type = 'xsd:' . $type;
				}
				else {
					$t_type = 'tns:' . $type;
				}
				$in[$name] = $t_type;
			}
			
			$this->_mServer->register($this->_mService->mClassName . "." . $func['name'], $in, $out, $this->_mService->mNameSpace);
		}
	}
	
	function executeService()
	{
		$HTTP_RAW_POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA'])? $GLOBALS['HTTP_RAW_POST_DATA'] : null;
		$this->_mServer->service($HTTP_RAW_POST_DATA);
	}
}

?>
