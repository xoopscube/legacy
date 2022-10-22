<?php

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) {
	$mydirname = 'xelfinder';
}

//$constpref = '_MD_' . strtoupper( $mydirname );
$constpref = '_MD';

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref . '_LOADED_MAIN' ) ) {

// a flag for this language file has already been read or not.
	define( $constpref . '_LOADED_MAIN', 1 );

	define( $constpref . '_FINDER_TITLE', $mydirname.' - File Manager');
	define( $constpref . '_FINDER_DESC', 'Allow members to upload and manage documents and images. The files are stored within the public upload folder.' );
	define( $constpref . '_OPEN_MANAGER', 'ファイルマネージャを開く' );
	define( $constpref . '_OPEN_WINDOW', 'ポップアップ' );
	define( $constpref . '_OPEN_FULL', 'フルウィンドウ' );
	define( $constpref . '_OPEN_WINDOW_ADMIN', 'ポップアップ(管理モード)' );
	define( $constpref . '_OPEN_FULL_ADMIN', 'フルウィンドウ(管理モード)' );
	define( $constpref . '_ADMIN_PANEL', '管理画面を開く' );

}
