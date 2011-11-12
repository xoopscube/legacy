<?php
class XpWikiExtension {
	function XpWikiExtension($xpwiki) {
		$this->xpwiki = & $xpwiki;
		$this->root   = & $xpwiki->root;
		$this->cont   = & $xpwiki->cont;
		$this->func   = & $xpwiki->func;
	}
}
?>