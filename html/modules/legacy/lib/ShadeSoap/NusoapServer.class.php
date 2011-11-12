<?php
/**
 * @package ShadeSoap
 * @version $Id: NusoapServer.class.php,v 1.3 2007/12/15 12:16:57 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://www.gnu.org/licenses/lgpl.txt GNU LESSER GENERAL PUBLIC LICENSE Version 2.1
 */
 // TODO prevent path disclosure, gigamaster
 error_reporting(0);

// if (!XC_CLASS_EXISTS('soap_server')) exit();

if (version_compare(PHP_VERSION, "5.0", ">=")) {
	if (!class_exists("soap_server", false)) {
		exit();
	}
}
else {
	if (!class_exists("soap_server")) {
		exit();
	}
}

class ShadeSoap_NusoapServer extends soap_server
{
	function invoke_method() {
		$this->debug('in invoke_method, methodname=' . $this->methodname . ' methodURI=' . $this->methodURI . ' SOAPAction=' . $this->SOAPAction);

		if ($this->wsdl) {
			if ($this->opData = $this->wsdl->getOperationData($this->methodname)) {
				$this->debug('in invoke_method, found WSDL operation=' . $this->methodname);
				$this->appendDebug('opData=' . $this->varDump($this->opData));
			} elseif ($this->opData = $this->wsdl->getOperationDataForSoapAction($this->SOAPAction)) {
				// Note: hopefully this case will only be used for doc/lit, since rpc services should have wrapper element
				$this->debug('in invoke_method, found WSDL soapAction=' . $this->SOAPAction . ' for operation=' . $this->opData['name']);
				$this->appendDebug('opData=' . $this->varDump($this->opData));
				$this->methodname = $this->opData['name'];
			} else {
				$this->debug('in invoke_method, no WSDL for operation=' . $this->methodname);
				$this->fault('Client', "Operation '" . $this->methodname . "' is not defined in the WSDL for this service");
				return;
			}
		} else {
			$this->debug('in invoke_method, no WSDL to validate method');
		}

		// if a . is present in $this->methodname, we see if there is a class in scope,
		// which could be referred to. We will also distinguish between two deliminators,
		// to allow methods to be called a the class or an instance
		$class = '';
		$method = '';
		if (strpos($this->methodname, '..') > 0) {
			$delim = '..';
		} else if (strpos($this->methodname, '.') > 0) {
			$delim = '.';
		} else {
			$delim = '';
		}

		if (strlen($delim) > 0 && substr_count($this->methodname, $delim) == 1 &&
			XC_CLASS_EXISTS(substr($this->methodname, 0, strpos($this->methodname, $delim)))) {
			// get the class and method name
			$class = substr($this->methodname, 0, strpos($this->methodname, $delim));
			$method = substr($this->methodname, strpos($this->methodname, $delim) + strlen($delim));
			$this->debug("in invoke_method, class=$class method=$method delim=$delim");
		}

		// does method exist?
		if ($class == '') {
			if (!function_exists($this->methodname)) {
				$this->debug("in invoke_method, function '$this->methodname' not found!");
				$this->result = 'fault: method not found';
				$this->fault('Client',"method '$this->methodname' not defined in service");
				return;
			}
		} else {
			$method_to_compare = (substr(phpversion(), 0, 2) == '4.') ? strtolower($method) : $method;
			if (!in_array($method_to_compare, get_class_methods($class))) {
				$this->debug("in invoke_method, method '$this->methodname' not found in class '$class'!");
				$this->result = 'fault: method not found';
				$this->fault('Client',"method '$this->methodname' not defined in service");
				return;
			}
		}

		// evaluate message, getting back parameters
		// verify that request parameters match the method's signature
		if(! $this->verify_method($this->methodname,$this->methodparams)){
			// debug
			$this->debug('ERROR: request not verified against method signature');
			$this->result = 'fault: request failed validation against method signature';
			// return fault
			$this->fault('Client',"Operation '$this->methodname' not defined in service.");
			return;
		}

		// if there are parameters to pass
		$this->debug('in invoke_method, params:');
		$this->appendDebug($this->varDump($this->methodparams));
		$this->debug("in invoke_method, calling '$this->methodname'");

		if ($class == '') {
			$this->debug('in invoke_method, calling function using call_user_func_array()');
			$call_arg = "$this->methodname";	// straight assignment changes $this->methodname to lower case after call_user_func_array()
		} elseif ($delim == '..') {
			$this->debug('in invoke_method, calling class method using call_user_func_array()');
			$call_arg = array ($class, $method);
		} else {
			$this->debug('in invoke_method, calling instance method using call_user_func_array()');
			$instance = new $class ();
			$call_arg = array(&$instance, $method);
		}
		
		//
		// Insert CUBE CODE
		//
		$root =& XCube_Root::getSingleton();
		// $root->mContext->mUser->setService(true);
		$retValue = call_user_func_array($call_arg, array($root->mContext->mUser, $this->methodparams));
		
		if (is_array($retValue)) {
			$retValue = $this->_encodeUTF8($retValue, $root->mLanguageManager);
		}
		else {
			$retValue = $root->mLanguageManager->encodeUTF8($retValue);
		}

		$this->methodreturn = $retValue;	

        $this->debug('in invoke_method, methodreturn:');
        $this->appendDebug($this->varDump($this->methodreturn));
		$this->debug("in invoke_method, called method $this->methodname, received $this->methodreturn of type ".gettype($this->methodreturn));
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
}


?>
