<?php
/**
 * Form hidden token field
 * @package    kernel
 * @subpackage form
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class XoopsFormHiddenToken extends XoopsFormHidden
{

    /**
     * Constructor
     *
     * @param string $name "name" attribute
     * @param int    $timeout
     */
    public function __construct($name = null, $timeout = 360)
    {
        if (empty($name)) {
            $token =& XoopsMultiTokenHandler::quickCreate(XOOPS_TOKEN_DEFAULT);
            $name = $token->getTokenName();
        } else {
            $token =& XoopsSingleTokenHandler::quickCreate(XOOPS_TOKEN_DEFAULT);
        }
        $this->XoopsFormHidden($name, $token->getTokenValue());
    }
    public function XoopsFormHiddenToken($name = null, $timeout = 360)
    {
        return $this->__construct($name, $timeout);
    }
}
