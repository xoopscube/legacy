<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: config.php,v 1.4 2008/01/21 23:43:47 nao-pon Exp $
// Copyright (C) 2003-2005 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// Parse a PukiWiki page as a configuration page

/*
 * $obj = new XpWikiConfig('plugin/plugin_name/')
 * $obj->read();
 * $array = & $obj->get($title);
 * $array[] = array(4, 5, 6);		// Add - directly
 * $obj->add($title, array(4, 5, 6));	// Add - method of Config object
 * $array = array(1=>array(1, 2, 3));		// Replace - directly
 * $obj->put($title, array(1=>array(1, 2, 3));	// Replace - method of Config object
 * $obj->put_values($title, NULL);	// Delete
 * $obj->write();
 */

// Configuration-page manager
class XpWikiConfig
{
	var $name, $page; // Page name
	var $objs = array();

	function XpWikiConfig(&$xpwiki, $name)
	{
		$this->root = & $xpwiki->root;
		$this->cont = & $xpwiki->cont;
		$this->func = & $xpwiki->func;
		
		$this->name = $name;
		$this->page = $this->cont['PKWK_CONFIG_PREFIX'] . $name;
	}

	// Load the configuration-page
	function read()
	{
		if (! $this->func->is_page($this->page)) return FALSE;

		$this->objs = array();
		$obj        = & new XpWikiConfigTable('');
		$matches = array();

		foreach ($this->func->get_source($this->page) as $line) {
			if ($line == '') continue;

			$head  = $line{0};	// The first letter
			$level = strspn($line, $head);

			if ($level > 3) {
				$obj->add_line($line);

			} else if ($head == '*') {
				// Cut fixed-heading anchors
				$line = preg_replace('/^(\*{1,5}.*)\[#[A-Za-z][\w-]+\](.*)$/', '$1$2', $line);

				if ($level == 1) {
					$this->objs[$obj->title] = $obj;
					$obj = & new XpWikiConfigTable($line);
				} else {
					if (! is_a($obj, 'XpWikiConfigTable_Direct'))
						$obj = & new XpWikiConfigTable_Direct('', $obj);
					$obj->set_key($line);
				}
				
			} else if ($head == '-' && $level > 1) {
				if (! is_a($obj, 'XpWikiConfigTable_Direct'))
					$obj = & new XpWikiConfigTable_Direct('', $obj);
				$obj->add_value($line);

			} else if ($head == '|' && preg_match('/^\|(.+)\|\s*$/', $line, $matches)) {
				// Table row
				if (! is_a($obj, 'XpWikiConfigTable_Sequential'))
					$obj = & new XpWikiConfigTable_Sequential('', $obj);
				// Trim() each table cell
				$obj->add_value(array_map('trim', explode('|', $matches[1])));
			} else {
				$obj->add_line($line);
			}
		}
		$this->objs[$obj->title] = $obj;

		return TRUE;
	}

	// Get an array
	function & get($title)
	{
		$obj = & $this->get_object($title);
		return $obj->values;
	}

	// Set an array (Override)
	function put($title, $values)
	{
		$obj         = & $this->get_object($title);
		$obj->values = $values;
	}

	// Add a line
	function add($title, $value)
	{
		$obj = & $this->get_object($title);
		$obj->values[] = $value;
	}

	// Get an object (or create it)
	function & get_object($title)
	{
		if (! isset($this->objs[$title]))
			$this->objs[$title] = & new XpWikiConfigTable('*' . trim($title) . "\n");
		return $this->objs[$title];
	}

	function write()
	{
		$this->func->page_write($this->page, $this->toString());
	}

	function toString()
	{
		$retval = '';
		foreach ($this->objs as $title=>$obj)
			$retval .= $obj->toString();
		return $retval;
	}
}

// Class holds array values
class XpWikiConfigTable
{
	var $title  = '';	// Table title
	var $before = array();	// Page contents (except table ones)
	var $after  = array();	// Page contents (except table ones)
	var $values = array();	// Table contents

	function XpWikiConfigTable($title, $obj = NULL)
	{
		if ($obj !== NULL) {
			$this->title  = $obj->title;
			$this->before = array_merge($obj->before, $obj->after);
		} else {
			$this->title  = trim(substr($title, strspn($title, '*')));
			$this->before[] = $title;
		}
	}

	// Addi an  explanation
	function add_line($line)
	{
		$this->after[] = $line;
	}

	function toString()
	{
		return join('', $this->before) . join('', $this->after);
	}
}

class XpWikiConfigTable_Sequential extends XpWikiConfigTable
{
	// Add a line
	function add_value($value)
	{
		$this->values[] = (count($value) == 1) ? $value[0] : $value;
	}

	function toString()
	{
		$retval = join('', $this->before);
		if (is_array($this->values)) {
			foreach ($this->values as $value) {
				$value   = is_array($value) ? join('|', $value) : $value;
				$retval .= '|' . $value . '|' . "\n";
			}
		}
		$retval .= join('', $this->after);
		return $retval;
	}
}

class XpWikiConfigTable_Direct extends XpWikiConfigTable
{
	var $_keys = array();	// Used at initialization phase

	function set_key($line)
	{
		$level = strspn($line, '*');
		$this->_keys[$level] = trim(substr($line, $level));
	}

	// Add a line
	function add_value($line)
	{
		$level = strspn($line, '-');
		$arr   = & $this->values;
		for ($n = 2; $n <= $level; $n++)
			$arr = & $arr[$this->_keys[$n]];
		$arr[] = trim(substr($line, $level));
	}

	function toString($values = NULL, $level = 2)
	{
		$retval = '';
		$root   = ($values === NULL);
		if ($root) {
			$retval = join('', $this->before);
			$values = & $this->values;
		}
		foreach ($values as $key=>$value) {
			if (is_array($value)) {
				$retval .= str_repeat('*', $level) . $key . "\n";
				$retval .= $this->toString($value, $level + 1);
			} else {
				$retval .= str_repeat('-', $level - 1) . $value . "\n";
			}
		}
		if ($root) $retval .= join('', $this->after);

		return $retval;
	}
}
?>