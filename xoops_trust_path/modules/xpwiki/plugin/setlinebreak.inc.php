<?php
class xpwiki_plugin_setlinebreak extends xpwiki_plugin {
	function plugin_setlinebreak_init () {



	}
	// PukiWiki - Yet another WikiWikiWeb clone
	// $Id: setlinebreak.inc.php,v 1.1 2006/10/13 13:17:49 nao-pon Exp $
	//
	// Set linebreak plugin - on/of linebreak-to-'<br />' conversion
	//
	// Usage:
	//	#setlinebreak          : Invert on/off
	//	#setlinebreak(on)      : ON  (from this line)
	//	#setlinebreak(off)     : OFF (from this line)
	//	#setlinebreak(default) : Reset
	
	function plugin_setlinebreak_convert()
	{
	//	global $line_break;
	//	static $default;
		static $default = array();
		if (!isset($default[$this->xpwiki->pid])) {$default[$this->xpwiki->pid] = array();}
	
		if (! isset($default[$this->xpwiki->pid])) $default[$this->xpwiki->pid] = $this->root->line_break;
	
		if (func_num_args() == 0) {
			// Invert
			$this->root->line_break = ! $this->root->line_break;
		} else {
			$args = func_get_args();
			switch (strtolower($args[0])) {
			case 'on':	/*FALLTHROUGH*/
			case 'true':	/*FALLTHROUGH*/
			case '1':
				$this->root->line_break = 1;
				break;
	
			case 'off':	/*FALLTHROUGH*/
			case 'false':	/*FALLTHROUGH*/
			case '0':
				$this->root->line_break = 0;
				break;
	
			case 'default':
				$this->root->line_break = $default[$this->xpwiki->pid];
				break;
	
			default:
				return '#setlinebreak: Invalid argument: ' .
				htmlspecialchars($args[0]) . '<br />';
			}
		}
		return '';
	}
}
?>