<?php
/**
 * Class for xoops2 compatibility
 * @package    Legacy
 * @subpackage core
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 * @deprecated
 */

class xoopssecurity
{
    public $errors;

    public function check($clearIfValid = true, $tokenValue = false)
    {
        return $this->validateToken($tokenValue, $clearIfValid);
    }

    public function createToken($timeout = XOOPS_TOKEN_TIMEOUT)
    {
        $token =& XoopsMultiTokenHandler::quickCreate(XOOPS_TOKEN_DEFAULT, $timeout);
        return $token->getTokenValue();
    }

    public function validateToken($tokenValue = false, $clearIfValid = true)
    {
        if (false !== $tokenValue) {
            $handler = new XoopsSingleTokenHandler();
            $token =& $handler->fetch(XOOPS_TOKEN_DEFAULT);
            if ($token->validate($tokenValue)) {
                if ($clearIfValid) {
                    $handler->unregister($token);
                }
                return true;
            } else {
                $this->setErrors('No token found');
                return false;
            }
        }
        return XoopsMultiTokenHandler::quickValidate(XOOPS_TOKEN_DEFAULT, $clearIfValid);
    }

    public function getTokenHTML()
    {
        $token =& XoopsMultiTokenHandler::quickCreate(XOOPS_TOKEN_DEFAULT);
        return $token->getHtml();
    }

    public function setErrors($error)
    {
        $this->errors[] = trim($error);
    }

    public function &getErrors($ashtml = false)
    {
        if (!$ashtml) {
            return $this->errors;
        } else {
            $ret = '';
            if (count($this->errors) > 0) {
                foreach ($this->errors as $error) {
                    $ret .= $error.'<br>';
                }
            }
            return $ret;
        }
    }
}
