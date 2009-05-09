<?php
// $Id: xoopsmailerlocal.php,v 1.1 2008/03/09 02:38:13 xoopserver Exp $
// License http://creativecommons.org/licenses/by/2.5/br/

error_reporting(0);

class XoopsMailerLocal extends XoopsMailer {

	function XoopsMailerLocal(){

		$this->XoopsMailer();
		$this->charSet = 'iso-8859-1';

	}

}
?>