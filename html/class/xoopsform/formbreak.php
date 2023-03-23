<?php
/**
 * Form break
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

class XoopsFormBreak extends XoopsFormElement
{
    public function __construct($extra = '', $class= '')
    {
        $this->setExtra($extra);
        $this->setClass($class);
    }
    public function XoopsFormBreak($extra = '', $class= '')
    {
        return $this->__construct($extra, $class);
    }

    public function isBreak()
    {
        return true;
    }
}
