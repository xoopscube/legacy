<?php
/**
 *
 * @package Legacy
 * @version $Id: xoopssecurity.php,v 1.3 2008/09/25 15:12:42 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
/**
 * Class for xoops.org 2.0.10 compatibility
 *
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
                    $ret .= $error.'<br />';
                }
            }
            return $ret;
        }
    }
}
