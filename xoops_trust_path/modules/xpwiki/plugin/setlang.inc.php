<?php
class xpwiki_plugin_setlang extends xpwiki_plugin {
	function plugin_setlang_init () {
		// Usage:
		$this->usage_inline = 'Usage: &amp;setlang(ja|zh|cn|ko){Text};';
		$this->usage_block = 'Usage: #setlang(ja|zh|cn|ko){{<br />Text<br/>}}';
		// 許可する言語
		$this->config['accepts'] = array('ja', 'zh', 'cn', 'ko');
		// 言語に対するスタイル名
		$this->config['classes'] = array(
									'ja' => 'jp' ,
									'zh' => 'cn' ,
									'cn' => 'cn' ,
									'ko' => 'ko' ,
								   );
		// インライン時のテンプレート
		$this->config['inline'] = '<span class="$class" xml:lang="$lang" lang="$lang">$body</span>';
		// ブロック時のテンプレート
		$this->config['block'] = '<div class="$class" xml:lang="$lang" lang="$lang">$body</div>';
	}
	
	function plugin_setlang_inline () {
		// 引数の数をチェック
		if (func_num_args() < 2) {
			$this->usage_inline;
		}
		// 引数の取得
		$args = func_get_args();
		// body部
		$body = array_pop($args);
		// 設定言語
		$lang = $args[0];
		// 許可された言語?
		if (! in_array($lang, $this->config['accepts'])) {
			return $this->usage_inline;
		}
		// クラス名
		$class = $this->config['classes'][$lang];
		
		// テンプレートを置換して出力
		return str_replace(array('$class', '$lang', '$body'), array($class, $lang, $body), $this->config['inline']);
	}

	function plugin_setlang_convert () {
		// 引数の数をチェック
		if (func_num_args() < 2) {
			$this->usage_block;
		}
		// 引数の取得
		$args = func_get_args();
		// body部
		$body = array_pop($args);
		$body = $this->func->convert_html_multiline($body);
		// 設定言語
		$lang = $args[0];
		// 許可された言語?
		if (! in_array($lang, $this->config['accepts'])) {
			return $this->usage_block;
		}
		// クラス名
		$class = $this->config['classes'][$lang];
		
		// テンプレートを置換して出力
		return str_replace(array('$class', '$lang', '$body'), array($class, $lang, $body), $this->config['block']);
	}

}
