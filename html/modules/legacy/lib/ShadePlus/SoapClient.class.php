<?php
/**
 * @package ShadePlus
 * @version $Id: SoapClient.class.php,v 1.3 2008/10/12 04:31:22 minahito Exp $
 * @copyright (c) 2005-2024 The XOOPSCube Project
 * @license BSD
 *
 */
 // TODO prevent path disclosure, gigamaster
 error_reporting(0);

if (!XC_CLASS_EXISTS('XCube_AbstractServiceClient')) {
    exit();
}

class ShadePlus_SoapClient extends XCube_AbstractServiceClient
{
    public $mClient = null;

    public function __construct(&$service)
    {
        parent::__construct($service);
        $this->mClient =new soap_client($service, true);
        $this->mClient->decodeUTF8(false);
    }

    public function call($operation, $args)
    {
        $root =& XCube_Root::getSingleton();

        $args = $this->_encodeUTF8($args, $root->mLanguageManager);

        $retValue = $this->mClient->call($operation, $args);

        if (is_array($retValue)) {
            $retValue = $this->_decodeUTF8($retValue, $root->mLanguageManager);
        } else {
            $retValue = $root->mLanguageManager->decodeUTF8($retValue);
        }

        return $retValue;
    }

    public function _encodeUTF8($arr, &$languageManager)
    {
        foreach (array_keys($arr) as $key) {
            if (is_array($arr[$key])) {
                $arr[$key] = $this->_encodeUTF8($arr[$key], $languageManager);
            } else {
                $arr[$key] = $languageManager->encodeUTF8($arr[$key]);
            }
        }

        return $arr;
    }

    public function _decodeUTF8($arr, &$languageManager)
    {
        foreach (array_keys($arr) as $key) {
            if (is_array($arr[$key])) {
                $arr[$key] = $this->_decodeUTF8($arr[$key], $languageManager);
            } else {
                $arr[$key] = $languageManager->decodeUTF8($arr[$key]);
            }
        }

        return $arr;
    }
}
