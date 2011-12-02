<?php
function b_xpwiki_notification_show( $options )
{
	$mydirname = empty( $options[0] ) ? 'xpwiki' : $options[0] ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$pgid = (!empty($_GET['pgid']))? intval($_GET['pgid']) : 0;

	if (!isset($GLOBALS['Xpwiki_'.$mydirname]) || !$pgid) return false;

	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_block_notification.html' : trim( $options[1] ) ;

	include_once XOOPS_TRUST_PATH."/modules/xpwiki/include.php";
	$xw =& XpWiki::getInitedSingleton($mydirname);

	$notification = $xw->func->get_notification_select($pgid);

	if ($notification) {
		$block = array(
			'mydirname' => $mydirname ,
			'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
			'content'  => $notification ,
		) ;
		require_once XOOPS_ROOT_PATH.'/class/template.php' ;
		$tpl = new XoopsTpl() ;
		$tpl->assign( 'block' , $block ) ;
		$ret['content'] = $tpl->fetch( $this_template ) ;
	} else {
		$ret = false;
	}
	return $ret;
}

function b_xpwiki_notification_edit( $options )
{
	$mydirname = empty( $options[0] ) ? 'xpwiki' : $options[0] ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$defs[1] = 'db:'.$mydirname.'_block_notification.html';
	$this_template = empty( $options[1] ) ? $defs[1] : trim( $options[1] ) ;

	$form = "
		<input type='hidden' name='options[0]' value='$mydirname' />
		<label for='this_template'>"._MB_XPWIKI_THISTEMPLATE."</label>&nbsp;:
		<input type='text' size='40' name='options[1]' id='this_template' value='".htmlspecialchars($this_template,ENT_QUOTES)."' /> ( {$defs[1]} )
		<br />
	\n" ;

	return $form;
}

function b_xpwiki_a_page_show( $options )
{
	$mydirname = empty( $options[0] ) ? 'xpwiki' : $options[0] ;

	// 必要なファイルの読み込み (固定値:変更の必要なし)
	include_once XOOPS_TRUST_PATH."/modules/xpwiki/include.php";

	// インスタンス化 (引数: モジュールディレクトリ名)
	$xw = new XpWiki($mydirname);

	$page = empty( $options[1] ) ? '' : $options[1] ;
	$width = empty( $options[2] ) ? '100%' : $options[2] ;
	$this_template = empty( $options[3] ) ? 'db:'.$mydirname.'_block_a_page.html' : trim( $options[3] ) ;
	$div_class = empty( $options[4] ) ? 'xpwiki_b_' . $mydirname : $options[4];
	$css = isset( $options[5] ) ? $options[5] : NULL;
	$disabled_pagecache = empty($options[6])? false : true;
	$head_tag_place = empty($options[7])? 'body' : trim($options[7]);
	$configs = array();

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	// ページキャッシュを常に無効にする?
	if ($disabled_pagecache) {
		$configs['root']['pagecache_min'] = 0;
	}

	// ブロック用として取得 (引数: ページ名, 表示幅)
	list($str, $head) = $xw->get_html_for_block($page, $width, $div_class, $css, $configs, TRUE);

	// オブジェクトを破棄
	$xw = null;
	unset($xw);

	if ($head_tag_place === 'body' || !b_xpwiki_insert_headtag($head, $head_tag_place)) {
		$str = $head . $str;
	}

	$constpref = '_MB_' . strtoupper( $mydirname ) ;

	$block = array(
		'mydirname' => $mydirname ,
		'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
		'pagename' => $page ,
		'content'  => $str ,
	) ;

	$tpl = new XoopsTpl() ;
	$tpl->assign( 'block' , $block ) ;
	$ret['content'] = $tpl->fetch( $this_template ) ;
	return $ret ;
}

