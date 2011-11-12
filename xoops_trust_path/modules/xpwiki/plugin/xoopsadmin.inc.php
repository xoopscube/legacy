<?php
//
// Created on 2006/11/06 by nao-pon http://hypweb.net/
// $Id: xoopsadmin.inc.php,v 1.1 2006/11/06 01:54:11 nao-pon Exp $
//

class xpwiki_plugin_xoopsadmin extends xpwiki_plugin {
	
	function plugin_xoopsadmin_convert()
	{
		$this->root->runmode = 'xoops_admin';
		$this->cont['SKIN_CHANGER'] = FALSE;
		return '';
	}

}
?>