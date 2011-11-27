<?php
class xpwiki_plugin_online extends xpwiki_plugin {
	function plugin_online_init () {


	// $Id: online.inc.php,v 1.5 2011/11/26 12:03:10 nao-pon Exp $
	// Copyright (C)
	//   2002-2005 PukiWiki Developers Team
	//   2001-2002 Originally written by yu-ji
	// License: GPL v2 or (at your option) any later version
	//
	// Online plugin -- Just show the number 'users-on-line'

		$this->cont['PLUGIN_ONLINE_TIMEOUT'] =  60 * 5; // Count users in N seconds

	// ----

	// List of 'IP-address|last-access-time(seconds)'
		$this->cont['PLUGIN_ONLINE_USER_LIST'] =  $this->cont['COUNTER_DIR'] . 'user.dat';

	// Regex of 'IP-address|last-access-time(seconds)'
		$this->cont['PLUGIN_ONLINE_LIST_REGEX'] =  '/^([^\|]+)\|([0-9]+)$/';

	}

	function plugin_online_convert()
	{
		return $this->plugin_online_itself(0);
	}

	function plugin_online_inline()
	{
		return $this->plugin_online_itself(1);
	}

	function plugin_online_itself($type = 0)
	{
	//	static $count, $result, $base;
		static $count = array();
		if (!isset($count[$this->xpwiki->pid])) {$count[$this->xpwiki->pid] = array();}
		static $result = array();
		if (!isset($result[$this->xpwiki->pid])) {$result[$this->xpwiki->pid] = array();}
		static $base = array();
		if (!isset($base[$this->xpwiki->pid])) {$base[$this->xpwiki->pid] = array();}

		if (! $count[$this->xpwiki->pid]) {
			if (isset($_SERVER['REMOTE_ADDR'])) {
				$host  = & $_SERVER['REMOTE_ADDR'];
			} else {
				$host  = '';
			}

			// Try read
			if ($this->plugin_online_check_online($count[$this->xpwiki->pid], $host)) {
				$result[$this->xpwiki->pid] = TRUE;
			} else {
				// Write
				$result[$this->xpwiki->pid] = $this->plugin_online_sweep_records($host);
			}
		}

		if ($result[$this->xpwiki->pid]) {
			return $count[$this->xpwiki->pid]; // Integer
		} else {
			if (! $base[$this->xpwiki->pid]) $base[$this->xpwiki->pid] = basename($this->cont['PLUGIN_ONLINE_USER_LIST']);
			$error = '"COUNTER_DIR/' . $base[$this->xpwiki->pid] . '" not writable';
			if ($type == 0) {
				$error = '#online: ' . $error . '<br />' . "\n";
			} else {
				$error = '&online: ' . $error . ';';
			}
			return $error; // String
		}
	}

	// Check I am already online (recorded and not time-out)
	// & $count == Number of online users
	function plugin_online_check_online(& $count, $host = '')
	{
		if (! is_file($this->cont['PLUGIN_ONLINE_USER_LIST']) &&
		    ! @touch($this->cont['PLUGIN_ONLINE_USER_LIST']))
			return FALSE;

		// Open
		$fp = @fopen($this->cont['PLUGIN_ONLINE_USER_LIST'], 'r');
		if ($fp == FALSE) return FALSE;
		set_file_buffer($fp, 0);

		// Init
		$count   = 0;
		$found   = FALSE;
		$matches = array();

		flock($fp, LOCK_SH);

		// Read
		while (! feof($fp)) {
			$line = fgets($fp, 512);
			if ($line === FALSE) continue;

			// Ignore invalid-or-outdated lines
			if (! preg_match($this->cont['PLUGIN_ONLINE_LIST_REGEX'], $line, $matches) ||
			    ($matches[2] + $this->cont['PLUGIN_ONLINE_TIMEOUT']) <= $this->cont['UTIME'] ||
			    $matches[2] > $this->cont['UTIME']) continue;

			++$count;
			if (! $found && $matches[1] == $host) $found = TRUE;
		}

		flock($fp, LOCK_UN);
		if(! fclose($fp)) return FALSE;

		if (! $found && $host != '') ++$count; // About you

		return $found;
	}

	// Cleanup outdated records, Add/Replace new record, Return the number of 'users in N seconds'
	// NOTE: Call this when plugin_online_check_online() returnes FALSE
	function plugin_online_sweep_records($host = '')
	{
		// Open
		$fp = @fopen($this->cont['PLUGIN_ONLINE_USER_LIST'], 'r+');
		if ($fp == FALSE) return FALSE;
		set_file_buffer($fp, 0);

		flock($fp, LOCK_EX);

		// Read to check
		$lines = @file($this->cont['PLUGIN_ONLINE_USER_LIST']);
		if ($lines === FALSE) $lines = array();

		// Need modify?
		$line_count = $count = count($lines);
		$matches = array();
		$dirty   = FALSE;
		for ($i = 0; $i < $line_count; $i++) {
			if (! preg_match($this->cont['PLUGIN_ONLINE_LIST_REGEX'], $lines[$i], $matches) ||
			    ($matches[2] + $this->cont['PLUGIN_ONLINE_TIMEOUT']) <= $this->cont['UTIME'] ||
			    $matches[2] > $this->cont['UTIME'] ||
			    $matches[1] == $host) {
				unset($lines[$i]); // Invalid or outdated or invalid date
				--$count;
				$dirty = TRUE;
			}
		}
		if ($host != '' ) {
			// Add new, at the top of the record
			array_unshift($lines, strtr($host, "\n", '') . '|' . $this->cont['UTIME'] . "\n");
			++$count;
			$dirty = TRUE;
		}

		if ($dirty) {
			// Write
			if (! ftruncate($fp, 0)) return FALSE;
			rewind($fp);
			fputs($fp, join('', $lines));
		}

		flock($fp, LOCK_UN);
		if(! fclose($fp)) return FALSE;

		return $count; // Number of lines == Number of users online
	}
}
?>