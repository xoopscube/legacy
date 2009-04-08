<?php
// $Id$
// License http://creativecommons.org/licenses/by/2.5/br/

if (!defined('XOOPS_ROOT_PATH')) exit();

class XoopsMailerLocal extends XoopsMailer {

	function XoopsMailerLocal(){

		$this->XoopsMailer();
		$this->charSet = 'iso-8859-1';

	}

}
?>