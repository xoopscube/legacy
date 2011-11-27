<?php
//
// Created on 2006/10/25 by nao-pon http://hypweb.net/
// $Id: code.inc.php,v 1.23 2011/11/26 12:03:10 nao-pon Exp $
//

class xpwiki_plugin_code extends xpwiki_plugin {
	function plugin_code_init () {


		/**
		 * コードハイライト機能をPukiWikiに追加する
		 * Time-stamp: <05/10/07 01:31:12 sasaki>
		 *
		 * GPL
		 *
		 * r 0.6.0_pr3
		 */

		$this->cont['PLUGIN_CODE_LANGUAGE'] =  'php';  // 標準言語 全て小文字で指定
		// 標準設定
		$this->cont['PLUGIN_CODE_NUMBER'] =     1;  // 行番号
		$this->cont['PLUGIN_CODE_OUTLINE'] =    1;  // アウトライン
		$this->cont['PLUGIN_CODE_BLOCK'] =      1;  // ブロック開閉
		$this->cont['PLUGIN_CODE_LITERAL'] =    1;  // 文字列開閉
		$this->cont['PLUGIN_CODE_SW_COMMENT'] = 1;  // コメント開閉
		$this->cont['PLUGIN_CODE_MENU'] =       1;  // メニューの表示/非表示;
		$this->cont['PLUGIN_CODE_FILE_ICON'] =  1;  // 添付ファイルにダウンロードアイコンを付ける
		$this->cont['PLUGIN_CODE_LINK'] =       1;  // オートリンク
		$this->cont['PLUGIN_CODE_CACHE'] =      1;  // キャッシュを使う


		// URLで指定したファイルを読み込むか否か
		$this->cont['PLUGIN_CODE_READ_URL'] =   0;

		// テーブルを使うか否か(0はCSSのdivによる分割)
		$this->cont['PLUGIN_CODE_TABLE'] =      1;

		// TAB幅
		$this->cont['PLUGIN_CODE_WIDTHOFTAB'] =  '    ';
		// 画像ファイルの設定
		$this->cont['PLUGIN_CODE_IMAGE_FILE'] =  $this->cont['LOADER_URL'] . '?src=code_dot.png';

		$this->cont['PLUGIN_CODE_OUTLINE_OPEN_FILE'] =   $this->cont['LOADER_URL'].'?src=code_outline_open.png';
		$this->cont['PLUGIN_CODE_OUTLINE_CLOSE_FILE'] =  $this->cont['LOADER_URL'].'?src=code_outline_close.png';

		// plugin pre
		$this->cont['PLUGIN_PRE_NUMBER'] =         0;  // 行番号
		$this->cont['PLUGIN_PRE_VERVATIM_HARD'] =  1;  // インライン展開をしない
		$this->cont['PLUGIN_PRE_FILE_ICON'] =      1;  // 添付ファイルにダウンロードアイコンを付ける


		$this->cont['PLUGIN_CODE_HEADER'] =  'code_';
		$this->cont['PLUGIN_PRE_HEADER'] =  'pre_';
		$this->cont['PLUGIN_PRE_COLOR_REGEX'] =  '/^(#[0-9a-f]{3}|#[0-9a-f]{6}|[a-z-]+)$/i';

		if (! isset($this->cont['FILE_ICON'])) {
			$this->cont['FILE_ICON'] =
			'<img src="' . $this->cont['IMAGE_DIR'] . 'file.png" width="20" height="20"' .
			' alt="file" style="border-width:0px" />';
		}

		$this->cont['PLUGIN_CODE_USAGE'] =
		'<p class="error">Plugin code: Usage:<br />#code[(Lang)]{{<br />src<br />}}</p>';

		$this->cont['PLUGIN_CODE_CONFIG_PAGE_MIME'] =  'plugin/code/mimetype';
		$this->cont['PLUGIN_CODE_CONFIG_PAGE_EXTENSION'] =  'plugin/code/extension';

		$this->cont['PLUGIN_CODE_LINE_MAX'] = 25; // Show max line count
		$this->cont['PLUGIN_CODE_LINE_HEIGHT'] = 1.2; // style line-height: (em)

		$this->config['codehighlightClassFile'] = $this->root->mytrustdirpath . '/plugin/code/codehighlight.php';
		$this->config['codehighlightClassName'] = 'XpWikiCodeHighlight';
	}

