<?php
// $Id$
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
class XoopsMailerLocal extends XoopsMailer
{
    public function XoopsMailerLocal()
    {
        $this->multimailer = new XoopsMultiMailerLocal();
        $this->reset();
        $this->charSet = 'UTF-8';
        $this->encoding = '8bit';
        $this->multimailer->CharSet = $this->charSet;
        $this->multimailer->SetLanguage('pt');
        $this->multimailer->Encoding = "8bit";
    }
}
class XoopsMultiMailerLocal extends XoopsMultiMailer
{
    public function XoopsMultiMailerLocal()
    {
        parent::XoopsMultiMailer();
    }
}
