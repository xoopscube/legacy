<?php
// $Id$
// License http://creativecommons.org/licenses/by/2.5/br/

error_reporting(0);

class XoopsMailerLocal extends XoopsMailer {

	function XoopsMailerLocal(){

		$this->XoopsMailer();
		$this->charSet = 'iso-8859-1';

	}

}
?>