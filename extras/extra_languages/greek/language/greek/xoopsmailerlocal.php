<?php

error_reporting(0);

class xoopsmailerlocal extends XoopsMailer
{

    public function XoopsMailerLocal()
    {
        $this->XoopsMailer();
        $this->charSet = 'UTF-8';
    }
}
