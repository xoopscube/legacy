<?php
//
// Created on 2009/05/28 by nao-pon http://xoops.hypweb.net/
// $Id: html.inc.php,v 1.2 2011/07/29 07:14:25 nao-pon Exp $
//

/**
 * Write HTML
 *
 * @author     sonots
 * @license    http://www.gnu.org/licenses/gpl.html GPL v2
 * @link       http://lsx.sourceforge.jp/?Plugin%2Fhtml.inc.php
 * @version    $Id: html.inc.php,v 1.2 2011/07/29 07:14:25 nao-pon Exp $
 * @package    plugin
 */

class xpwiki_plugin_html extends xpwiki_plugin {
	function plugin_html_init () {
		switch ($this->cont['UI_LANG']) {
			case 'ja':
				$this->msg['error'] = '<p>#html(): このページ($page) は、管理人以外が編集できるので HTML は表示されません。</p>';
				break;
			default:
				$this->msg['error'] = '<p>#html(): Because this page ($page) can be edited in case of no manager, HTML is not displayed.</p>';
		}
	}

	function plugin_html_convert()
	{
	    $args = func_get_args();
	    $body = array_pop($args);
	    if (substr($body, -1) != "\r") {
	        return '<p>html(): no argument(s).</p>';
	    }
	    $page = $this->root->vars['page'];
	    if (! $this->func->is_editable_only_admin($page)) {
	        $page = htmlspecialchars($page);
	        if ($this->cont['UI_LANG'] === 'ja' && $this->cont['SOURCE_ENCODING'] === 'UTF-8') {
	        	$this->msg['error'] = mb_convert_encoding($this->msg['error'], 'UTF-8', 'EUC-JP');
	        }
	        return str_replace('$page', $page, $this->msg['error']);
	    }

	    $noskin = in_array("noskin", $args);
	    if ($noskin) {
			// clear output buffer
			$this->func->clear_output_buffer();
	        $this->func->pkwk_common_headers();
	        print $body;
	        exit;
	    }
	    return $body;
	}
}
?>