	function plugin_code_action()
	{
	//	global $vars;
	//	global $_source_messages;

		if ($this->cont['PKWK_SAFE_MODE']) $this->func->die_message('PKWK_SAFE_MODE prohibits this');

		$this->root->vars['refer'] = $this->root->vars['page'];

		if (! $this->func->is_page($this->root->vars['page']) || ! $this->func->check_readable($this->root->vars['page'],0,0)) {
			return array( 'msg' => $this->root->_source_messages['msg_notfound'],
					  'body' => $this->root->_source_messages['err_notfound'] );
		}
		return array( 'msg' => $this->root->_source_messages['msg_title'],
				  'body' => $this->plugin_code_convert('pukiwiki',
												$this->func->get_source($this->root->vars['page'], TRUE, TRUE)."\n"));
	}

	function plugin_code_convert()
	{

		if (is_file($this->config['codehighlightClassFile']))
			require_once($this->config['codehighlightClassFile']);
		else
			$this->func->die_message('file ' . $this->config['codehighlightClassFile'] . ' not exist or not readable.');

		$this->func->add_tag_head('code.css');

		$title = '';
		$lang = null;
		$option = array(
						'number'      => 0,  // 行番号を表示する
						'nonumber'    => 0,  // 行番号を表示しない
						'outline'     => 0,  // アウトライン モード
						'nooutline'   => 0,  // アウトライン 無効
						'block'       => 0,  // アウトライン ブロック
						'noblock'     => 0,  // アウトライン ブロック
						'literal'     => 0,  // アウトライン 文字列
						'noliteral'   => 0,  // アウトライン 文字列
						'comment'     => 0,  // アウトライン コメント
						'nocomment'   => 0,  // アウトライン コメント
						'menu'        => 0,  // メニューを表示する
						'nomenu'      => 0,  // メニューを表示しない
						'icon'        => 0,  // アイコンを表示する
						'noicon'      => 0,  // アイコンを表示しない
						'link'        => 0,  // オートリンク 有効
						'nolink'      => 0,  // オートリンク 無効
						'title'       => '',
						);

	    $num_of_arg = func_num_args();
	    $args = func_get_args();
	    if ($num_of_arg < 1) {
	        return $this->cont['PLUGIN_CODE_USAGE'];
	    }

		$arg = $args[$num_of_arg-1];
	    if (strlen($arg) == 0) {
	        return $this->cont['PLUGIN_CODE_USAGE'];
	    }
		if ($num_of_arg != 1 && ! $this->_plugin_code_check_argment($args[0], $option))
	        $lang = htmlspecialchars(strtolower($args[0])); // 言語名かオプションの判定

		$begin = 1;
		$end = null;
		// オプションを調べる
		for ($i = 1;$i < $num_of_arg-1; ++$i) {
			if (! $this->_plugin_code_check_argment($args[$i], $option))
				$this->_plugin_code_get_region($args[$i], $end, $begin);
		}

		$data = array();
		$multiline = $this->_plugin_code_multiline_argment($arg, $data, $lang, $option, $end, $begin);

		if (isset($data['_error']) && $data['_error'] != '') {
			return $data['_error'];
		}

		// For keitai
		if ($this->cont['UA_PROFILE'] === 'keitai') {
			$option['nonumber'] = TRUE;
			$option['nooutline'] = TRUE;
		}

		if ($this->cont['PLUGIN_CODE_CACHE'] && ! $multiline) {
			if ($_ary = $this->_plugin_code_read_cache($arg, $option)) {
				list($html, $option) = $_ary;
				if ($option['outline']) {
					$this->func->add_tag_head('code.js');
				}
				return $html;
			}
		}

		$data['data'] = rtrim($data['data']) . "\n";
		$line_cnt = count(explode("\n", $data['data']));

		$lines = $data['data'];
		$title = @ $data['title'];

		if (is_null($end)) {
			$end = substr_count($lines, "\n") + $begin - 1;
		}

		$_err = error_reporting(E_ALL ^ E_NOTICE); // orz...
		$highlight = new $this->config['codehighlightClassName']($this->xpwiki);
		$lines = $highlight->highlight($lang, $lines, $option, $end, $begin);
		error_reporting($_err);
		$highlight = NULL;

		if ($option['outline']) $line_cnt += 1 * $this->cont['PLUGIN_CODE_LINE_HEIGHT'];

		$styles = array();
		$styles[] = 'height:' . (min($line_cnt, $this->cont['PLUGIN_CODE_LINE_MAX']) * $this->cont['PLUGIN_CODE_LINE_HEIGHT'] + 0.3) . 'em;';
		$styles[] = 'overflow:auto;';

		if ($lang === 'pre') {
			$lines = '<div class="'.$lang.' notranslate" style="'.join('',$styles).'">'.$lines.'</div>';
		} else {
			$lines = '<div class="pre notranslate" style="'.join('',$styles).'"><div class="'.$lang.'">'.$lines.'</div></div>';
		}

		if ($option['outline']) {
			$this->func->add_tag_head('code.js');
		}

		$html = $title.$lines;
		if ($this->cont['PLUGIN_CODE_CACHE'] && ! $multiline) {
			$this->_plugin_code_write_cache($arg, $html, $option);
		}
	    return $html;
	}

