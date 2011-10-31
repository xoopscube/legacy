<?php
//
// Created on 2006/10/29 by nao-pon http://hypweb.net/
// $Id: whatsnew.inc.php,v 1.4 2007/07/11 06:18:08 nao-pon Exp $
//

// DIRNAME_new() 関数を動的に生成
eval( '

function '.$mydirname.'_new( $limit=0, $offset=0 )
{
	return xpwiki_whatsnew_base( "'.$mydirname.'" , $limit, $offset ) ;
}

' ) ;


if (! function_exists('xpwiki_whatsnew_base')) {
	// DIRNAME_new() 関数の実体
	function xpwiki_whatsnew_base( $mydirname, $limit, $offset ) {
	
		// 必要なファイルの読み込み
		$mytrustdirpath = dirname(dirname( __FILE__ )) ;
		include_once "$mytrustdirpath/include.php";
		
		// XpWiki オブジェクト作成
		$xpwiki = new XpWiki($mydirname);
		
		// whatsnew extension 読み込み
		$xpwiki->load_extensions("whatsnew");
		
		// 初期化
		$xpwiki->init('#RenderMode');
		
		// whatsnew データ取得
		$ret = $xpwiki->extension->whatsnew->get ($limit, $offset);
		
		// オブジェクト破棄
		$xpwiki = null;
		
		return $ret;
	}
}
?>