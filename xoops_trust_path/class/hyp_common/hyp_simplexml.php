<?php
// $Id: hyp_simplexml.php,v 1.5 2010/11/17 06:32:08 nao-pon Exp $
// HypSimpleXML Class by nao-pon http://hypweb.net
// Based on SimpleXML
// added function 'XMLstr_in()'

if (! function_exists('XC_CLASS_EXISTS')) {
	require dirname(__FILE__) . '/XC_CLASS_EXISTS.inc.php';
}

if( ! XC_CLASS_EXISTS( 'HypSimpleXML' ) )
{
/**
 *
 * SimpleXML - A simple XML parser for php.
 *
 * Copyright (C) 2002  Mark Raddatz <webnets@gmx.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */

/**
 * phpSimpeXML - A easy to use php class for reading and writing XML.
 *
 * Options only for XMLin():
 *
 *	   $opt["forcecontent"] = 1; (default 0)
 *
 *		   <i>This option allows you to force text content to always parse to
 *		   a associative array even when there are no attributes.</i>
 *
 *
 *
 * Options only for XMLout():
 *
 *	   $opt["filename"] = "example.xml"; (default "")
 *
 *		   <i>The default behaviour of XMLout() is to return the XML as a string.  If you
 *		   wish to write the XML to a file, simply supply the filename using the
 *		   'filename' option.</i>
 *
 *
 *
 * Options for XMLin() and XMLout():
 *
 *	   $opt["contentkey"] = "keyname"; (default "content")
 *
 *		   <i>When text content is parsed to a associative array, this option let's you specify a
 *		   name for the associative array key to override the default 'content'.</i>
 *
 *	   $opt["keeproot"] = 1; (default 0)
 *
 *		   <i>In its attempt to return a data structure free of superfluous detail and
 *		   unnecessary levels of indirection, XMLout() normally discards the root
 *		   element name.  Setting the 'keeproot' option to '1' will cause the root element
 *		   name to be retained.</i>
 *
 *
 *
 * Use this options like
 *	   $xml = XMLin("example.xml", $opt);
 * or
 *	   $output = XMLout($xml, $opt);
 *
 * @package	phpSimpleXML
 * @access public
 * @version 0.01
 * @author Mark Raddatz <webnets@gmx.de>
 * @link http://sourceforge.net/projects/phpsimplexml/
 *
 */

class HypSimpleXML
{

	/**
	 * @access private
	 */
	var $xml_parser;

	/**
	 * @access private
	 */
	var $options = array();

	/**
	 * @access private
	 */
	var $stack = array();

	/**
	 * @access private
	 */
	var $output = array();

	var $error = '';

	/**
	 * @access private
	 * @param mixed $parser
	 * @param string $name
	 * @param array $attributes
	 */
	function startElement($parser, $name, $attributes)
	{
		$xmldata = new HypXMLData($name, $this->options);
		$xmldata->setAttributes($attributes);
		array_push($this->stack, $xmldata);
	}


	/**
	 * @access private
	 * @param mixed $parser
	 * @param string $name
	 */
	function endElement($parser, $name)
	{
		$child = array_pop($this->stack);

		if (count($this->stack) < 1)
		{
			if (isset($this->options["keeproot"]))
			{
			$name = $child->getName();

			if (($attributes = $child->getAttributes()))
				$this->output[$name] = array_merge($this->output[$name], $attributes);

			if (($child2 = $child->getChild()))
				$this->output[$name] = array_merge($this->output[$name], $child2);

			if (($cdata = $child->getCData()))
				$this->output[$name]["content"] = $cdata;
			}
			else
			{
			$name = $child->getName();

			if (($attributes = $child->getAttributes()))
				$this->output = array_merge($this->output, $attributes);

			if (($child2 = $child->getChild()))
				$this->output = array_merge($this->output, $child2);

			if (($cdata = $child->getCData()))
				$this->output["content"] = $cdata;
			}

		}
		else
		{
			$xmldata = $this->stack[count($this->stack) - 1];
			$xmldata->setChild($child);
			$this->stack[count($this->stack) - 1] = $xmldata;
		}
	}

	function _die($msg, $ret = array()) {
		$this->error = $msg;
		return $ret;
	}

	/**
	 * @access private
	 * @param mixed $parser
	 * @param string $data
	 */
	function characterData($parser, $data)
	{
		$xmldata = $this->stack[count($this->stack) - 1];
		$xmldata->setCData($data);
		$this->stack[count($this->stack) - 1] = $xmldata;
	}


	/**
	 * Slurping XML into a associative array.
	 *
	 * @access public
	 * @param string $file filename
	 * @param array $options options
	 * @return array
	 */
	function XMLin($file, $options = array())
	{

		$this->options = $options;
		$this->xml_parser = xml_parser_create();
		xml_set_object($this->xml_parser, $this);
		xml_parser_set_option($this->xml_parser,XML_OPTION_CASE_FOLDING,0);
		xml_set_element_handler($this->xml_parser, "startElement", "endElement");
		xml_set_character_data_handler($this->xml_parser, "characterData");

		if (!($fp = fopen($file, "r")))
			$this->_die("could not open XML input");


		while ($data = fread($fp, 4096))
		{
			if (!xml_parse($this->xml_parser, $data, feof($fp)))
			{
				$this->_die(sprintf("XML error: %s at line %d",
				xml_error_string(xml_get_error_code($this->xml_parser)),
				xml_get_current_line_number($this->xml_parser)));
			}
		}

		return $this->output;
	}


	/**
	 * Slurping XML into a associative array.
	 *
	 * @access public
	 * @param string $str XML
	 * @param array $options options
	 * @return array
	 */
	function XMLstr_in($str, $options = array())
	{
		$this->output = array();
		$this->options = $options;
		$this->xml_parser = xml_parser_create();
		xml_set_object($this->xml_parser, $this);
		xml_parser_set_option($this->xml_parser,XML_OPTION_CASE_FOLDING,0);
		xml_set_element_handler($this->xml_parser, "startElement", "endElement");
		xml_set_character_data_handler($this->xml_parser, "characterData");

		$str = str_replace(array("\r\n","\r"),"\n",$str);
		foreach (explode("\n",$str) as $data)
		{
			if (!xml_parse($this->xml_parser, $data))
			{
				$this->_die(sprintf("XML error: %s at line %d",
				xml_error_string(xml_get_error_code($this->xml_parser)),
				xml_get_current_line_number($this->xml_parser)));
			}
		}

		return $this->output;
	}

	/**
	 * 'Unslurping' a associative array out to XML.
	 * @access public
	 * @param array $xmlarray xmlarray
	 * @param array $options options
	 * @return string
	 */
	function XMLout($xmlarray, $options = array())
	{

			$this->options = $options;

			if (empty($this->options["contentkey"]))
				$this->options["contentkey"] = "content";

			if ($this->options["keeproot"])
				list($name, $xmlarray) = each($xmlarray);
			else
				$name = "root";


			$xmlcode = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n\n";
			$xmlcode .= $this->buildXML($xmlarray, $name, "");


			if ($this->options["filename"])
			{
				if (!($fp = fopen($this->options["filename"], "w")))
					$this->_die("could not open XML input", '');

				fwrite($fp, $xmlcode);
				fclose($fp);
			}

			return $xmlcode;
	}

	/**
	 * @access private
	 * @param array $xmlarray xmlarray
	 * @param string $name name
	 * @param string $indent indent
	 * @return string
	 */

	function buildXML($xmlarray, $name, $indent)
	{
		$build = 0;

		foreach ($xmlarray as $key => $value) {
			$key2 = (int) $key;
			if ("$key" == "$key2")
			{
				$build = 1;
				if(is_array($value))
					$xmlcode .= $indent.($this->buildXML($value, $name, $indent))."\n";
				else
					$xmlcode .= "\n".$indent."<".$name.">".htmlentities($value)."</".$name.">\n";
			}
			else if (is_array($value))
				$xmlcode .= $indent.($this->buildXML($value, $key, $indent))."\n";
			else if ($key == $this->options["contentkey"])
				$xmlcode.= htmlentities($value);
			else
				$attr .= " ".$key."=\"".htmlentities($value)."\"";
		}
		if (!$build)
			$xmlcode = $indent."<".$name.$attr.($xmlcode ? ">".$xmlcode."</".$name.">" : " />" );
		return $xmlcode;
	}
}
}