function b_xpwiki_a_page_edit( $options )
{
	$mydirname = empty( $options[0] ) ? 'xpwiki' : $options[0] ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$defs[2] = '100%';
	$defs[3] = 'db:'.$mydirname.'_block_a_page.html';
	$defs[4] = 'xpwiki_b_' . $mydirname;
	$defs[5] = 'main.css';
	$defs[6] = 'No';
	$defs[7] = 'body';

	$page = empty( $options[1] ) ? '' : $options[1] ;
	$width = empty( $options[2] ) ? $defs[2] : $options[2] ;
	$this_template = empty( $options[3] ) ? $defs[3] : trim( $options[3] ) ;
	$div_class = empty( $options[4] ) ? $defs[4] : trim( $options[4] );
	$css = isset( $options[5] ) ? trim( $options[5] ) : $defs[5];
	$disabled_pagecache = empty($options[6])? 1 : 0;
	$check_pagecache = array('', '');
	$check_pagecache[$disabled_pagecache] = ' checked="checked"';
	$head_tag_place = empty($options[7])? $defs[7] : trim($options[7]);
	$check_headtag = array('module' => '', 'block' => '', 'body' => '');
	$check_headtag[$head_tag_place] = ' checked="checked"';

	$form = "
		<input type='hidden' name='options[0]' value='$mydirname' />
		<label for='pagename'>"._MB_XPWIKI_PAGENAME."</label>&nbsp;:
		<input type='text' size='20' name='options[1]' id='pagename' value='".$page."' />
		<br />
		<label for='blockwidth'>"._MB_XPWIKI_WIDTH."</label>&nbsp;:
		<input type='text' size='20' name='options[2]' id='blockwidth' value='".$width."' /> ( {$defs[2]} )
		<br />
		<label for='this_template'>"._MB_XPWIKI_THISTEMPLATE."</label>&nbsp;:
		<input type='text' size='40' name='options[3]' id='this_template' value='".htmlspecialchars($this_template,ENT_QUOTES)."' /> ( {$defs[3]} )
		<br />
		<label for='divclass'>"._MB_XPWIKI_DIVCLASS."</label>&nbsp;:
		<input type='text' size='30' name='options[4]' id='divclass' value='".htmlspecialchars($div_class,ENT_QUOTES)."' /> ( {$defs[4]} )
		<br />
		<label for='this_css'>"._MB_XPWIKI_THISCSS."</label>&nbsp;:
		<input type='text' size='30' name='options[5]' id='this_css' value='".htmlspecialchars($css,ENT_QUOTES)."' /> ( {$defs[5]} )
		<br />
		<label>"._MB_XPWIKI_DISABLEDPAGECACHE."</label>&nbsp;:
		<input type='radio' name='options[6]' value='1'{$check_pagecache[0]} />Yes &nbsp; <input type='radio' name='options[6]' value='0'{$check_pagecache[1]} />No &nbsp; ( {$defs[6]} )
		<br />
		<label>"._MB_XPWIKI_HEAD_TAG_PLACE."</label>&nbsp;:
		<input type='radio' name='options[7]' value='module'{$check_headtag['module']} id='headtag_module' /><label for='headtag_module'>xoops_module_header</label> &nbsp; <input type='radio' name='options[7]' value='block'{$check_headtag['block']} id='headtag_block' /><label for='headtag_block'>xoops_block_header</label> &nbsp; <input type='radio' name='options[7]' value='body'{$check_headtag['body']} id='headtag_body' /><label for='headtag_body'>&lt;body&gt;(Inline)</label>
		<br />( {$defs[7]} )<br />
		\n" ;
	return $form;
}

function b_xpwiki_fusen_show( $options )
{
	$src = <<<EOD
#fusen
EOD;
	return b_xpwiki_block_show( $options, $src, true );
}

function b_xpwiki_menubar_show( $options )
{
	$src = <<<EOD
#menu
EOD;
	$options['menubar'] = TRUE;
	return b_xpwiki_block_show( $options, $src, true );
}

function b_xpwiki_block_show( $options, $src, $nocache = false )
{
	$mydirname = empty( $options[0] ) ? 'xpwiki' : $options[0] ;

	// 必要なファイルの読み込み (固定値:変更の必要なし)
	include_once XOOPS_TRUST_PATH."/modules/xpwiki/include.php";

	// インスタンス化 (引数: モジュールディレクトリ名)
	$xw = new XpWiki($mydirname);

	$width = empty( $options[1] ) ? '100%' : $options[1] ;
	$this_template = empty( $options[2] ) ? 'db:'.$mydirname.'_block_a_page.html' : trim( $options[2] ) ;
	$div_class = empty( $options[3] ) ? 'xpwiki_b_' . $mydirname : $options[3];
	$css = isset( $options[4] ) ? $options[4] : NULL;
	$head_tag_place = empty($options[5])? 'module' : trim($options[5]);

	$configs = array();

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	// ページキャッシュを常に無効にする
	if ($nocache) $configs['root']['pagecache_min'] = 0;

	// Wikiソース
	$arg = array('source' => $src);

	// ブロック用として取得 (引数: Wikiソース, 表示幅)
	list($str, $head) = $xw->get_html_for_block($arg, $width, $div_class, $css, $configs, TRUE);

	// MenuBar の ページCSS を読み込み
	if (isset($options['menubar'])) {
		$head .= $xw->func->get_page_css_tag('MenuBar');
	}

	// オブジェクトを破棄
	$xw = null;
	unset($xw);

	if ($head_tag_place === 'body' || !b_xpwiki_insert_headtag($head, $head_tag_place)) {
		$str = $head . $str;
	}

	if (! $str) return FALSE;

	$block = array(
		'mydirname' => $mydirname ,
		'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
		'pagename' => '' ,
		'content'  => $str ,
	) ;

	$tpl = new XoopsTpl() ;
	$tpl->assign( 'block' , $block ) ;
	$ret['content'] = $tpl->fetch( $this_template ) ;
	return $ret ;
}

