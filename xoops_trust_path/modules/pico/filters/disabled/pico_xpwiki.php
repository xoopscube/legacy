<?php

define('_MD_PICO_FILTERS_XPWIKIINITWEIGHT',15);

function pico_xpwiki( $mydirname , $text , $content4assign )
{
	@ include_once XOOPS_TRUST_PATH."/modules/xpwiki/include.php";

	if( ! class_exists( 'XpWiki' ) ) die( 'xpWiki is not installed correctly' ) ;

	// 引数は、xpWikiをインストールしたディレクトリ名です。
	$wiki = new XpWiki('xpwiki'); // create instance. option is xpWiki module's directory name.

	// 第二引数は、xpWikiのCSSを適用するためのDIVクラス名です。
	return $wiki->transform($text, 'pico_body');
}

?>