<?php
// $Id: xoopsmailerlocal.php,v 1.1 2008/06/22 03:29:11 minahito Exp $

if (!defined('XOOPS_ROOT_PATH')) exit();

class XoopsMailerLocal extends XoopsMailer {
	function XoopsMailerLocal()
	{
		$this->multimailer = new XoopsMultiMailerLocal();
		$this->reset();
		$this->charSet = 'utf-8';
		$this->encoding = 'base64';
		$this->multimailer->CharSet = $this->charSet;
		$this->multimailer->SetLanguage('en');
		$this->multimailer->Encoding = "base64";	
	}
}

class XoopsMultiMailerLocal extends XoopsMultiMailer
{
	function XoopsMultiMailerLocal()
	{
		parent::XoopsMultiMailer();
	}
}

?>