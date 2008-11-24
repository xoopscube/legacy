<?php

class XoopsMailerLocal extends XoopsMailer {

	function XoopsMailerLocal(){
		$this->XoopsMailer();
		$this->charSet = 'UTF-8';
	}
}
?>