function b_xpwiki_block_edit( $options )
{
	$mydirname = empty( $options[0] ) ? 'xpwiki' : $options[0] ;
	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$defs[1] = '100%';
	$defs[2] = 'db:'.$mydirname.'_block_a_page.html';
	$defs[3] = 'xpwiki_b_' . $mydirname;
	$defs[4] = 'main.css';
	$defs[5] = 'module';

	$width = empty( $options[1] ) ? $defs[1] : $options[1] ;
	$this_template = empty( $options[2] ) ? $defs[2] : trim( $options[2] ) ;
	$div_class = empty( $options[3] ) ? $defs[3] : trim( $options[3] );
	$css = isset( $options[4] ) ? trim( $options[4] ) : $defs[4];
	$head_tag_place = empty($options[5])? $defs[5] : trim($options[5]);
	$check_headtag = array('module' => '', 'block' => '', 'body' => '');
	$check_headtag[$head_tag_place] = ' checked="checked"';


	$form = "
		<input type='hidden' name='options[0]' value='$mydirname' />
		<label for='blockwidth'>"._MB_XPWIKI_WIDTH."</label>&nbsp;:
		<input type='text' size='20' name='options[1]' id='blockwidth' value='".$width."' /> ( {$defs[1]} )
		<br />
		<label for='this_template'>"._MB_XPWIKI_THISTEMPLATE."</label>&nbsp;:
		<input type='text' size='40' name='options[2]' id='this_template' value='".htmlspecialchars($this_template,ENT_QUOTES)."' /> ( {$defs[2]} )
		<br />
		<label for='divclass'>"._MB_XPWIKI_DIVCLASS."</label>&nbsp;:
		<input type='text' size='30' name='options[3]' id='divclass' value='".htmlspecialchars($div_class,ENT_QUOTES)."' /> ( {$defs[3]} )
		<br />
		<label for='this_css'>"._MB_XPWIKI_THISCSS."</label>&nbsp;:
		<input type='text' size='30' name='options[4]' id='this_css' value='".htmlspecialchars($css,ENT_QUOTES)."' /> ( {$defs[4]} )
		<br />
		<label>"._MB_XPWIKI_HEAD_TAG_PLACE."</label>&nbsp;:
		<input type='radio' name='options[5]' value='module'{$check_headtag['module']} id='headtag_module' /><label for='headtag_module'>xoops_module_header</label> &nbsp; <input type='radio' name='options[5]' value='block'{$check_headtag['block']} id='headtag_block' /><label for='headtag_block'>xoops_block_header</label> &nbsp; <input type='radio' name='options[5]' value='body'{$check_headtag['body']} id='headtag_body' /><label for='headtag_body'>&lt;body&gt;(Inline)</label>
		<br />( {$defs[5]} )<br />
		\n" ;
	return $form;
}

function b_xpwiki_insert_headtag($heads, $head_tag_place)
{
	if( is_object( $GLOBALS['xoopsTpl'] ) ) {
		if ($head_tag_place === 'module' || $head_tag_place === 'block') {
			$xoops_header = $GLOBALS['xoopsTpl']->get_template_vars( 'xoops_'.$head_tag_place.'_header' );
			$head = '';
			foreach(explode("\n", $heads) as $_head) {
				$_head = trim($_head);
				if ($_head && strpos($xoops_header, $_head) === FALSE) {
					$head .= $_head . "\n";
				}
			}
			$GLOBALS['xoopsTpl']->assign( 'xoops_'.$head_tag_place.'_header' , $xoops_header . $head);
			return TRUE;
		}
	}
	return FALSE;
}

?>