	/**
	 * キャッシュに書き込む
	 * 引数は添付ファイル名, HTML変換後のファイル
	 */
	function _plugin_code_write_cache($fname, $html, $option)
	{

		// 添付ファイルのあるページ: defaultは現在のページ名
		$page = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';

		// ファイル名にページ名(ページ参照パス)が合成されているか
		//   (Page_name/maybe-separated-with/slashes/ATTACHED_FILENAME)
		if (preg_match('#^(.+)/([^/]+)$#', $fname, $matches)) {
			if ($matches[1] == '.' || $matches[1] == '..')
				$matches[1] .= '/'; // Restore relative paths
			$fname = $matches[2];
			$page = $this->func->get_fullname($this->func->strip_bracket($matches[1]), $page); // strip is a compat
		//	$file = $this->func->encode($page) . '_' . $this->func->encode($fname);
		//} else {
		//	// Simple single argument
		//	$file =  $this->func->encode($page) . '_' . $this->func->encode($fname);
		}
		$file = $this->func->encode($page) . '_' . $this->func->encode($fname);

		// md5ハッシュ取得
		list(,,,,,,,,$md5) = array_pad(@file($this->cont['UPLOAD_DIR'].$file.".log"),9,"");
		$md5 = trim($md5);

		$html = $this->func->strip_MyHostUrl($html);
		$data = serialize(array($html, $option, $md5));

		$fp = fopen($this->cont['CACHE_DIR'].'plugin/'.$file.(($this->cont['UA_PROFILE'] === 'keitai')? '.k' : '').'.code', 'wb') or
			$this->func->die_message('Cannot write cache file ' .
					$this->cont['CACHE_DIR'].'plugin/'. $file .'.code'.
					'<br />Maybe permission is not writable or filename is too long');

		set_file_buffer($fp, 0);
		flock($fp, LOCK_EX);
		rewind($fp);
		fputs($fp, $data);
		flock($fp, LOCK_UN);
		fclose($fp);
	}

