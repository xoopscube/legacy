<?php
//
// Created on 2006/10/26 by nao-pon http://hypweb.net/
// $Id: pre.inc.php,v 1.1 2006/10/27 11:50:40 nao-pon Exp $
//

/**
 * a part of code.inc.php
 * Time-stamp: <05/08/03 21:27:10 sasaki>
 * 
 * GPL
 */

if ($this->exist_plugin('code')) {
	class xpwiki_plugin_pre extends xpwiki_plugin_code {
		function plugin_pre_init () {
			parent::plugin_code_init ();
		}
	}
}
?>