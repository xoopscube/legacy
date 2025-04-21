<?php
/**
 * Token
 * @package    Legacy
 * @subpackage core
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class XoopsFormToken extends XoopsFormHidden
{
    /**
     * Constructor
     *
     * @param object    $token  XoopsToken instance
    */
    public function __construct($token)
    {
        if (is_object($token)) {
            parent::__construct($token->getTokenName(), $token->getTokenValue());
        } else {
            parent::__construct('', '');
        }
    }
    public function XoopsFormToken($token)
    {
        return self::__construct($token);
    }
}
