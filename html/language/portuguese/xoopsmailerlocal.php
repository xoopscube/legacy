<?php
// $Id: xoopsmailerlocal.php 862 2008-02-28 17:41:15Z mikhail.miguel $
// License http://creativecommons.org/licenses/by/2.5/br/

class XoopsMailerLocal extends XoopsMailer {

	function XoopsMailerLocal(){

		$this->XoopsMailer();
		$this->charSet = 'iso-8859-1';

	}

}
?>