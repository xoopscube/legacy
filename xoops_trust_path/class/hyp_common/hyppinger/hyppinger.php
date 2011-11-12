<?php
/*
 * Created on 2008/05/15 by nao-pon http://hypweb.net/
 * $Id: hyppinger.php,v 1.3 2009/09/01 01:25:11 nao-pon Exp $
 */

require_once(dirname(dirname(__FILE__)) . '/hyp_common_func.php');

class HypPinger {
	var $name, $url, $changesurl, $rssurl, $tag;
	
	var $sendTo = array();
	var $encoding = '';
	var $xml_normal = '';
	var $xml_extended = '';
	var $results = array();
	var $connect_timeout = 10;
	var $read_timeout = 5;
	var $blocking = FALSE;
		
	var $debug = FALSE;
	
	function HypPinger ($name, $url, $changesurl=NULL, $rssurl=NULL, $tag=NULL) {
		$this->name = htmlspecialchars($name);
		$this->url = $url;
		$this->changesurl = $changesurl? $changesurl : '';
		$this->rssurl = $rssurl? $rssurl : '';
		$this->tag = $tag? htmlspecialchars($tag) : '';
		// a name (or "tag") categorizing your site content (string, limited to 1024 characters. You may delimit multiple values by using the '|' character.)
	}
	
	function send () {
		$this->_buildXML();
		$d = new Hyp_HTTP_Request();
		foreach($this->sendTo as $sendTo) {
			$d->init();
			$d->url     = $sendTo['url'];
			$d->method  = 'POST';
			$d->headers = "Content-Type: text/xml\r\n";
			$d->post    = $sendTo['extended']? $this->xml_extended : $this->xml_normal;
			$d->connect_timeout = $this->connect_timeout;
			$d->read_timeout = $this->read_timeout;
			$d->blocking = $this->blocking;
			
			$d->get();
			
			if ($this->debug) {
				$this->querys[$d->url] = $d->query;
			}
			$this->results[$d->url]['query'] = $d->query; 
			$this->results[$d->url]['rc']    = $d->rc;
			$this->results[$d->url]['header']= $d->header; 
			$this->results[$d->url]['data']  = $d->data; 
		}
		$d = NULL;
		unset($d);
	}
	
	function addSendTo ($to=NULL, $extended=FALSE) {
		if (! $to) return;
		$this->sendTo[] = array('url' => $to, 'extended' => $extended);
	}
	
	function setEncording ($encode) {
		$this->encording = $encode;
	}
	
	function _buildXML() {
		if ($this->encording) {
			if (! extension_loaded('mbstring') && ! XC_CLASS_EXISTS('HypMBString')) {
				require_once(dirname(dirname(__FILE__)) . '/mbemulator/mb-emulator.php');
			}
			$this->name = mb_convert_encoding($this->name, 'UTF-8', $this->encording);
			$this->tag  = mb_convert_encoding($this->tag, 'UTF-8', $this->encording);
		}
		$tag = $changesurl = '';
		if ($this->changesurl) {
			$changesurl = <<<EOD
<param>
<value>{$this->changesurl}</value>
</param>
EOD;
		}
		if ($this->tag) {
			$tag = <<<EOD
<param>
<value>{$this->tag}</value>
</param>
EOD;
		}
		$this->xml_normal = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
<methodName>weblogUpdates.ping</methodName>
<params>
<param>
<value>{$this->name}</value>
</param>
<param>
<value>{$this->url}</value>
</param>{$changesurl}{$tag}
</params>
</methodCall>
EOD;
		$this->xml_extended = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
<methodName>weblogUpdates.extendedPing</methodName>
<params>
<param>
<value>{$this->name}</value>
</param>
<param>
<value>{$this->url}</value>
</param>
{$changesurl}
<param>
<value>{$this->rssurl}</value>
</param>
{$tag}
</params>
</methodCall>
EOD;
	}
}
?>