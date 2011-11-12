<?php
/*
 * Created on 2009/04/09 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: boxdate.inc.php,v 1.2 2009/04/11 07:44:12 nao-pon Exp $
 */

class xpwiki_plugin_boxdate extends xpwiki_plugin {
	
	function plugin_boxdate_init() {
		// Page title's date format
		//  * See PHP date() manual for detail
		//  * '$\w' = weeklabel defined in $_msg_week
		$this->conf['DATE_FORMAT']['en'] = '<\s\p\a\n \c\l\a\s\s="\b\o\x\d\a\t\e">
<\s\p\a\n \c\l\a\s\s="\d\w\e\e\k \w$\D"> D </\s\p\a\n>
<\s\p\a\n \c\l\a\s\s="\d\a\y \d$\D"> j </\s\p\a\n>
<\s\p\a\n \c\l\a\s\s="\m\o\n\t\h"> M </\s\p\a\n>
<\s\p\a\n \c\l\a\s\s="\y\e\a\r"> Y </\s\p\a\n>
</\s\p\a\n>'
		;
		
		$this->conf['DATE_FORMAT']['ja'] = '<\s\p\a\n \c\l\a\s\s="\b\o\x\d\a\t\e">
<\s\p\a\n \c\l\a\s\s="\y\e\a\r"> Y </\s\p\a\n>
<\s\p\a\n \c\l\a\s\s="\m\o\n\t\h"> n&#26376; </\s\p\a\n>
<\s\p\a\n \c\l\a\s\s="\d\a\y \d$\D"> j </\s\p\a\n>
<\s\p\a\n \c\l\a\s\s="\d\w\e\e\k \w$\D"> ($\w) </\s\p\a\n>
</\s\p\a\n>'
		;
		
		$this->options = array(
			'link' => FALSE,
			'page' => FALSE,
			'date' => FALSE,
		);
	}
	
	function plugin_boxdate_convert() {
		
		$options = $this->options;
		$args = func_get_args();
		
		$this->fetch_options($options, $args);

		$page = $options['page']? $options['page'] : $this->root->vars['page'];

		$date = $options['date']? $options['date'] : $this->func->basename($page);

		$time = strtotime($date);
		if ($time === -1 || $time === FALSE) {
			$s_page = htmlspecialchars($page); // Failed. Why?
		} else {
			$this->func->add_tag_head('calendar.css');
			$week   = $this->root->weeklabels[date('w', $time)];
			$D      = date('D', $time);
			$format = isset($this->conf['DATE_FORMAT'][$this->cont['UI_LANG']])? $this->conf['DATE_FORMAT'][$this->cont['UI_LANG']] : $this->conf['DATE_FORMAT']['en'];
			$s_page = str_replace(
					array('$w', '$D'),
					array($week, $D),
					date($format, $time)
					);
		}
		
		if ($options['link']) {
			$s_page = $this->func->make_pagelink($page, $s_page);
		}
		
		return '<div class="boxdate_base">' . $s_page . '</div><div class="boxdate_spacer">&nbsp;</div>' . "\n";

	}
}
