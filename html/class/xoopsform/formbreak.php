<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class XoopsFormBreak extends XoopsFormElement
{
    public function XoopsFormBreak($extra = '', $class= '')
    {
        $this->setExtra($extra);
        $this->setClass($class);
    }
    
    public function isBreak()
    {
        return true;
    }
}
