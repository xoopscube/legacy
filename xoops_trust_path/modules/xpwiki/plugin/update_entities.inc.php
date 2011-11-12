<?php
// PukiWiki - Yet another WikiWikiWeb clone
// $Id: update_entities.inc.php,v 1.7 2009/10/01 23:35:35 nao-pon Exp $
//
// Update entities plugin - Update XHTML entities from DTD
// (for admin)

class xpwiki_plugin_update_entities extends xpwiki_plugin {
	
	// メッセージ設定
	function plugin_update_entities_init()
	{
		// DTDの場所
		$this->cont['W3C_XHTML_DTD_LOCATION'] =  'http://www.w3.org/TR/xhtml1/DTD/';
	}
	
	function plugin_update_entities_action()
	{
		// 権限チェック
		if (!$this->root->userinfo['admin']) {
			return $this->action_msg_admin_only();
		}
		
		// 言語ファイルの読み込み
		$this->load_language();

		// 管理画面モード指定
		if ($this->root->module['platform'] == "xoops") {
			$this->root->runmode = "xoops_admin";
		}

		$msg = $body = '';
		if (empty($this->root->vars['action'])) {
			$msg   = & $this->msg['title_update'];
			$items = $this->plugin_update_entities_create();
			$script = $this->func->get_script_uri();
			$body  = $this->func->convert_html(sprintf($this->msg['msg_usage'], join("\n" . '-', $items)));
			$body .= <<<EOD
<form method="POST" action="{$script}">
 <div>
  <input type="hidden" name="plugin" value="update_entities" />
  <input type="hidden" name="action" value="update" />
  <input type="submit" value="{$this->msg['btn_submit']}" />
 </div>
</form>
EOD;
		} else if ($this->root->vars['action'] == 'update') {
			$this->plugin_update_entities_create(TRUE);
			$msg  = & $this->msg['title_update'];
			$body = & $this->msg['msg_done'    ];
		} else {
			$msg  = & $this->msg['title_update'];
			$body = & $this->msg['err_invalid' ];
		}
		return array('msg'=>$msg, 'body'=>$body);
	}
	
	// Remove &amp; => amp
	function plugin_update_entities_strtr($entity){
		return strtr($entity, array('&'=>'', ';'=>''));
	}
	
	function plugin_update_entities_create($do = FALSE)
	{
		$files = array('xhtml-lat1.ent', 'xhtml-special.ent', 'xhtml-symbol.ent');
		
		$entities = array_values(get_html_translation_table(HTML_ENTITIES));
		$entities = array_map(array(&$this, 'plugin_update_entities_strtr'), $entities);
		$items   = array('php:html_translation_table');
		$matches = array();
		foreach ($files as $file) {
			$source = file($this->cont['W3C_XHTML_DTD_LOCATION'] . $file);
	//			or die_message('cannot receive ' . W3C_XHTML_DTD_LOCATION . $file . '.');
			if (! is_array($source)) {
				$items[] = 'w3c:' . $file . ' COLOR(red):not found.';
				continue;
			}
			$items[] = 'w3c:' . $file;
			if (preg_match_all('/<!ENTITY\s+([A-Za-z0-9]+)/',
			join('', $source), $matches, PREG_PATTERN_ORDER))
			{
				$entities = array_merge($entities, $matches[1]);
			}
		}
		if (! $do) return $items;
	
		$entities = array_unique($entities);
		sort($entities, SORT_STRING);
		$min = 999;
		$max = 0;
		foreach ($entities as $entity) {
			$len = strlen($entity);
			$max = max($max, $len);
			$min = min($min, $len);
		}
	
		$pattern = '(?=[a-zA-Z0-9]{' . $min . ',' . $max . '})' . $this->func->get_matcher_regex($entities);
		$fp = fopen($this->cont['CACHE_DIR']  . $this->cont['PKWK_ENTITIES_REGEX_CACHE'], 'w')
			or $this->func->die_message('cannot write file PKWK_ENTITIES_REGEX_CACHE<br />' . "\n" .
			'maybe permission is not writable or filename is too long');
		fwrite($fp, $pattern);
		fclose($fp);
	
		return $items;
	}
}
?>