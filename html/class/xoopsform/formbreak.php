<?php

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
        return self::__construct($extra, $class);
    }
    
    public function isBreak()
    {
        return true;
    }
}
