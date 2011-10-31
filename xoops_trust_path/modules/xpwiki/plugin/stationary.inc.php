<?php
class xpwiki_plugin_stationary extends xpwiki_plugin {
	
	// Init someting
	function plugin_stationary_init()
	{

	// $Id: stationary.inc.php,v 1.1 2006/10/13 13:17:49 nao-pon Exp $
	//
	// Stationary plugin
	// License: The same as PukiWiki
	
	// Define someting like this
		$this->cont['PLUGIN_STATIONARY_MAX'] =  10;

		if ($this->cont['PKWK_SAFE_MODE'] || $this->cont['PKWK_READONLY']) return; // Do nothing
	
		$messages = array(
			'_plugin_stationary_A' => 'a',
		'_plugin_stationary_B' => array('C' => 'c', 'D'=>'d'),
		);
		$this->func->set_plugin_messages($messages);
	}
	
	// Convert-type plugin: #stationary or #stationary(foo)
	function plugin_stationary_convert()
	{
		// If you don't want this work at secure/productive site,
		if ($this->cont['PKWK_SAFE_MODE']) return ''; // Show nothing
	
		// If this plugin will write someting,
		if ($this->cont['PKWK_READONLY']) return ''; // Show nothing
	
		// Init
		$args = array();
		$result = '';
	
		// Get arguments
		if (func_num_args()) {
			$args = func_get_args();
			foreach	(array_keys($args) as $key)
				$args[$key] = trim($args[$key]);
			$result = join(',', $args);
		}
	
		return '#stationary(' . htmlspecialchars($result) . ')<br />';
	}
	
	// In-line type plugin: &stationary; or &stationary(foo); , or &stationary(foo){bar};
	function plugin_stationary_inline()
	{
		if ($this->cont['PKWK_SAFE_MODE'] || $this->cont['PKWK_READONLY']) return ''; // See above
	
		// {bar} is always exists, and already sanitized
		$args = func_get_args();
		$body = $this->func->strip_autolink(array_pop($args)); // {bar}
	
		foreach	(array_keys($args) as $key)
			$args[$key] = trim($args[$key]);
		$result = join(',', $args);
	
		return '&amp;stationary(' . htmlspecialchars($result) . '){' . $body . '};';
	}
	
	// Action-type plugin: ?plugin=stationary&foo=bar
	function plugin_stationary_action()
	{
		// See above
		if ($this->cont['PKWK_SAFE_MODE'] || $this->cont['PKWK_READONLY'])
			$this->func->die_message('PKWK_SAFE_MODE or PKWK_READONLY prohibits this');
	
		$msg  = 'Message';
		$body = 'Message body';
	
		return array('msg'=>htmlspecialchars($msg), 'body'=>htmlspecialchars($body));
	}
}
?>