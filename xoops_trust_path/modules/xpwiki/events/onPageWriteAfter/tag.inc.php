<?php
//
// Created on 2006/10/31 by nao-pon http://hypweb.net/
// $Id: tag.inc.php,v 1.8 2011/11/26 12:03:10 nao-pon Exp $
//
function xpwiki_onPageWriteAfter_tag(&$xpwiki_func, &$page, &$postdata, &$notimestamp, &$mode, &$diffdata) {

	// ページのtagデータファイルがある || tagプラグインらしき記述がある？
	$do = is_file($xpwiki_func->cont['CACHE_DIR'] . $xpwiki_func->encode($page) . '_page.tag');
	if ($do || preg_match("/&tag\([^)]*\)(\{.*?\})?;/",$postdata)) {
		$params = array();
		if ( $mode !== 'delete' ) {
			$ic = new XpWikiInlineConverter($xpwiki_func->xpwiki, array('plugin'));
			$data = explode("\n",$postdata);
			while (! empty($data)) {
				$line =  array_shift($data);
				
				if (!$line) continue; // 空行
				
				// The first character
				$head = $line{0};
				
				if (
					// Escape comments
					substr($line, 0, 2) === '//' ||
					// Horizontal Rule
					substr($line, 0, 4) === '----' ||
					// Pre
					$head === ' ' || $head === "\t"
				) {	continue; }
		
				// Multiline-enabled block plugin
				if (!$xpwiki_func->cont['PKWKEXP_DISABLE_MULTILINE_PLUGIN_HACK'] && preg_match('/^#[^{]+(\{\{+)\s*$/', $line, $matches)) {
					$len = strlen($matches[1]);
					while (! empty ($data)) {
						$next_line = preg_replace("/[\r\n]*$/", '', array_shift($data));
						if (preg_match('/^\}{'.$len.'}/', $next_line)) { break; }
					}
				}
				
				// tagプラグインのパラメータを抽出
				$arr = $ic->get_objects($line, $page);
				while( ! empty($arr) ) {
					$obj = array_shift($arr);
					if ( $obj->name === 'tag' ) {
						$do = TRUE;
						$params = array_merge($params, $xpwiki_func->csv_explode(',', $obj->param));
					}
				}
			}
		}
		
		if ($do) {
			$plugin =& $xpwiki_func->get_plugin_instance('tag');
			if ($plugin !== FALSE) {
				$params = array_unique($params);
				$_params = array();
				foreach ($params as $prm) {
					if ($prm) $_params[] = $prm;
				}
				$_aryargs = array($page, $_params);
				call_user_func_array(array($xpwiki_func->root->plugin_tag, 'renew_tagcache'), $_aryargs);
			}
		}
	}
}
?>