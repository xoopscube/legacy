<?php
class XpWikiCodeHighlight {
	var $id_number;
	var $blockno;
	var $outline;
	var $nestlevel;
	function XpWikiCodeHighlight(& $xpwiki)
	{
		$this->xpwiki =& $xpwiki;
		$this->root   =& $xpwiki->root;
		$this->cont   =& $xpwiki->cont;
		$this->func   =& $xpwiki->func;
		
		$this->cont['PLUGIN_CODE_HEADER'] =  'code_';
		
		// common
        $this->cont['PLUGIN_CODE_CODE_CANCEL'] =           0; // 指定を無効化する
        $this->cont['PLUGIN_CODE_IDENTIFIRE'] =            2; 
        $this->cont['PLUGIN_CODE_SPECIAL_IDENTIFIRE'] =    3; 
        $this->cont['PLUGIN_CODE_STRING_LITERAL'] =        5; 
        $this->cont['PLUGIN_CODE_NONESCAPE_LITERAL'] =     7; 
        $this->cont['PLUGIN_CODE_PAIR_LITERAL'] =          8; 
        $this->cont['PLUGIN_CODE_ESCAPE'] =               10;
        $this->cont['PLUGIN_CODE_COMMENT'] =              11;
        $this->cont['PLUGIN_CODE_COMMENT_WORD'] =         12; // コメントが文字列で始まるもの
        $this->cont['PLUGIN_CODE_FORMULA'] =              14; 
		// outline
        $this->cont['PLUGIN_CODE_BLOCK_START'] =          20;
        $this->cont['PLUGIN_CODE_BLOCK_END'] =            21;
        $this->cont['PLUGIN_CODE_STRING_CONCAT'] =        24;
		// 行指向用
        $this->cont['PLUGIN_CODE_COMMENT_CHAR'] =         50; // 1文字でコメントと決定できるもの
        $this->cont['PLUGIN_CODE_HEAD_COMMENT'] =         52; // コメントが行頭だけのもの (1文字)  // fortran
        $this->cont['PLUGIN_CODE_HEADW_COMMENT'] =        53; // コメントが行頭だけのもの   // pukiwiki
        $this->cont['PLUGIN_CODE_CHAR_COMMENT'] =         54; // コメントが行頭だけかつ英字であるのもの (1文字) // fortran
        $this->cont['PLUGIN_CODE_IDENTIFIRE_CHAR'] =      60; // 1文字で命令が決定するもの
        $this->cont['PLUGIN_CODE_IDENTIFIRE_WORD'] =      61; // 命令が文字列で決定するもの
        $this->cont['PLUGIN_CODE_MULTILINE'] =            62; // 複数文字列への命令

        $this->cont['PLUGIN_CODE_CARRIAGERETURN'] =       70; // 空行
		$this->cont['PLUGIN_CODE_POST_IDENTIFIRE'] =      75; // 文末の語よって決まるルール
	
		$this->blockno = 0;
		$this->outline = Array();// $outline[lineno][nest] $outline[lineno][blockno]がある。
	}
	//include_once(PLUGIN_DIR.'code/line.php');

    function highlight(& $lang, & $src, & $option, $end = null, $begin = 1) {
//		static $id = 0; // プラグインが呼ばれた回数(IDに利用)
		static $id = array();
		if (!isset($id[$this->xpwiki->pid])) {$id[$this->xpwiki->pid] = 0;}
               
        if (empty($option['id'])) {
			$this->id_number = ++$id[$this->xpwiki->pid];
        } else {
        	$this->id_number = $option['id'];
        }

		if (strlen($lang) > 16)
            $lang = '';
		
		$option['number']  = ($this->cont['PLUGIN_CODE_NUMBER']     && ! $option['nonumber']  || $option['number']);
		$option['outline'] = ($this->cont['PLUGIN_CODE_OUTLINE']    && ! $option['nooutline'] || $option['outline']);
		$option['block']   = ($this->cont['PLUGIN_CODE_BLOCK']      && ! $option['noblock']   || $option['block']);
		$option['literal'] = ($this->cont['PLUGIN_CODE_LITERAL']    && ! $option['noliteral'] || $option['literal']);
		$option['comment'] = ($this->cont['PLUGIN_CODE_SW_COMMENT'] && ! $option['nocomment'] || $option['comment']);
		$option['link']    = ($this->cont['PLUGIN_CODE_LINK']       && ! $option['nolink']    || $option['link']);

        // mozillaの空白行対策
        if($option['number'] || $option['outline']) {
            // ライン表示用補正
            $src = preg_replace('/^$/m',' ',$src);
        }
		if (is_file(dirname(__FILE__).'/keyword.'.$lang.'.php')) {
			// 言語定義ファイルが有る言語
			$data = $this->srcToHTML($src, $lang, $option, $end, $begin);
			$src = '<pre class="code"><code class="'.$lang.'">'.$data['src'].'</code></pre>';
		} else if (is_file(dirname(__FILE__).'/line.'.$lang.'.php')) {
			// 行指向解析設定ファイルが有る言語
			$data = $this->lineToHTML($src, $lang, $option, $end, $begin);
			$src = '<pre class="code"><code class="'.$lang.'">'.$data['src'].'</code></pre>';
		} else {
			// PHP と 未定義言語
			$option['outline'] = 0;

			// 最後の余分な改行を削除
			if ($src[strlen($src)-2] == ' ')
				$src = substr($src, 0, -2);
			else
				$src= substr($src, 0, -1);

			if ($option['number']) {
				if ($end === null) // 行数を得る
					$end = substr_count($src, "\n") + $begin - 1;
				if ($src[strlen($src)-1] == "\n")
					$src = substr($src,0,-1);
				$data = array('number' => '');
				$data['number'] = xpwiki_plugin_code::_plugin_code_makeNumber($end, $begin);
			}
			if ('php' == $lang) // PHPは標準機能を使う
				$src =  '<pre class="code">'.$this->highlightPHP($src). '</pre>';
			else // 未定義言語
				$src =  '<pre class="code"><code class="unknown">' .htmlspecialchars($src). '</code></pre>';
		}
		$option['menu']  = ($this->cont['PLUGIN_CODE_MENU']  && ! $option['nomenu']  || $option['menu']);
		$option['menu']  = ($option['menu'] && $option['outline']);

		$menu = '';
		if ($option['menu']) {
			// アイコンの設定
			$menu .= '<div class="'.$this->cont['PLUGIN_CODE_HEADER'].'menu">';
			if ($option['outline']) {
				// アウトラインのメニュー
				if ($option['block']) {
					$_code_expand = 'Everything is expanded.';
					$_code_short = 'Everything is shortened.';
					$menu .= '<img src="'.$this->cont['PLUGIN_CODE_OUTLINE_OPEN_FILE'].'" style="cursor: pointer" alt="'.$_code_expand.'" title="'.$_code_expand.'" '
						.'onclick="code_classname(\''.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number.'\','.$data['blocknum'].',\'\',\'code_block\')" '
						.'onkeypress="code_classname(\''.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number.'\','.$data['blocknum'].',\'\',\'code_block\')" />'
						.'<img src="'.$this->cont['PLUGIN_CODE_OUTLINE_CLOSE_FILE'].'" style="cursor: pointer" alt="'.$_code_short.'" title="'.$_code_short.'" '
						.'onclick="code_classname(\''.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number.'\','.$data['blocknum'].',\'none\',\'code_block\')" '
						.'onkeypress="code_classname(\''.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number.'\','.$data['blocknum'].',\'none\',\'code_block\')" />';
				}
			}
			$menu .= '</div>';
		}

		if ($option['number'])
			$data['number'] = '<pre class="'.$this->cont['PLUGIN_CODE_HEADER'].'number" style="width:auto;">'.$data['number'].'</pre>';
		else
			$data['number'] = null;

		if ($option['outline'])
			$data['outline'] = '<pre class="'.$this->cont['PLUGIN_CODE_HEADER'].'outline">'.$data['outline'].'</pre>';

		$html .= '<div id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number.'" class="'.$this->cont['PLUGIN_CODE_HEADER'].'table">'
			. $menu
			. xpwiki_plugin_code::_plugin_code_column($this->cont, $src, $data['number'], $data['outline'])
			. '</div>';

		return $html;
	}