if( ! XC_CLASS_EXISTS( 'HypXMLData' ) )
{
/**
 * @package	phpSimpleXML
 * @access private
 */

class HypXMLData
{

	/**
	 * @access private
	 */
	var $name;


	/**
	 * @access private
	 */
	var $options;


	/**
	 * @access private
	 */
	var $attributes;


	/**
	 * @access private
	 */
	var $cdata;

	/**
	 * @access private
	 */
	var $child;


	/**
	 * @access public
	 * @param string $name name
	 * @param array $options options
	 */
	function HypXMLData($name, $options = array())
	{
		if (empty($options["contentkey"]))
			$options["contentkey"] = "content";

		$this->options = $options;

		$this->name = $name;
	}


	/**
	 * @access public
	 * @param string $cdata cdata
	 */
	function setCData($cdata)
	{
		$cdata = ltrim($cdata);

		if ($cdata)
		{
			$this->cdata .= $cdata;
		}
	}


	/**
	 * @access public
	 * @return string
	 */
	function getCData()
	{
		return $this->cdata;
	}


	/**
	 * @access public
	 * @param array $attributes attributes
	 */
	function setAttributes($attributes)
	{
		$this->attributes = $attributes;
	}


	/**
	 * @access public
	 * @return array
	 */
	function getAttributes()
	{
		return $this->attributes;
	}


	/**
	 * @access public
	 * @param string $name name
	 */
	function setName($name)
	{
		$name = ltrim($name);

		if ($name)
		{
			$this->name = $name;
		}
	}


	/**
	 * @access public
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}


	/**
	 * @access public
	 * @param object $xmldata xmldata
	 */
	function setChild($xmldata)
	{
		$name = $xmldata->getName();
		$contentkey = $this->options["contentkey"];

		if (isset($this->child[$name]))
		{
			if (empty($this->child[$name][0]) || ($this->child[$name][0] && !(is_array($this->child[$name]))))
			{
				$temp = $this->child[$name];
				$this->child[$name] = array();
				$this->child[$name][0] = $temp;
			}

			$index = count($this->child[$name]);
			if (empty($this->child[$name][$index])) $this->child[$name][$index] = array();

			if (($attributes = $xmldata->getAttributes()))
				$this->child[$name][$index] = array_merge($this->child[$name][$index], $attributes);

			if (($child = $xmldata->getChild()))
				$this->child[$name][$index] = array_merge($this->child[$name][$index], $child);

			if (($cdata = $xmldata->getCData()))
			{
				if (empty($this->child[$name][$index]) && empty($this->options["forcecontent"]))
					$this->child[$name][$index] = $cdata;
				else
				{
					if (@ $this->child[$name][$index][$contentkey])
					{

						$temp = $this->child[$name][$index][$contentkey];
						$this->child[$name][$index][$contentkey] = array();
						$this->child[$name][$index][$contentkey][0] = $temp;

						$this->child[$name][$index][$contentkey][1] = $cdata;
					}
					else
						$this->child[$name][$index][$contentkey] = $cdata;
				}

			}
		}
		else
		{
			$this->child[$name] = "";
			if (($attributes = $xmldata->getAttributes()))
			{
				$this->child[$name] = ($this->child[$name]) ? array_merge($this->child[$name], $attributes) : $attributes;
			}
			if (($child = $xmldata->getChild()))
			{
				$this->child[$name] =($this->child[$name]) ? array_merge($this->child[$name], $child) : $child;
			}
			if (($cdata = $xmldata->getCData()))
			{

				if (empty($this->child[$name]) && empty($this->options["forcecontent"]))
					$this->child[$name] = $cdata;
				else
				{
					if (@ $this->child[$name][$contentkey])
					{

						$temp = $this->child[$name][$contentkey];
						$this->child[$name][$contentkey] = array();
						$this->child[$name][$contentkey][0] = $temp;

						$this->child[$name][$contentkey][1] = $cdata;
					}
					else
						$this->child[$name][$contentkey] = $cdata;
				}

			}
		}
	}


	/**
	 * @access public
	 * @return object
	 */
	function getChild()
	{
		return $this->child;
	}
}
}
?>