	/**
	 * キャッシュを読み出す
	 * 引数は添付ファイル名
	 * 変換されたファイルデータを返す
	 */
	function _plugin_code_read_cache($fname, & $option)
	{
	//	global $vars;
		// 添付ファイルのあるページ: defaultは現在のページ名
		$page = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';

		// ファイル名にページ名(ページ参照パス)が合成されているか
		//   (Page_name/maybe-separated-with/slashes/ATTACHED_FILENAME)
		if (preg_match('#^(.+)/([^/]+)$#', $fname, $matches)) {
			if ($matches[1] == '.' || $matches[1] == '..')
				$matches[1] .= '/'; // Restore relative paths
			$fname = $matches[2];
			$page = $this->func->get_fullname($this->func->strip_bracket($matches[1]), $page); // strip is a compat
			$file = $this->func->encode($page) . '_' . $this->func->encode($fname);
		} else {
			// Simple single argument
			$file =  $this->func->encode($page) . '_' . $this->func->encode($fname);
		}

		/* Read file data */
		// md5
		list(,,,,,,,,$md5) = array_pad(@ file($this->cont['UPLOAD_DIR'].$file.".log"), 9, '');
		$md5 = trim($md5);

		$cache = $this->cont['CACHE_DIR'].'plugin/'.$file.(($this->cont['UA_PROFILE'] === 'keitai')? '.k' : '').'.code';
		if (!is_file($cache)) {
			$option['id'] = $md5;
			return false;
		}
		$fdata = file_get_contents($cache);

		$dat = unserialize($fdata);
		//md5ハッシュの検査
		if ($dat[2] !== $md5) {
			$option['id'] = $md5;
			return false;
		}

		return $dat;
	}

	/**
	 * 整形済出力
	 * @author sky
	 *
	 * Code.inc.php Ver. 0.5
	 */
	function plugin_pre_convert()
	{
	//	static $id_number = 0; // プラグインが呼ばれた回数(IDに利用)
		static $id_number = array();
		if (!isset($id_number[$this->xpwiki->pid])) {$id_number[$this->xpwiki->pid] = 0;}
		$id_number[$this->xpwiki->pid]++;

	    $option = array(
	                  'number'      => 0,  // 行番号を表示する
	                  'nonumber'    => 0,  // 行番号を表示しない
					  'hard'        => 0,  // インライン展開しない
					  'soft'        => 0,  // インライン展開する
					  'icon'        => 0,  // アイコンを表示する
					  'noicon'      => 0,  // アイコンを表示しない
	                  'link'        => 0,  // オートリンク 有効
	                  'nolink'      => 0,  // オートリンク 無効
	                  'title'       => '',
	              );
	    $num_of_arg = func_num_args();
		$args = func_get_args();

		$text = '';
		$number = '';

		$style = '';
		$stylecnt = 0;

		$begin = 1;
		$end = null;
		$lang = null;

		$a = array();

	    // オプションを調べる
	    for ($i = 0;$i < $num_of_arg-1; ++$i) {
	        if (! $this->_plugin_code_check_argment($args[$i], $option)) {
				if (! $this->_plugin_code_get_region($args[$i], $end, $begin)) {
					// style
					if ($stylecnt == 0) {
						$color   = $args[$i];
						++$stylecnt;
					} else {
						$bgcolor = $args[$i];
					}
				}
			}
	    }
		if ($stylecnt) {
			// Invalid color
			foreach(array($color, $bgcolor) as $col){
				if ($col != '' && ! preg_match($this->cont['PLUGIN_PRE_COLOR_REGEX'], $col))
					return '<p class="error">#pre():Invalid color: '.htmlspecialchars($col).';</p>';
			}
			if ($color != '' ) {
				$style   = ' style="color:'.$color;
				if ($bgcolor != '')
					$style .= ';background-color:'.$bgcolor.'"';
				else
					$style .= '"';
			} else {
				if ($bgcolor != '')
					$style .= ' style="background-color:'.$bgcolor.'"';
				else
					$style = '';
			}
		}

		$this->_plugin_code_multiline_argment($args[$num_of_arg-1], $data, $lang, $option, $end, $begin);
		if (isset($data['_error']) && $data['_error'] != '') {
			return $data['_error'];
		}
		$text = $data['data'];
		$title = @$data['title'];

		if ($end === null)
			$end = substr_count($text, "\n") + $begin -1;

		if ($this->cont['PLUGIN_PRE_VERVATIM_HARD']  && ! $option['soft']  || $option['hard']) {
			$text = htmlspecialchars($text);
		} else {
			$text = $this->func->make_link($text);
		}
		$html = '<pre class="'.$this->cont['PLUGIN_PRE_HEADER'].'body" '.$style.'>'.$text.'</pre>';

		if ($this->cont['PLUGIN_PRE_NUMBER']  && ! $option['nonumber']  || $option['number']) {
			$number = '<pre class="'.$this->cont['PLUGIN_PRE_HEADER'].'number">'
			.$this->_plugin_code_makeNumber($end, $begin).'</pre>';
			$html = '<div id="'.$this->cont['PLUGIN_PRE_HEADER'].$id_number[$this->xpwiki->pid].'" class="'.$this->cont['PLUGIN_PRE_HEADER'].'table">'
			.$this->_plugin_code_column($this->cont, $html, $number, null). '</div>';
		}

		return '<div class="pre">' . $title.$html . '</div>';
	}

