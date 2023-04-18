<?php

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) {
	$mydirname = 'xelfinder';
}

//$constpref = '_MD_' . strtoupper( $mydirname );
$constpref = '_MD';

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref . '_LOADED_MAIN' ) ) {

// a flag for this language file has already been read or not.
	define( $constpref . '_LOADED_MAIN', 1 );

	define( $constpref . '_FINDER_TITLE', $mydirname.' - Файловый менеджер');
	define( $constpref . '_FINDER_DESC', 'Позволяет участникам загружать документы и изображения и управлять ими.' );
	define( $constpref . '_OPEN_MANAGER', 'Откройте файловый менеджер' );
	define( $constpref . '_OPEN_WINDOW', 'Всплывающие окна' );
	define( $constpref . '_OPEN_FULL', 'Новое окно' );
	define( $constpref . '_OPEN_WINDOW_ADMIN', 'Всплывающее окно (режим администратора)' );
	define( $constpref . '_OPEN_FULL_ADMIN', 'Новое окно (режим администратора)' );
	define( $constpref . '_ADMIN_PANEL', 'Перейдите в панель управления' );

}