	/**
	 * この関数は1行切り出す
	 * 定型フォーマットを持つ言語用
	 */
	function getline(& $string){
		$line = '';
		if(! $string[0]) return false;
		$pos = strpos($string, "\n"); // 改行まで切り出す
		if ($pos === false) { // 見つからないときは終わりまで
			$line = $string;
			$string = '';
		} else {
			$line = substr($string, 0, $pos+1);
			$string = substr($string, $pos+1);
		}
		return $line;
	}

	/**
	 * この関数は行頭の文字を判定して解析・変換する
	 * 定型フォーマットを持つ言語用
	 */
	function lineToHTML(& $string, & $lang, & $option, $end = null, $begin = 1) {

        // テーブルジャンプ用ハッシュ
        $switchHash = Array();
        $capital = false; // 大文字小文字を区別しない

		// 改行
		$switchHash["\n"] = $this->cont['PLUGIN_CODE_CARRIAGERETURN'];
		// エスケープ文字
        $switchHash['\\'] = $this->cont['PLUGIN_CODE_ESCAPE'];
        // 識別子開始文字
        for ($i = ord('a'); $i <= ord('z'); ++$i)
            $switchHash[chr($i)] = $this->cont['PLUGIN_CODE_IDENTIFIRE'];
        for ($i = ord('A'); $i <= ord('Z'); ++$i)
            $switchHash[chr($i)] = $this->cont['PLUGIN_CODE_IDENTIFIRE'];
        $switchHash['_'] = $this->cont['PLUGIN_CODE_IDENTIFIRE'];

        // 文字列開始文字
        $switchHash['"'] = $this->cont['PLUGIN_CODE_STRING_LITERAL'];
		$linemode = false; // 行内を解析するか否か

        $str_len = strlen($string);
        // 文字->html変換用ハッシュ
        $htmlHash = Array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;', '&' => '&amp;');
        $spaceHash = Array("\t" => $this->cont['PLUGIN_CODE_WIDTHOFTAB'], ' ' => ' ');
 
        // 言語定義ファイル読み込み
        include(dirname(__FILE__).'/line.'.$lang.'.php');
		
		$html = '';   // 出力されるHTMLコード付きソース
        $num_of_line = $begin-1;  // 行数をカウント
        $this->nestlevel = 1;// ネスト
        $this->blockno = 0;// 何番目のブロックか？IDをユニークにするために用いる
		$terminate = array();  // ブロック終端文字
		$str_continue = 0; // ブロックの種類

		$line = $this->getline($string);
		while($line !== false) {
			++$num_of_line;
			while ($line[strlen($line)-2] == '\\') {
				// 行末がエスケープ文字なら次の行も切り出す
				++$num_of_line;
				$line .= $this->getline($string);
			}
			// 行頭文字の判定
            switch ($switchHash[$line[0]]) {

			case $this->cont['PLUGIN_CODE_CHAR_COMMENT']:
			case $this->cont['PLUGIN_CODE_HEAD_COMMENT']:
			case $this->cont['PLUGIN_CODE_COMMENT_CHAR']:
				// 行頭の1文字でコメントと判断できるもの
				$line = htmlspecialchars(substr($line,0,-1), ENT_QUOTES);
				if ($option['link']) 
					$line = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
										 '<a href="$0">$0</a>',$line);
				
				if($str_continue != $this->cont['PLUGIN_CODE_COMMENT']) {
					if ($str_continue != 0) {
						$this->endRegion($num_of_line);
						$html .= '</span>';
					}
					if ($option['comment']) {
						$this->beginRegion($num_of_line);
						// アウトラインが閉じた時に表示する画像を埋め込む場所
						//$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'_img" display="none"></span>';
						$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'" class="'.$this->cont['PLUGIN_CODE_HEADER'].'comment">';
						$str_continue = $this->cont['PLUGIN_CODE_COMMENT'];
					}
				}
				// htmlに追加
				$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].'comment">'.$line.'</span>'."\n";

				$line = $this->getline($string); // next line
				continue 2;
				
			case $this->cont['PLUGIN_CODE_HEADW_COMMENT']:
			case $this->cont['PLUGIN_CODE_COMMENT_WORD']:
				// 2文字以上のパターンから始まるコメント
				if (strncmp($line, $commentpattern, strlen($commentpattern)) == 0) {
					$line = htmlspecialchars(substr($line,0,-1), ENT_QUOTES);
					if ($option['link']) 
						$line = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
											 '<a href="$0">$0</a>',$line);
					if($str_continue != $this->cont['PLUGIN_CODE_COMMENT']) {
						if ($str_continue != 0) {
							$this->endRegion($num_of_line-1);
							$html .= '</span>';
						}
						if ($option['comment']) {
							$this->beginRegion($num_of_line);
							// アウトラインが閉じた時に表示する画像を埋め込む場所
							//$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'_img" display="none"></span>';
							$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'" class="'.$this->cont['PLUGIN_CODE_HEADER'].'comment">';
							$str_continue = $this->cont['PLUGIN_CODE_COMMENT'];
						}
					}
					// htmlに追加
					$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].'comment">'.$line.'</span>'."\n";
					
