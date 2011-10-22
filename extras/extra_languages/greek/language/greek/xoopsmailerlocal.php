<?php

error_reporting(0);

class XoopsMailerLocal extends XoopsMailer {

	function XoopsMailerLocal(){
		$this->XoopsMailer();
		$this->charSet = 'UTF-8';
	}
}
?>