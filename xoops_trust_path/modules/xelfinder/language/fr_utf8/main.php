<?php

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) ) {
	$mydirname = 'xelfinder';
}

//$constpref = '_MD_' . strtoupper( $mydirname );
$constpref = '_MD';

if ( defined( 'FOR_XOOPS_LANG_CHECKER' ) || ! defined( $constpref . '_LOADED_MAIN' ) ) {

// a flag for this language file has already been read or not.
	define( $constpref . '_LOADED_MAIN', 1 );

	define( $constpref . '_FINDER_TITLE', $mydirname.' - Gestionnaire de fichiers');
	define( $constpref . '_FINDER_DESC', 'Permet aux membres de télécharger et de gérer des documents et des images.' );
	define( $constpref . '_OPEN_MANAGER', 'Ouvrir le gestionnaire de fichiers' );
	define( $constpref . '_OPEN_WINDOW', 'Fenêtre contextuelle' );
	define( $constpref . '_OPEN_FULL', 'Nouvelle fenêtre' );
	define( $constpref . '_OPEN_WINDOW_ADMIN', 'Fenêtre contextuelle (mode administrateur)' );
	define( $constpref . '_OPEN_FULL_ADMIN', 'Nouvelle fenêtre (mode administrateur)' );
	define( $constpref . '_ADMIN_PANEL', 'Aller au panneau de contrôle' );

}
