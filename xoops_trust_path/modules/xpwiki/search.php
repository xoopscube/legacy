<?php

eval( '

function '. $mydirname .'_global_search( $keywords , $andor , $limit , $offset , $userid )
{
	// for XOOPS Search module
	static $readed = array();
	$md5 = md5($keywords . $andor . $limit . $offset . $userid);
	if(isset($readed[$md5])) { return array() ; }
	$readed[$md5] = TRUE;
	return xpwiki_global_search_base( "'.$mydirname.'" , $keywords , $andor , $limit , $offset , $userid ) ;
}

' ) ;


if( ! function_exists( 'xpwiki_global_search_base' ) ) {

function xpwiki_global_search_base( $mydirname , $keywords , $andor , $limit , $offset , $userid )
{
	// 必要なファイルの読み込み
	$mytrustdirpath = dirname( __FILE__ ) ;
	include_once "$mytrustdirpath/include.php";
	
	// XpWiki オブジェクト作成
	$xpwiki = new XpWiki($mydirname);
	
	// xoopsSearch extension 読み込み
	$xpwiki->load_extensions("xoopsSearch");
	
	// 初期化
	$xpwiki->init('#RenderMode');
	
	// データ取得
	$ret = $xpwiki->extension->xoopsSearch->get ( $keywords , $andor , $limit , $offset , $userid );
	
	// オブジェクト破棄
	$xpwiki = null;
	
	return $ret;
}

}

?>