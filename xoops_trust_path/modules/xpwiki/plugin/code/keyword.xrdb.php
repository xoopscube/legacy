<?php
/**
 * Xrdb キーワード定義ファイル
 */

// コメント定義
$switchHash['!'] = $this->cont['PLUGIN_CODE_COMMENT'];	// コメントは ! から改行まで
$code_comment = Array(
	'!' => Array(
				 Array('/^!/', "\n", 1),
	)
);

$code_css = Array(
);

$code_keyword = Array(
);?>