	/* pre.inc.php と共用 */

	/**
	 * 最終引数を解析する
	 * 引数はプラグインへの最後の引数の内容
	 * 複数行引数の場合に真を返す
	 */
	function _plugin_code_multiline_argment(& $arg, & $data, & $lang, & $option, $end = null, $begin = 1)
	{
	    // 改行コード変換
		$arg = str_replace(array("\r\n", "\r"), "\n", $arg);

	    // 最後の文字が改行でない場合は外部ファイル
	    if ($arg[strlen($arg)-1] != "\n") {
			// 言語自動判定
			if ($lang === null) {
				$lang = $this->_plugin_code_extension($arg);
			}
			if ($lang === null) {
				if ($this->_plugin_code_mimetype($arg)) {
					$data['_error'] = '<p class="error">Maybe file extension like binary. '.htmlspecialchars($arg).';</p>';
					return 0;
				} else {
					$lang = 'pre';
				}
			}

	        $params = $this->_plugin_code_read_file_data($arg, $end, $begin);
	        if (isset($params['_error']) && $params['_error'] != '') {
	            $data['_error'] = '<p class="error">'.$params['_error'].';</p>';
	            return 0;
	        }
	        $data['data'] = $params['data'];
	        if ($data['data'] == "\n" || $data['data'] == '' || $data['data'] == null) {
	            $data['_error'] ='<p class="error">file '.htmlspecialchars($params['title']).' is empty.</p>';
	            return 0;
	        }
			if ($this->cont['PLUGIN_CODE_FILE_ICON'] && !$option['noicon'] || $option['icon']) {
				$icon = $this->cont['FILE_ICON'];
			} else {
				$icon = '';
			}

	        @$data['title'] .= '<h5 class="'.$this->cont['PLUGIN_CODE_HEADER'].'title">'.'<a href="'.$params['url'].'" title="'.$params['info'].'">'.$icon.$params['title'].'</a></h5>'."\n";

		} else {
			$data['data'] = $arg;
			if ($option['title']) $data['title'] = '<h5 class="'.$this->cont['PLUGIN_CODE_HEADER'].'title">'.htmlspecialchars($option['title']).'</h5>'."\n";
			return 1;
		}
		return 0;
	}

	/**
	 * バイナリファイルの拡張子を検索する
	 *
	 */
	function _plugin_code_mimetype($name)
	{
		$extension = strtolower(substr($name, strrpos($name, '.')+1));
		if (!$extension) return null;

		// mime-type一覧表を取得
		$config = new XpWikiConfig($this->xpwiki, $this->cont['PLUGIN_CODE_CONFIG_PAGE_MIME']);
		$table = $config->read() ? $config->get('mime-type') : array();
		unset($config); // メモリ節約

		foreach ($table as $row) {
			$_type = trim($row[0]);
			$exts = preg_split('/\s+|,/', trim($row[1]), -1, PREG_SPLIT_NO_EMPTY);
			foreach ($exts as $ext) {
				if ($extension == $ext) return 1;
			}
		}
		return 0;
	}

