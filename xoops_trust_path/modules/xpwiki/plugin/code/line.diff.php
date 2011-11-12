<?php
/**
 * diff キーワード定義ファイル
 * 行指向モード用
 */

$switchHash['!'] = $this->cont['PLUGIN_CODE_IDENTIFIRE_CHAR'];   // changed
$switchHash['|'] = $this->cont['PLUGIN_CODE_IDENTIFIRE_CHAR'];   // changed
$switchHash['+'] = $this->cont['PLUGIN_CODE_IDENTIFIRE_WORD'];   // added
$switchHash['>'] = $this->cont['PLUGIN_CODE_IDENTIFIRE_CHAR'];   // added
$switchHash[')'] = $this->cont['PLUGIN_CODE_IDENTIFIRE_CHAR'];   // added
$switchHash['-'] = $this->cont['PLUGIN_CODE_IDENTIFIRE_WORD'];   // removed
$switchHash['<'] = $this->cont['PLUGIN_CODE_IDENTIFIRE_CHAR'];   // removed
$switchHash['('] = $this->cont['PLUGIN_CODE_IDENTIFIRE_CHAR'];   // removed
$switchHash['*'] = $this->cont['PLUGIN_CODE_IDENTIFIRE_CHAR'];   // control
$switchHash['\\']= $this->cont['PLUGIN_CODE_IDENTIFIRE_CHAR'];   // control
$switchHash['@'] = $this->cont['PLUGIN_CODE_IDENTIFIRE_CHAR'];   // control

$mkoutline = $option['outline'] = 0; // アウトラインモード不可 
$linemode = 1; // 行内を解析しない

// 
$code_identifire = array(
	 '-' => Array(
		  '---',
		 ),
	 '+' => Array(
		  '+++',
		 ),
	 );


// コメント定義
$switchHash['#'] = $this->cont['PLUGIN_CODE_COMMENT'];	// コメントは # から改行まで

$code_css = Array(
					   'changed', //
					   'added',   //
					   'removed', //

					   'system', //
);

$code_keyword = Array(
						   '!' => 1,
						   '|' => 1,

						   '+' => 2,
						   '>' => 2,
						   ')' => 2,
						   '/' => 2,

						   '-' => 3,
						   '<' => 3,
						   '(' => 3,
						   '\\' => 3,

						   '*' => 4,
						   '\\' => 4,
						   '@' => 4,
						   '---' => 4,
						   '+++' => 4,
);?>