					$line = $this->getline($string); // next line
					continue 2;
				}
				// コメントではない
				break;

			case $this->cont['PLUGIN_CODE_IDENTIFIRE_CHAR']:
				// 行頭の1文字が意味を持つもの
				if ($str_continue != 0) {
					$this->endRegion($num_of_line);
					$html .= '</span>';
					$str_continue = 0;
				}
				$index = $code_keyword[$line[0]];
				$line = htmlspecialchars($line, ENT_QUOTES);
				if ($option['link']) 
					$line = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
										 '<a href="$0">$0</a>',$line);
				if ($index != '')
					$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].$code_css[$index-1].'">'.$line.'</span>';
				else
					$html .= $line;

				$line = $this->getline($string); // next line
				continue 2;

			case $this->cont['PLUGIN_CODE_IDENTIFIRE_WORD']:
				if ($str_continue != 0) {
					$this->endRegion($num_of_line);
					$html .= '</span>';
					$str_continue = 0;
				}

				if (strlen($line) < 2 && $line[0] == ' ') break; // 空行判定
				// 行頭のパターンを調べる
				foreach ($code_identifire[$line[0]] as $pattern) {
					if (strncmp($line, $pattern, strlen($pattern)) == 0) {
						$index = $code_keyword[$pattern];
						// htmlに追加
						$line = htmlspecialchars($line, ENT_QUOTES);
						if ($option['link']) 
							$line = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
												 '<a href="$0">$0</a>',$line);
						if ($index != '')
							$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].$code_css[$index-1].'">'.$line.'</span>';
						else
							$html .= $line;
						
						$line = $this->getline($string); // next line
						continue 3;
					}
				}
				// 行頭の1文字が意味を持つものか判定
				$index = $code_keyword[$line[0]];
				if ($index != '') {
					$line = htmlspecialchars($line, ENT_QUOTES);
					$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].$code_css[$index-1].'">'.$line.'</span>';
					$line = $this->getline($string); // next line
					continue 2;
				}
				else // IDENTIFIREではない
					break;

			case $this->cont['PLUGIN_CODE_POST_IDENTIFIRE']:
				if ($str_continue != 0) {
					$this->endRegion($num_of_line);
					$html .= '</span>';
					$str_continue = 0;
				}
				// 行中の特定のパターンを検索する
				// makeのターゲット用 識別子(アルファベットから始まっている)
				$str_pos = strpos($line, $post_identifire);
				if ($str_pos !== false) {
					$result  = htmlspecialchars(substr($line, 0, $str_pos), ENT_QUOTES);
					$result2 = htmlspecialchars(substr($line, $str_pos+1), ENT_QUOTES);
					$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].'target">'.$result.$post_identifire.'</span>'
						.'<span class="'.$this->cont['PLUGIN_CODE_HEADER'].'src">'.$result2.'</span>';
					$line = $this->getline($string); // next line
					continue 2;
				}
				else // 該当しない
					break;

			case $this->cont['PLUGIN_CODE_MULTILINE']:
				// 複数行に渡って効果を持つ指定
				if ($str_continue != 0) {
					$this->endRegion($num_of_line);
					$html .= '</span>';
					$str_continue = 0;
				}

				$index = $code_keyword[$line[0]];
				$src = rtrim(htmlspecialchars($line, ENT_QUOTES));
				if ($option['link']) 
					$src = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
										'<a href="$0">$0</a>',$src);
				if ($index != '')
					$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].$code_css[$index-1].'">'.$src;
				else
					$html .= $src;
				// outline
				++$this->blockno;
				$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'_img" style="display:none;"></span>'
					."\n"
					.'<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'">';

				$line = $this->getline($string);
				$multilines = 0;
				$result = '';
				while (in_array($line[0], $multilineEOL) === false && $line !== false) {
					// 効果の範囲内を取得する
					$src = htmlspecialchars($line, ENT_QUOTES);
					if ($option['link']) 
						$src = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
											 '<a href="$0">$0</a>',$src);
					$result .= $src;
					++$multilines;
					$line = $this->getline($string);
				}
				if ($multilines >= 1) {
					if(! isset($this->outline[$num_of_line])) {
						$this->outline[$num_of_line]=Array();
					}
					array_push($this->outline[$num_of_line],Array('nest'=>($this->nestlevel+1), 'blockno'=>$this->blockno, 'state'=>1));
					$num_of_line += $multilines;
					$this->outline[$num_of_line] = Array();
					array_push($this->outline[$num_of_line],Array('nest'=>$this->nestlevel,'blockno'=>0, 'state'=>1));
				}
				
				$html .= $result;
				if ($index != '')
					$html .= '</span>';
				$html .= '</span>';
				continue 2;

			case $this->cont['PLUGIN_CODE_BLOCK_START']:
				// 特殊文字から始まる識別子
				if ($str_continue != 0) {
					$this->endRegion($num_of_line);
					$html .= '</span>';
					$str_continue = 0;
				}
				// 次の文字が英字か判定
				if (! ctype_alpha($line[1])) break;
				$result = substr($line, 1);
				preg_match('/[A-Za-z0-9_\-]+/', $result, $matches);
				$str_pos = strlen($matches[0]);
				$result = substr($line, 0, $str_pos+1);
				$r_result = rtrim(substr($line, $str_pos+1));
				// htmlに追加
				if($capital)
					$index = $code_keyword[strtolower($result)];// 大文字小文字を区別しない
				else
					$index = $code_keyword[$result];
				$result = htmlspecialchars($result, ENT_QUOTES);
 				if ($index != '')
					$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].$code_css[$index-1].'">'.$result.'</span>';
				else
					$html .= $result;

				if ($option['block'] && $r_result[strlen($r_result)-1] == '{') {
					$this->beginRegion($num_of_line, 1);
					$terminate[$this->nestlevel] = strlen($r_result) - strpos($r_result,'{');
					$html .= $r_result;
					// アウトラインが閉じた時に表示する画像を埋め込む場所
					$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'_img" style="display:none;"></span>';
					$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'" style="display:'.$display.'" class="'.$this->cont['PLUGIN_CODE_HEADER'].'block">'."\n";
				} else 
					$html .= $r_result."\n";

				$line = $this->getline($string); // next line
				continue 2;

			case $this->cont['PLUGIN_CODE_BLOCK_END']:
				// outline 表示終了文字 for PukiWikis
				if ($option['block'] && $terminate[$this->nestlevel] == strlen(trim($line))) {
					$this->endRegion($num_of_line);
					$html .= '</span>';
				}
				$html .= $line;
				$line = $this->getline($string); // next line
				continue 2;

 			default:
				// 行内を解析せずにHTMLに追加する (diff)
				if($linemode) {
					$line = htmlspecialchars($line, ENT_QUOTES);
					if ($option['link']) 
						$html .= preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
											  '<a href="$0">$0</a>',$line);
					
					$line = $this->getline($string); // next line
					continue 2;
				}
			} //switch
				
			// 行内の解析 1文字ずつ解析する
			$str_len = strlen($line);
			$str_pos = 0;
			if ($str_len == $str_pos) $code = false; else $code = $line[$str_pos++];// getc
			while($code !== false) {
				switch ($switchHash[$code]) {
				case $this->cont['PLUGIN_CODE_CHAR_COMMENT']: // 行頭以外ではコメントにはならない (fortran)
				case $this->cont['PLUGIN_CODE_IDENTIFIRE']:
					// 識別子(アルファベットから始まっている)
					if ($str_continue != 0) {
						$this->endRegion($num_of_line);
						$html .= '</span>';
						$str_continue = 0;
					}
					// 出来る限り長く識別子を得る
					--$str_pos; // エラー処理したくないからpreg_matchで必ず見つかるようにする
					$result = substr($line, $str_pos); 
					preg_match('/[A-Za-z0-9_\-]+/', $result, $matches);
					$str_pos += strlen($matches[0]);
					$result = $matches[0];
					
					if($capital)
						$index = $code_keyword[strtolower($result)];// 大文字小文字を区別しない
					else
						$index = $code_keyword[$result];
					$result = htmlspecialchars($result, ENT_QUOTES);

					if ($index!='')
						$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].$code_css[$index-1].'">'.$result.'</span>';
					else
						$html .= $result;
					
					// 次の検索用に読み込み
					if ($str_len == $str_pos) $code = false; else $code = $line[$str_pos++]; // getc
					continue 2;
					
				case $this->cont['PLUGIN_CODE_SPECIAL_IDENTIFIRE']:
					// 特殊文字から始まる識別子
					// 次の文字が英字か判定
					if ($str_continue != 0) {
						$this->endRegion($num_of_line);
						$html .= '</span>';
						$str_continue = 0;
					}
					if (! ctype_alpha($line[$str_pos])) break;
					$result = substr($line, $str_pos);
					preg_match('/[A-Za-z0-9_\-]+/', $result, $matches);
					$str_pos += strlen($matches[0]);
					$result = $code.$matches[0];
					// htmlに追加
					if($capital)
						$index = $code_keyword[strtolower($result)];// 大文字小文字を区別しない
					else
						$index = $code_keyword[$result];
					$result = htmlspecialchars($result, ENT_QUOTES);
					if ($index != '')
						$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].$code_css[$index-1].'">'.$result.'</span>';
					else
						$html .= $result;
					
					// 次の検索用に読み込み
					if ($str_len == $str_pos) $code = false; else $code = $line[$str_pos++]; // getc
					continue 2;

				case $this->cont['PLUGIN_CODE_STRING_LITERAL']:
				case $this->cont['PLUGIN_CODE_NONESCAPE_LITERAL']:
					if($str_continue != $this->cont['PLUGIN_CODE_STRING_LITERAL']) {
						if ($str_continue != 0) {
							$this->endRegion($num_of_line);
							$html .= '</span>';
						}
						if ($option['literal']) {
							$this->beginRegion($num_of_line);
							// アウトラインが閉じた時に表示する画像を埋め込む場所
							//$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'_img" display="none"></span>';
							$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'" class="'.$this->cont['PLUGIN_CODE_HEADER'].'string">';
							$str_continue = $this->cont['PLUGIN_CODE_STRING_LITERAL'];
						}
					}
					// 文字列リテラルを得る //現在エスケープする必要が無い
					$pos = $str_pos;
					$result = substr($line, $str_pos);
					$pos1 = strpos($result, $code); // 文字列終了文字検索
					if ($pos1 === false) { // 次を検索する
						$pos1 = strpos($string, $code); // 文字列終了文字検索
						if ($pos1 === false) { // 文字列が終わらなかったので全部文字列とする
							$num_of_line += substr_count($string, "\n")+1; // ライン数カウント
							// 最後の余分な改行を削除
							if ($string[strlen($string)-2] == ' ')
								$string = substr($string, 0, -2);
							else
								$string = substr($string, 0, -1);
							$result = $code.$result.$string;
							$str_len = 0;
							$str_pos = 0;
							$string = '';
							$code = false;
							$result = htmlspecialchars($result, ENT_QUOTES);
							if ($option['link']) 
								$result = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
													   '<a href="$0">$0</a>',$result);
							$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].'string">'.$result.'</span>';
							break 3;
						} else {
							$result = $code.$result.substr($string, 0, $pos1+2);
							$num_of_line += substr_count($result, "\n")-1; // ライン数カウント
							$string = substr($string, $pos1+2);
							if ($string[$pos1+2] == "\n") {
								$str_len = 0;
								$str_pos = 0;
								$code = false;
							} else {
								$code = $line[$str_pos++]; // getc
								$line = $this->getline($string);
								$str_len = strlen($line);
								$str_pos = 0;
							}
						}
					} else {
						$str_pos += $pos1 + 1;
						$result = $code.substr($line, $pos, $str_pos - $pos);
					}
					// htmlに追加
					$result = htmlspecialchars($result, ENT_QUOTES);
					if ($option['link']) 
						$result = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
											   '<a href="$0">$0</a>',$result);
					$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].'string">'.$result.'</span>';
					
					// 次の検索用に読み込み
					if ($str_len == $str_pos) $code = false; else $code = $line[$str_pos++]; // getc
					continue 2;

				case $this->cont['PLUGIN_CODE_COMMENT_CHAR']: // 1文字で決まるコメント
					$line = substr($line, $str_pos-1, $str_len-$str_pos);
					$line = htmlspecialchars($line, ENT_QUOTES);
					if ($option['link']) 
						$line = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
											 '<a href="$0">$0</a>',$line);
					if ($str_continue != 0) {
						$this->endRegion($num_of_line);
						$html .= '</span>';
					}
					$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].'comment">'.$line.'</span>'."\n";
					
					$line = $this->getline($string); // next line
					continue 3;
				} //switch
				// その他の文字
				$result = $spaceHash[$code];
				if ($result) {
					$html .= $result;
				} else {
					if ($str_continue != 0) {
						$this->endRegion($num_of_line);
						$html .= '</span>';
						$str_continue = 0;
					}
					$result = $htmlHash[$code];
					if ($result) {
						$html .= $result;
					} else {
						$html .= $code;
					}
				}
				// 次の検索用に読み込み
				if ($str_len == $str_pos) $code = false; else $code = $line[$str_pos++]; // getc

			}// char
			$line = $this->getline($string); // next line
		} // line
		
		// 最後の余分な改行を削除
		if ($html[strlen($html)-2] == ' ')
			$html = substr($html, 0, -2);
		else
			$html = substr($html, 0, -1);
		
		if (is_null($end)) $end = substr_count($html, "\n") + $begin - 1;
		$html = array('src' => $html, 'number' => '', 'outline' => '', 'blocknum' => $this->blockno);
		if($option['outline']) 
			return $this->makeOutline($html, $option['number'],$end, $begin);
		if($option['number']) $html['number'] = xpwiki_plugin_code::_plugin_code_makeNumber($end, $begin); 
		return $html;
	}
    /**
      * ソースからHTML生成
      */
    function srcToHTML(& $string, & $lang, & $option, $end = null, $begin = 1) {
        // テーブルジャンプ用ハッシュ
        $switchHash = Array();
        $capital = 0; // 大文字小文字を区別しない
		$mkoutline = $option['outline'];
		$mknumber  = $option['number'];

		// 改行
        $switchHash["\n"] = $this->cont['PLUGIN_CODE_CARRIAGERETURN'];

        $switchHash['\\'] = $this->cont['PLUGIN_CODE_ESCAPE'];
        // 識別子開始文字
        for ($i = ord('a'); $i <= ord('z'); ++$i)
            $switchHash[chr($i)] = $this->cont['PLUGIN_CODE_IDENTIFIRE'];
        for ($i = ord('A'); $i <= ord('Z'); ++$i)
            $switchHash[chr($i)] = $this->cont['PLUGIN_CODE_IDENTIFIRE'];
        $switchHash['_'] = $this->cont['PLUGIN_CODE_IDENTIFIRE'];

        // 文字列開始文字
        $switchHash['"'] = $this->cont['PLUGIN_CODE_STRING_LITERAL'];

        // 言語定義ファイル読み込み
        include(dirname(__FILE__).'/keyword.'.$lang.'.php');
		
        // 文字->html変換用ハッシュ
        $htmlHash = Array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;', '&' => '&amp;');
        $spaceHash = Array("\t" => $this->cont['PLUGIN_CODE_WIDTHOFTAB'], ' ' => ' ');

        $html = '';
        $str_len = strlen($string);
        $str_pos = 0;
        $num_of_line = $begin;  // 行数をカウント
        $this->nestlevel = 1;// ネスト
        $this->blockno = 0;// 何番目のブロックか？IDをユニークにするために用いる
		$terminate = array();  // ブロック終端文字
		$str_continue = 0; // ブロックの種類
		$startline = 1; // 行頭判定
		//$indentlevel = 0; // インデントの深さ

        // 最初の検索用に読み込み
        if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++];// getc
        while ($code !== false) {

            switch ($switchHash[$code]) {

			case $this->cont['PLUGIN_CODE_CARRIAGERETURN']: // 改行
				$startline = 1;
				if ($str_continue == $this->cont['PLUGIN_CODE_STRING_LITERAL']) {
					$result = ltrim(substr($string, $str_pos));
					$code = $result[0];
					switch ($switchHash[$code]) {
					case $this->cont['PLUGIN_CODE_STRING_LITERAL']:
					case $this->cont['PLUGIN_CODE_NONESCAPE_LITERAL']:
					case $this->cont['PLUGIN_CODE_PAIR_LITERAL']:
					case $this->cont['PLUGIN_CODE_STRING_CONCAT']:
						break;
					default:
						$this->endRegion($num_of_line);
						$html .= '</span>';
						$str_continue = 0;
					}
				} else if ($str_continue != 0) {
					$this->endRegion($num_of_line);
					$html .= '</span>';
					$str_continue = 0;
				}
				++$num_of_line;
				$html .="\n";

				// 次の検索用に読み込み
				if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
				continue 2;

			case $this->cont['PLUGIN_CODE_ESCAPE']:
				$startline = 0;
				if ($str_continue != 0) {
					$this->endRegion($num_of_line);
					$html .= '</span>';
					$str_continue = 0;
				}
				// escape charactor
				$start = $code;
				// 判定用にもう1文字読み込む
				if ($str_len == $str_pos)
					$code = false;
				else
					$code = $string[$str_pos++]; // getc
				if (ctype_alnum($code)) {
					// 文字(変数)なら終端まで見付ける
					--$str_pos; // エラー処理したくないからpreg_matchで必ず見つかるようにする
					$result = substr($string, $str_pos);
					preg_match('/[A-Za-z0-9_]+/', $result, $matches);
					$str_pos += strlen($matches[0]);
					$result = $matches[0];
				} else {
					// 記号なら1文字だけ切り出す
					$result = $code;
					if ($code == "\n") ++$num_of_line;
				}
				// htmlに追加
				$html .= htmlspecialchars($start.$result, ENT_QUOTES);
				
				// 次の検索用に読み込み
				if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
				continue 2;
			case $this->cont['PLUGIN_CODE_COMMENT']:
			case $this->cont['PLUGIN_CODE_HEAD_COMMENT']: // 行頭に限る
				// コメント
				--$str_pos;
				$result = substr($string, $str_pos);
				foreach($code_comment[$code] as $pattern) {
					if (($switchHash[$code] == $this->cont['PLUGIN_CODE_COMMENT'] || $startline) && preg_match($pattern[0], $result)) {
						if ($pattern[2] === 'reg') {
							if (preg_match($pattern[1], $result, $match)) {
								$result = $match[0];
								$str_pos += strlen($result);
							} else {
								// 見つからないときは終わりまで
								$str_pos = $str_len;
							}
						} else {
							$pos = strpos($result, $pattern[1]);
							if ($pos === false) { // 見つからないときは終わりまで
								$str_pos = $str_len;
								//$result = $result; ってことで何もしない
							} else {
								$pos += $pattern[2];
								$str_pos += $pos;
								$result = substr($result, 0, $pos);
							}
						}
						// ライン数カウント
						$commentlines = substr_count($result,"\n");
						
						if ($pattern[1] == "\n") {
							if($str_continue != $this->cont['PLUGIN_CODE_COMMENT']) {
								if ($str_continue != 0) {
									$this->endRegion($num_of_line-1);
									$html .= '</span>';
								}
								if ($option['comment'] && $startline) {
									$this->beginRegion($num_of_line);
									// アウトラインが閉じた時に表示する画像を埋め込む場所
									//$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'_img" display="none"></span>';
									$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'" class="'.$this->cont['PLUGIN_CODE_HEADER'].'comment">';
									$str_continue = $this->cont['PLUGIN_CODE_COMMENT'];
								}
							}
							++$num_of_line;
							$startline = 1;
						} else {
							if ($str_continue != 0) {
								$this->endRegion($num_of_line);
								$html .= '</span>';
								$str_continue = 0;
							}
							if ($option['comment']) {
								$is_comment = 0;
								if ($commentlines >= 1) {
									if(! isset($this->outline[$num_of_line])) {
										$this->outline[$num_of_line]=Array();
									}
									$this->beginRegion($num_of_line);
									$num_of_line += $commentlines;
									$this->endRegion($num_of_line);
								}
							} 
							else
								$num_of_line += $commentlines;
							$startline = 0;
						}
						
						// htmlに追加
						$result = str_replace('\t', $this->cont['PLUGIN_CODE_WIDTHOFTAB'], htmlspecialchars($result, ENT_QUOTES));
						if ($option['link']) 
							$result = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
												   '<a href="$0">$0</a>',$result);
						// アウトラインが閉じた時に表示する画像を埋め込む場所
						//$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'_img" display="none"></span>';
						$_id = $commentlines ? ' id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'"' : '';
						$html .= '<span'.$_id.' class="'.$this->cont['PLUGIN_CODE_HEADER'].'comment">'.$result.'</span>';

						// 次の検索用に読み込み
						if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
						continue 3;
					}
				}
				// コメントではない
				++$str_pos;
				break;
				
			case $this->cont['PLUGIN_CODE_COMMENT_WORD']:
				// 文字列から始まるコメント
				
				// 出来る限り長く識別子を得る
				--$str_pos;
				$result = substr($string, $str_pos);
				foreach($code_comment[$code] as $pattern) {
					if (preg_match($pattern[0], $result)) {
						if ($pattern[2] === 'reg') {
							if (preg_match($pattern[1], $result, $match)) {
								$result = $match[0];
								$str_pos += strlen($result);
							} else {
								// 見つからないときは終わりまで
								$str_pos = $str_len;
							}
						} else {
							$pos = strpos($result, $pattern[1]);
							if ($pos === false) { // 見つからないときは終わりまで
								$str_pos = $str_len;
								//$result = $result; ってことで何もしない
							} else {
								$pos += $pattern[2];
								$str_pos += $pos;
								$result = substr($result, 0, $pos);
							}
						}

						if($str_continue != $this->cont['PLUGIN_CODE_COMMENT']) {
							if ($str_continue != 0) {
								$this->endRegion($num_of_line);
								$html .= '</span>';
							}
							if ($option['comment'] && $startline) {
								$this->beginRegion($num_of_line);
								// アウトラインが閉じた時に表示する画像を埋め込む場所
								//$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'_img" display="none"></span>';
								$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'" class="'.$this->cont['PLUGIN_CODE_HEADER'].'comment">';
								$str_continue = $this->cont['PLUGIN_CODE_COMMENT'];
							}
						}
						++$num_of_line;
						$startline = 1;
						// htmlに追加
						$result = str_replace('\t', $this->cont['PLUGIN_CODE_WIDTHOFTAB'], htmlspecialchars($result, ENT_QUOTES));
						if ($option['link']) 
							$result = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
												   '<a href="$0">$0</a>',$result);
						$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].'comment">'.$result.'</span>';
						// 次の検索用に読み込み
						if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
						continue 3;
					}
				}
				++$str_pos;
				// コメントでなければ文字列 (break を使わない)
			case $this->cont['PLUGIN_CODE_IDENTIFIRE']:
				// 識別子(アルファベットから始まっている)
				
				// 出来る限り長く識別子を得る
				--$str_pos;// エラー処理したくないからpreg_matchで必ず見つかるようにする
				$result = substr($string, $str_pos);
				preg_match('/[A-Za-z0-9_\-]+/', $result, $matches);
				$str_pos += strlen($matches[0]);
				$result = $matches[0];
				
				// htmlに追加
				if($capital)
					$index = $code_keyword[strtolower($result)];// 大文字小文字を区別しない
				else
					$index = $code_keyword[$result];
				$result = htmlspecialchars($result, ENT_QUOTES);

				if ($str_continue != 0) {
					$this->endRegion($num_of_line);
					$html .= '</span>';
					$str_continue = 0;
				}
				// begin outline
				if ($option['block'] && isset($outline_def[$result])) {
					$status = $outline_def[$result];
					if ($option['outline'] && ! $status[1])
						$state = 0;
					else
						$state = 1;
					$display = $state?'':'none';
					if ($status[2] != 'startline' || $startline != 0) {
						$this->beginRegion($num_of_line, $state);
						$terminate[$this->nestlevel] = $status[0];
						$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].$code_css[$index-1].'">'.$result.'</span>'
							.'<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'_img" style="display:none;"></span>'
							.'<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'" class="'.$this->cont['PLUGIN_CODE_HEADER'].'block" style="display:'.$diplay.'">';
					} else {
						$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].$code_css[$index-1].'">'.$result.'</span>';
					}
				} else if ($result == $terminate[$this->nestlevel]) {
					$this->endRegion($num_of_line);
					$html .= '</span>';
					$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].$code_css[$index-1].'">'.$result.'</span>';
					//$pos1 = strpos($result2, "\n");
					// end outline
				} else if ($index != '')
					$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].$code_css[$index-1].'">'.$result.'</span>';
				else
					$html .= $result;
			  
				$startline = 0;
				// 次の検索用に読み込み
				if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
				continue 2;
				
			case $this->cont['PLUGIN_CODE_SPECIAL_IDENTIFIRE']:
				// 特殊文字から始まる識別子
				// 次の文字が英字か判定
				if (! ctype_alpha($string[$str_pos])) break;
				$result = substr($string, $str_pos);
				preg_match('/[A-Za-z0-9_\-]+/', $result, $matches);
				$str_pos += strlen($matches[0]);
				$result = $code.$matches[0];
				// htmlに追加
				if($capital)
					$index = $code_keyword[strtolower($result)];// 大文字小文字を区別しない
				else
					$index = $code_keyword[$result];
				$result = htmlspecialchars($result, ENT_QUOTES);
				
				if ($str_continue != 0) {
					$this->endRegion($num_of_line);
					$html .= '</span>';
					$str_continue = 0;
				}
				// begin outline
				if ($option['block'] && isset($outline_def[$result])) {
					$status = $outline_def[$result];
					if ($option['outline'] && ! $status[1])
						$state = 0;
					else
						$state = 1;
					$display = $state ? '' : 'none';

					$this->beginRegion($num_of_line, $state);
					$terminate[$this->nestlevel] = $status[0];

					$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].$code_css[$index-1].'">'.$result.'</span>'
						.'<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'_img" style="display:none;"></span>'
						.'<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'" class="'.$this->cont['PLUGIN_CODE_HEADER'].'block" style="display:'.$display.'">';
				} else if ($result == $terminate[$this->nestlevel]) {
					$result2 = substr($string, $str_pos);
					$html .= '</span>';
					$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].$code_css[$index-1].'">'.$result.'</span>';
					//$pos1 = strpos($result2, "\n");
					$this->endRegion($num_of_line);
					// end outline
				} else if ($index!='')
					$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].$code_css[$index-1].'">'.$result.'</span>';
				else
					$html .= $result;

				$startline = 0;
				// 次の検索用に読み込み
				if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
				continue 2;
			case $this->cont['PLUGIN_CODE_STRING_LITERAL']:
				// 文字列リテラルを得る
				$pos = $str_pos;
				do {
					$result = substr($string, $str_pos);
					$pos1 = strpos($result, $code); // 文字列終了文字検索
					if ($pos1 === false) { // 文字列が終わらなかったので全部文字列とする
						$str_pos = $str_len-1;
						break;
					}
					$str_pos += $pos1 + 1;
				} while ($string[$str_pos-2] == '\\'); // 前の文字がエスケープ文字なら続ける
				$result = $code.substr($string, $pos, $str_pos - $pos);
				if($str_continue != $this->cont['PLUGIN_CODE_STRING_LITERAL']) {
					if ($str_continue != 0) {
						$this->endRegion($num_of_line);
						$html .= '</span>';
					}
					if ($option['literal'] && $startline) {
						$this->beginRegion($num_of_line);
						// アウトラインが閉じた時に表示する画像を埋め込む場所
						//$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'_img" display="none"></span>';
						$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'" class="'.$this->cont['PLUGIN_CODE_HEADER'].'string">';
						$str_continue = $this->cont['PLUGIN_CODE_STRING_LITERAL'];
					}
				}
				// ライン数カウント
				$num_of_line += substr_count($result,"\n");
				$startline = 0;		
				// htmlに追加
				$result = htmlspecialchars($result, ENT_QUOTES);
				if ($option['link']) 
					$result = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
										   '<a href="$0">$0</a>',$result);
				$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].'string">'.$result.'</span>';
				
				// 次の検索用に読み込み
				if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
				continue 2;
				
			case $this->cont['PLUGIN_CODE_NONESCAPE_LITERAL']:
				// エスケープ文字と式展開を無視した文字列
				// 文字列リテラルを得る

				$pos = $str_pos;
				$result = substr($string, $str_pos);
				$pos1 = strpos($result, $code); // 文字列終了文字検索
				if ($pos1 === false) { // 文字列が終わらなかったので全部文字列とする
					$str_pos = $str_len-1;
				} else {
					$str_pos += $pos1 + 1;
				}
				$result = $code.substr($string, $pos, $str_pos - $pos);
				if($str_continue != $this->cont['PLUGIN_CODE_STRING_LITERAL']) {
					if ($str_continue != 0) {
						$this->endRegion($num_of_line);
						$html .= '</span>';
					}
					if ($option['literal'] && $startline) {
						$this->beginRegion($num_of_line);
						// アウトラインが閉じた時に表示する画像を埋め込む場所
						//$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'_img" display="none"></span>';
						$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'" class="'.$this->cont['PLUGIN_CODE_HEADER'].'string">';
						$str_continue = $this->cont['PLUGIN_CODE_STRING_LITERAL'];
					}
				}
				// ライン数カウント
				$num_of_line+=substr_count($result,"\n");
				$startline = 0;
				// htmlに追加
				$result = htmlspecialchars($result, ENT_QUOTES);
				if ($option['link']) 
					$result = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
										   '<a href="$0">$0</a>',$result);
				$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].'string">'.$result.'</span>';
				
				// 次の検索用に読み込み
				if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
				continue 2;
				
			case $this->cont['PLUGIN_CODE_PAIR_LITERAL']:
				$startline = 0;
				// 対記号で囲まれた文字列リテラルを得る PostScript
				$pos = $str_pos;
				do {
					$result = substr($string, $str_pos);
					$pos1 = strpos($result, $literal_delimiter); // 文字列終了文字検索
					if ($pos1 === false) { // 文字列が終わらなかったので全部文字列とする
						$str_pos = $str_len-1;
						break;
					}
					$str_pos += $pos1 + 1;
				} while ($string[$str_pos-2] == '\\'); // 前の文字がエスケープ文字なら続ける
				$result = $code.substr($string, $pos, $str_pos - $pos);
				
				if($str_continue != $this->cont['PLUGIN_CODE_STRING_LITERAL']) {
					if ($str_continue != 0) {
						$this->endRegion($num_of_line);
						$html .= '</span>';
					}
					if ($option['literal']) {
						$this->beginRegion($num_of_line);
						// アウトラインが閉じた時に表示する画像を埋め込む場所
						//$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'_img" display="none"></span>';
						$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'" class="'.$this->cont['PLUGIN_CODE_HEADER'].'string">';
						$str_continue = $this->cont['PLUGIN_CODE_STRING_LITERAL'];
					}
				}
				// ライン数カウント
				$num_of_line+=substr_count($result,"\n");
				
				// htmlに追加
				$result = htmlspecialchars($result, ENT_QUOTES);
				if ($option['link']) 
					$result = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
										   '<a href="$0">$0</a>',$result);
				$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].'string">'.$result.'</span>';
				
				// 次の検索用に読み込み
				if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
				continue 2;
			case $this->cont['PLUGIN_CODE_STRING_CONCAT']:
				$startline = 0;
				$result = $htmlHash[$code];
				if ($result) 
					$html .= $result;
				else
					$html .= $code;
				
				// 次の検索用に読み込み
				if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
				continue 2;
			case $this->cont['PLUGIN_CODE_FORMULA']:
				$startline = 0;
				if ($str_continue != 0) {
					$this->endRegion($num_of_line);
					$html .= '</span>';
				}
				// TeXの数式に使用 将来的には汎用性を持たせる 
				$pos = $str_pos;
				$result = substr($string, $str_pos);
				$pos1 = strpos($result, $code); // 文字列終了文字検索
				if ($pos1 === false) { // 文字列が終わらなかったので全部文字列とする
					$str_pos = $str_len-1;
				} else {
					$str_pos += $pos1 + 1;
				}
				$result = $code.substr($string, $pos, $str_pos - $pos);
				
				// htmlに追加
				$result = htmlspecialchars($result, ENT_QUOTES);
				if ($option['link']) 
					$result = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
										   '<a href="$0">$0</a>',$result);
				$html .= '<span class="'.$this->cont['PLUGIN_CODE_HEADER'].'formula">'.$result.'</span>';
				
				// 次の検索用に読み込み
				if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
				continue 2;
				
			case $this->cont['PLUGIN_CODE_BLOCK_START']:
				$startline = 0;
				if ($str_continue != 0) {
					$this->endRegion($num_of_line);
					$html .= '</span>';
				}
				$html .= $code;
				if ($option['block']) {
					// outline 表示用開始文字 {, (
					$this->beginRegion($num_of_line);
					// アウトラインが閉じた時に表示する画像を埋め込む場所
					$html .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'_img" style="display:none;"></span>'
						.'<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$this->id_number._.$this->blockno.'" class="'.$this->cont['PLUGIN_CODE_HEADER'].'block">';
				}
				if ($str_len == $str_pos)
					$code = false;
				else
					$code = $string[$str_pos++]; // getc
				continue 2;
				
			case $this->cont['PLUGIN_CODE_BLOCK_END']:
				$startline = 0;
				if ($str_continue != 0) {
					$this->endRegion($num_of_line);
					$html .= '</span>';
				}
				if ($option['block']) {
					// outline 表示終了文字 }, )
					$this->endRegion($num_of_line);
					$html .= '</span>';
				}
				$html .= $code;
				if ($str_len == $str_pos)
					$code = false;
				else
					$code = $string[$str_pos++]; // getc
				continue 2;
				
            }// switch
			// その他の文字
            $result = $spaceHash[$code];
            if ($result) {
  				$html .= $result;
			} else {
				$startline = 0;
				if ($str_continue != 0) {
					$this->endRegion($num_of_line);
					$html .= '</span>';
					$str_continue = 0;
				}
				$result = $htmlHash[$code];
				if ($result) {
					$html .= $result;
				} else {
					$html .= $code;
				}
			}
            // 次の検索用に読み込み
            if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc

        }// while

		// 最後の余分な改行を削除
		if ($html[strlen($html)-2] == ' ')
			$html = substr($html, 0, -2);
		else
			$html = substr($html, 0, -1);

		if (is_null($end)) $end = substr_count($html, "\n") + $begin - 1;
		$html = array('src' => $html, 'number' => '', 'outline' => '', 'blocknum' => $this->blockno);
		if($option['outline']) 
			return $this->makeOutline($html, $option['number'],$end, $begin); // 最後に改行を削除したため -1
		if($option['number']) $html['number'] = xpwiki_plugin_code::_plugin_code_makeNumber($end, $begin); 
		return $html;
	}

	/**
	 * アウトラインの開始設定
	 */
	function beginRegion($line, $state=1) 
	{
		++$this->blockno;
		++$this->nestlevel;

		if(! isset($this->outline[$line])) {
			$this->outline[$line]=Array();
		}
		array_push($this->outline[$line],Array('nest'=>$this->nestlevel, 'blockno'=>$this->blockno, 'state'=>$state));
	}
	/**
	 * アウトラインの終了設定
	 */
	function endRegion($line) 
	{
		--$this->nestlevel;
		if(! isset($this->outline[$line])) {
			$this->outline[$line]=Array();
			array_push($this->outline[$line],Array('nest'=>$this->nestlevel,'blockno'=>0, 'state'=>1));
		} else {
			$old = array_pop($this->outline[$line]);
			if($old['blockno']!=0 && ($this->nestlevel+1) == $old['nest']) {
			} else {
				if(! is_null($old))
					array_push($this->outline[$line],$old);
				array_push($this->outline[$line],Array('nest'=>$this->nestlevel,'blockno'=>0, 'state'=>1));
			}
		}
	}

	/**
	 * outline の形成
	 */
	function makeOutline(& $html, $mknumber, $end, $begin = 1)
	{
		while($this->nestlevel>1) {// ネストがちゃんとしてなかった場合の対策
			$html['src'] .= '</span>';
			--$this->nestlevel;
		}
		$outline = '';
		$number = '';
		$this->nestlevel = 1;

		//$linelen=$line+1;
		//$str_len = max(3, strlen(''.$end-1));
		$str_len = strlen(''.$end);
		
		for($i=$begin; $i<=$end; ++$i) {
			$plus = '';
			$plus1 = '';
			$plus2 = '';
			$minus = '';
			if(isset($this->outline[$i])) {
				while(1) {
					$array = array_shift($this->outline[$i]);
					if (is_null($array))
						break;
					if ($this->nestlevel <= $array['nest']) {
						$id = $this->id_number.'_'.$array['blockno'];
						$display = $array['state'] ? '' : 'none';
						$letter = $array['state'] ? '-' : '+';
						//$letter = $array['state'] ? '<img src="'.$this->cont['IMAGE_DIR'].'treemenu_triangle_open.png"  width="9" height="9" alt="-" title="close"  class="treemenu" />' : '<img src="'.$this->cont['IMAGE_DIR'].'treemenu_triangle_close.png" width="9" height="9" alt="+" title="open" class="treemenu" />';
						if($plus == '')
							$plus = '<a class="'.$this->cont['PLUGIN_CODE_HEADER'].'outline" href="#" onclick="code_outline(\''
								.$this->cont['PLUGIN_CODE_HEADER'].$id.'\');return false;" id="'.$this->cont['PLUGIN_CODE_HEADER'].$id.'a" name="'.$this->cont['PLUGIN_CODE_HEADER'].$id.'a">'.$letter.'</a>';
						$plus1 .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$id.'o" style="display:'.$display.'">';
						$plus2 .= '<span id="'.$this->cont['PLUGIN_CODE_HEADER'].$id.'n" style="display:'.$display.'">';
						$this->nestlevel = $array['nest'];
					} else {
						$this->nestlevel = $array['nest'];
						$minus .= '</span>';
					}
				}
			}
			if($mknumber) {
				$number.= sprintf('%'.$str_len.'d',($i)).$minus.$plus2."\n";
			}
			if($plus == '' && $minus == '') {
				if($this->nestlevel == 1)
					$outline.=' ';
				else
					$outline .= '|';
			} else if($plus != '' && $minus == '') {
				$outline.= $plus.$plus1;
			} else if($plus == '' && $minus != '') {
				$outline .=  '!'.$minus;
			} else if($plus != '' && $minus != '') {
				$outline .= $plus.$minus.$plus1;
			}
			$outline.="\n";
		}
		while ($this->nestlevel>1) {// ネストがちゃんとしてなかった場合の対策
			$number .= '</span>';
			$outline .= '</span>';
			--$this->nestlevel;
		}
		$html['number'] = $number;
		$html['outline'] = $outline;
		return $html;
	}

	/**
	 * PHPを標準関数でハイライトする
	 */
	function highlightPHP($src) {
		// phpタグが存在するか？
		$phptagf = 0;
		if(! strstr($src,'<?php')) {
			$phptagf = 1;
			$src='<'.'?php '.$src;
		}
		if (version_compare(PHP_VERSION, '4.2.0', '>=')) {
			$html = highlight_string($src, TRUE);
		} else {
			ob_start(); //出力のバッファリングを有効に
			highlight_string($src); //phpは標準関数でハイライト
			$html = ob_get_contents(); //バッファの内容を得る
			ob_end_clean(); //バッファクリア?
		}
		// phpタグを取り除く。
		if ($phptagf) {
			$html = preg_replace('/(<(span|font)[^>]*>)?&lt;\?(?:<\/\\2><\\2[^>]*>)?php(?: |&nbsp;)(<\/\\2>)?/','$1$3',$html);
			$html = preg_replace('/<(span|font)[^>]*><\/\\1>/', '', $html);
		}
		$html = str_replace('&nbsp;', ' ', $html);
		$html = str_replace("\n", '', $html); //$html内の"\n"を''で置き換える
		$html = str_replace('<br />', "\n", $html);
		//Vaild XHTML 1.1 Patch (thanks miko)
		$html = str_replace('<font color="', '<span style="color:', $html);
		$html = str_replace('</font>', '</span>', $html);
		return $html;
	}
}
?>