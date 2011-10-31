<?php
/////////////////////////////////////////////////
// PukiWiki - Yet another WikiWikiWeb clone.
//
// $Id: endregion.inc.php,v 1.2 2007/11/07 23:48:13 nao-pon Exp $
//
class xpwiki_plugin_endregion extends xpwiki_plugin {
	function plugin_endregion_init () {

	}
	
	function plugin_endregion_convert()
	{
		// Close area div
		$areadiv_closer = $this->func->get_areadiv_closer();
		return <<<EOD
{$areadiv_closer}</td></tr></table>
EOD;
	}
}
?>