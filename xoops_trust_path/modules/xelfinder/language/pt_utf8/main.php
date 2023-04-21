<?php

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) {
	$mydirname = 'xelfinder';
}

//$constpref = '_MD_' . strtoupper( $mydirname );
$constpref = '_MD';

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref . '_LOADED_MAIN' ) ) {

// a flag for this language file has already been read or not.
	define( $constpref . '_LOADED_MAIN', 1 );

	define( $constpref . '_FINDER_TITLE', $mydirname.' - Gerenciador de arquivos');
	define( $constpref . '_FINDER_DESC', 'Permite que os membros carreguem e gerenciem documentos e imagens.' );
	define( $constpref . '_OPEN_MANAGER', 'Abrir o gerenciador de arquivos' );
	define( $constpref . '_OPEN_WINDOW', 'Janela pop-up' );
	define( $constpref . '_OPEN_FULL', 'Novas janela' );
	define( $constpref . '_OPEN_WINDOW_ADMIN', 'Janela pop-up (modo Admin)' );
	define( $constpref . '_OPEN_FULL_ADMIN', 'Nova janela (modo Admin)' );
	define( $constpref . '_ADMIN_PANEL', 'Acesso ao painel de controle' );

}
