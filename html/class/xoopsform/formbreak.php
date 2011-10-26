<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class XoopsFormBreak extends XoopsFormElement {
    function XoopsFormBreak($extra = '', $class= '') {
        $this->setExtra($extra);
        $this->setClass($class);
    }
    
    function isBreak() {
        return true;
    }
}
?>