	/**
	 * ファイル名から対応言語を検索する
	 *
	 */
	function _plugin_code_extension($name)
	{
		//$lang = $this->cont['PLUGIN_CODE_LANGUAGE']; // default

		//if (! is_file($filename)) return $lang;

		if (! strncasecmp($name, 'makefile', 8))
			return 'make';

		$extension = strtolower(substr($name, strrpos($name, '.')+1));
		if (!$extension) return null;

		// extension 一覧表を取得
		$config = new XpWikiConfig($this->xpwiki, $this->cont['PLUGIN_CODE_CONFIG_PAGE_EXTENSION']);
		$table = $config->read() ? $config->get('lang') : array();
		unset($config); // メモリ節約

		// search extension
		foreach ($table as $row) {
			$_lang = trim($row[0]);
	        $exts = preg_split('/\s+|,/', trim($row[1]), -1, PREG_SPLIT_NO_EMPTY);
			foreach ($exts as $ext) {
				if ($extension == $ext) return $_lang;
			}
		}
		return null;
	}

	/**
	 * 引数に与えられたファイルの内容を文字列に変換して返す
	 * 文字コードは PukiWikiと同一, 改行は \n である
	 */
	function _plugin_code_read_file_data($name, $end = null, $begin = 1) {
	//    global $vars;

		// Configファイルの存在チェック
		$c_name = $this->cont['PKWK_CONFIG_PREFIX'].$this->cont['PLUGIN_CODE_CONFIG_PAGE_MIME'];
		if (!$this->func->is_page($c_name)) {
			copy(dirname(__FILE__)."/code/wiki/".$this->func->encode($c_name).".txt", $this->func->get_filename($c_name));
		}
		$c_name = $this->cont['PKWK_CONFIG_PREFIX'].$this->cont['PLUGIN_CODE_CONFIG_PAGE_EXTENSION'];
		if (!$this->func->is_page($c_name)) {
			copy(dirname(__FILE__)."/code/wiki/".$this->func->encode($c_name).".txt", $this->func->get_filename($c_name));
		}

	    // 添付ファイルのあるページ: defaultは現在のページ名
	    $page = isset($this->root->vars['page']) ? $this->root->vars['page'] : '';

	    // 添付ファイルまでのパスおよび(実際の)ファイル名
	    $file = '';
	    $fname = $name;

	    $is_url = $this->func->is_url($fname);

	    /* Check file location */
	    if ($is_url) { // URL
			if (! $this->cont['PLUGIN_CODE_READ_URL']) {
	            $params['_error'] = 'Cannot assign URL';
	            return $params;
	        }
	        $url = htmlspecialchars($fname);
	        $params['title'] = htmlspecialchars(preg_match('/([^\/]+)$/', $fname, $matches) ? $matches[1] : $url);
	    } else {  // 添付ファイル
	        if (! is_dir($this->cont['UPLOAD_DIR'])) {
	            $params['_error'] = 'No UPLOAD_DIR';
	            return $params;
	        }

	        $matches = array();
	        // ファイル名にページ名(ページ参照パス)が合成されているか
	        //   (Page_name/maybe-separated-with/slashes/ATTACHED_FILENAME)
	        if (preg_match('#^(.+)/([^/]+)$#', $fname, $matches)) {
	            if ($matches[1] == '.' || $matches[1] == '..')
	                $matches[1] .= '/'; // Restore relative paths
	            $fname = $matches[2];
	            $page = $this->func->get_fullname($this->func->strip_bracket($matches[1]), $page); // strip is a compat
	            $file = $this->cont['UPLOAD_DIR'] . $this->func->encode($page) . '_' . $this->func->encode($fname);
	            $is_file = is_file($file);
	        } else {
	            // Simple single argument
	            $file = $this->cont['UPLOAD_DIR'] . $this->func->encode($page) . '_' . $this->func->encode($fname);
	            $is_file = is_file($file);
	        }

	        if (! $is_file) {
	            $params['_error'] = htmlspecialchars('File not found: "' .$fname . '" at page "' . $page . '"');
	            return $params;
	        }
	        $params['title'] = htmlspecialchars($fname);
	        $fname = $file;

			$url = $this->root->script . '?plugin=attach' . '&amp;refer=' . rawurlencode($page) .
			'&amp;openfile=' . rawurlencode($name); // Show its filename at the last
	    }

		$params['url'] = $url;
		$params['info'] = $this->func->get_date('Y/m/d H:i:s', filemtime($file) - $this->cont['LOCALZONE'])
			. ' ' . sprintf('%01.1f', round(filesize($file)/1024, 1)) . 'KB';

	    /* Read file data */
	    $fdata = '';
	    $filelines = file($fname);
		if ($end === null)
			$end = count($filelines);

		for ($i=$begin-1; $i<$end; ++$i)
	        $fdata .= str_replace("\r\n", "\n", $filelines[$i]);

	    $fdata = strtr($fdata, "\r", "\n");
	    $fdata = mb_convert_encoding($fdata, $this->cont['SOURCE_ENCODING'], "auto");

		// ファイルの最後を改行にする
		if($fdata[strlen($fdata)-1] != "\n")
			$fdata .= "\n";

		$params['data'] = $fdata;

	    return $params;
	}

