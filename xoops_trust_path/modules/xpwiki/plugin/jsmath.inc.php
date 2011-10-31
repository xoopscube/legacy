<?php
/*
 * Created on 2008/03/04 by nao-pon http://hypweb.net/
 * $Id: jsmath.inc.php,v 1.2 2008/04/14 08:37:20 nao-pon Exp $
 */

class xpwiki_plugin_jsmath extends xpwiki_plugin {
	function plugin_jsmath_init () {
		$this->config['checkPath'] = $this->cont['ROOT_PATH'] . 'jsMath/';
		$this->config['jsUrl'] = $this->cont['ROOT_URL'] . 'jsMath/easy/load.js';
	}
	
	function plugin_jsmath_convert () {
		if (! file_exists($this->config['checkPath'])) {
			$into = (! $this->root->userinfo['admin'])? '' : ' (Into: ' . $this->config['checkPath'] . ')';
			return '<div>jsMath not found. Please install <a href="http://www.math.union.edu/~dpvc/jsMath/download/jsMath.html">jsMath</a> library.' . $into . '</div>' . "\n";
		}
		
		$args = func_get_args();
		if ($body = $this->get_body($args)) {
			return'<!--NA--><div class="math" style="text-aligh:left;">' . htmlspecialchars($body) . '</div><!--/NA-->';
		} else {
			return '';
		}
	}
	
	function plugin_jsmath_inline () {
		$args = func_get_args();
		$_body = array_pop($args); // {}
		$body =  htmlspecialchars($this->get_body($args));
		
		$body = $_body? $_body : $body;
		
		if ($body) {
			return '<!--NA--><span class="math">' . $body . '</span><!--/NA-->';
		} else {
			return '';
		}
	}
	
	function get_body ($args) {
		$this->func->add_tag_head('jsmath.css');
		
	    $extentions = array(
			// extentions
			'mimeTeX'    => FALSE,
			'AMSmath'    => FALSE,
			'AMSsymbols' => FALSE,
			'autobold'   => FALSE,
			'boldsymbol' => FALSE,
			'verb'       => FALSE,
	    );

	    $plugins = array(
			// plugins
			'smallFonts'   => FALSE,
			'noImageFonts' => FALSE,
			'lobal'        => FALSE,
			'noGlobal'     => TRUE,
			'noCache'      => FALSE,
			'CHMmode'      => FALSE,
			'spriteImageFonts' => FALSE,
	    );
		
		$options = array_merge($extentions, $plugins);
		
		$this->fetch_options($options, $args);
		
		$body = '';
		if (isset($options['_args'])) {
			$body = array_pop($options['_args']);
			unset($options['_args']);
		}

	    $jsMathRoot = dirname(dirname($this->config['jsUrl']));
	    $loadFiles = array();
	    foreach ($options as $option => $bool) {
	        if ($bool) {
	            if (in_array($option, array_keys($extentions))) {
	            	$loadFiles[] = 'extensions/' . $option . '.js';
	            } else {
	            	$this->func->add_js_head($jsMathRoot . '/plugins/' . $option . '.js');
	            }
	        }
	    }
	    
	    $this->func->add_js_head($this->config['jsUrl']);
	    
	    if ($loadFiles) {
	    	$this->func->add_js_var_head('jsMath.Easy.loadFiles = jsMath.Easy.loadFiles.push("' . join('","', $loadFiles) . '");');
	    }
		
		if ($body) {
			$body = str_replace("\r", "\n", $body);
		}
		
		return $body;
	}
}
?>