	/**
	 * オプション解析
	 * 引数に対応するキーをOnにする
	 * キーをセットしたら"1"を返す
	 */
	function _plugin_code_check_argment(& $arg, & $option) {
	    $arg = strtolower($arg);
	    if (isset($option[$arg])) {
	        $option[$arg] = 1;
			return 1;
		} else if (strpos($arg, 'title') === 0) {
			$option['title'] = substr($arg, 6);
			return 1;
		}
		return 0;
	}

	/**
	 * 範囲指定を解析
	 * 呼び出し側の変数を設定する
	 * 範囲を設定したら"1"を返す
	 */
	function _plugin_code_get_region(& $option, & $end, & $begin)
	{
		if (false !== strpos($option, '-')) {
			$array = explode('-', $option);
		} else if (false !== strpos($option, '..')) {
			$array = explode('..', $option);
		} else {
			return 0;
		}

		if (is_numeric ($array[0]))
			if ($array[0] < 1)
				$begin = 1;
			else
				$begin = $array[0];
		else
			$begin = 1;
		if (is_numeric ($array[1]))
			$end = $array[1];
		else
			$end = null;

		return 1;
	}

	/**
	 * 行番号を作成する
	 * 引数は行番号の範囲
	 * 整形された行番号を返す
	 */
	function _plugin_code_makeNumber($end, $begin=1)
	{
		$number='';
		//$str_len=max(3,strlen(''.$end));
		$str_len = strlen(''.$end);
		for($i=$begin; $i<=$end; ++$i) {
			$number.= sprintf('%'.$str_len.'d',($i))."\n";
		}
		return $number;
	}

	/**
	 * 段組みして出力する
	 *
	 * 整形HTMLを返す
	 */
	function _plugin_code_column(& $const, & $text, $number=null, $outline=null)
	{
		if ($number === null && $outline === null)
			return $text;

		$html = '';
		if ($this->cont['PLUGIN_CODE_TABLE']) {
			$html .= '<table class="'.$this->cont['PLUGIN_CODE_HEADER']
				.'table" border="0" cellpadding="0" cellspacing="0"><tr>';
			if ($number !== null)
				$html .= '<td style="line-height:'.$this->cont['PLUGIN_CODE_LINE_HEIGHT'].'em;">'.$number.'</td>';
			if ($outline !== null)
				$html .= '<td style="line-height:'.$this->cont['PLUGIN_CODE_LINE_HEIGHT'].'em;">'.$outline.'</td>';
			$html .= '<td style="line-height:'.$this->cont['PLUGIN_CODE_LINE_HEIGHT'].'em;">'.$text.'</td></tr></table>';
		} else {
			if ($number !== null)
				$html .= '<div class="'.$this->cont['PLUGIN_CODE_HEADER'].'number" style="line-height:'.$this->cont['PLUGIN_CODE_LINE_HEIGHT'].'em;">'.$number.'</div>';
			if ($outline !== null)
				$html .= '<div class="'.$this->cont['PLUGIN_CODE_HEADER'].'outline" style="line-height:'.$this->cont['PLUGIN_CODE_LINE_HEIGHT'].'em;">'.$outline.'</div>';
			$html .= '<div class="'.$this->cont['PLUGIN_CODE_HEADER'].'src" style="line-height:'.$this->line_height.'em">'.$text.'</div>'
			. '<div style="clear:both;"><br style="display:none;" /></div>';
		}

		return $html;
	}
}
?>