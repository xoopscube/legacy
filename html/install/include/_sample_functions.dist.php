<?php
/**
 * Installer functions for XCL Distribution
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */



function getLanguage() {
	$language_array = [
		'en' => 'english',
//			'cn' => 'schinese',
		'cs' => 'czech',
//			'de' => 'german',
		'el' => 'greek',
//			'es' => 'spanish',
		'fr' => 'french',
		'ja' => 'japanese',
		'ko' => 'korean',
//			'nl' => 'dutch',
		'pt' => 'pt_utf8',
		'ru' => 'russian',
		'zh' => 'schinese',

	];

	$charset_array = [
		'Shift_JIS' => 'ja_utf8',
	];

	$language = 'english';
	if ( ! empty( $_POST['lang'] ) ) {
		$language = $_POST['lang'];
	} else if ( isset( $_COOKIE['install_lang'] ) ) {
		$language = $_COOKIE['install_lang'];
	} else if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
		$accept_langs = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
		foreach ( $accept_langs as $al ) {
			$al     = strtolower( $al );
			$al_len = strlen( $al );
			if ( $al_len > 2 ) {
				if ( preg_match( '/([a-z]{2});q=[0-9.]+$/', $al, $al_match ) ) {
					$al = $al_match[1];
				} else {
					continue;
				}
			}
			if ( isset( $language_array[ $al ] ) ) {
				$language = $language_array[ $al ];
				break;
			}
		}
		if ( file_exists( dirname( __DIR__ ) . '/language/' . $al . '_utf8' ) ) {
			$language = $al . '_utf8';
		}
	} elseif ( isset( $_SERVER['HTTP_ACCEPT_CHARSET'] ) ) {
		foreach ( $charset_array as $ac => $lg ) {
			if ( strpos( $_SERVER['HTTP_ACCEPT_CHARSET'], $ac ) !== false ) {
				$language = $lg;
				break;
			}
		}
	}
	if ( ! file_exists( './language/' . $language . '/install.php' ) ) {
		$language = 'english';
	}
	setcookie( 'install_lang', $language, ['expires' => ini_get( 'session.cookie_lifetime' ), 'path' => ini_get( 'session.cookie_path' ), 'domain' => ini_get( 'session.cookie_domain' ), 'secure' => ini_get( 'session.cookie_secure' ), 'httponly' => ini_get( 'session.cookie_httponly' )] );

	return $language;
}

/*
 * gets list of name of directories inside a directory
 */
function getDirList( $dirname ) {
	$dirlist = [];
	if ( is_dir( $dirname ) && $handle = opendir( $dirname ) ) {
		while ( false !== ( $file = readdir( $handle ) ) ) {
			if ( ! preg_match( '/^[.]{1,2}$/', $file ) ) {
				if ( 'cvs' != strtolower( $file ) && is_dir( $dirname . $file ) ) {
					$dirlist[ $file ] = $file;
				}
			}
		}
		closedir( $handle );
		asort( $dirlist );
		reset( $dirlist );
	}

	return $dirlist;
}

/*
 * gets list of name of files within a directory
 */
function getImageFileList( $dirname ) {
	$filelist = [];
	if ( is_dir( $dirname ) && $handle = opendir( $dirname ) ) {
		while ( false !== ( $file = readdir( $handle ) ) ) {
			if ( ! preg_match( '/^[.]{1,2}$/', $file ) && preg_match( '/[.gif|.jpg|.png]$/i', $file ) ) {
				$filelist[ $file ] = $file;
			}
		}
		closedir( $handle );
		asort( $filelist );
		reset( $filelist );
	}

	return $filelist;
}

function &xoops_module_gettemplate( $dirname, $template, $block = false ) {
	if ( $block ) {
		$path = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/templates/blocks/' . $template;
	} else {
		$path = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/templates/' . $template;
	}
	if ( ! file_exists( $path ) ) {
		$ret = false;

		return $ret;
	} else {
		$lines = file( $path );
	}
	if ( ! $lines ) {
		$ret = false;

		return $ret;
	}
	$ret   = '';
	$count = count( $lines );
	for ( $i = 0; $i < $count; $i ++ ) {
		$ret .= str_replace( "\n", "\r\n", str_replace( "\r\n", "\n", $lines[ $i ] ) );
	}

	return $ret;
}

function check_language( $language ) {
	if ( file_exists( './language/' . $language . '/install.php' ) ) {
		return $language;
	}

	return 'english';
}

function b_back( $option = null ) {
	if ( ! isset( $option ) || ! is_array( $option ) ) {
		return '';
	}
	$content = '';
	if ( isset( $option[0] ) && '' !== $option[0] ) {
		$content .= '<a href="javascript:void(0);" onclick=\'location.href="index.php?op=' . htmlspecialchars( $option[0] ) . '"\' class="back" style="display:inline-block;vertical-align:top;"><img src="img/back.png" alt="' . _INSTALL_L42 . '"></a>';
	} else {
		$content .= '<a href="javascript:history.back();" class="back" style="display:inline-block;vertical-align:top;"><img src="img/back.png" alt="' . _INSTALL_L42 . '" /></a>';
	}
	if ( isset( $option[1] ) && '' !== $option[1] ) {
		$content .= '<span style="font-size:90%;"> &lt;&lt; ' . htmlspecialchars( $option[1] ) . '</span>';
	}

	return $content;
}

function b_reload( $option = '' ) {
	if ( empty( $option ) ) {
		return '';
	}
	if ( ! defined( '_INSTALL_L200' ) ) {
		define( '_INSTALL_L200', 'Reload' );
	}

	if ( ! empty( $_POST['op'] ) ) {
		$op = $_POST['op'];
	} elseif ( ! empty( $_GET['op'] ) ) {
		$op = $_GET['op'];
	} else {
		$op = 'langselect';
	}

	return '<input type="image" src="img/reload.png" class="reload" title="Reload" value="' . _INSTALL_L200 . '" onclick="location.reload();" />';
}

function b_next( $option = null ) {
	if ( ! isset( $option ) || ! is_array( $option ) ) {
		return '';
	}
	$content = '';
	if ( isset( $option[1] ) && '' !== $option[1] ) {
		$content .= '<span style="font-size:90%;">' . htmlspecialchars( $option[1], ENT_QUOTES | ENT_HTML5 ) . ' &gt;&gt; </span>';
	}
	$content .= '<input type="hidden" name="op" value="' . htmlspecialchars( $option[0], ENT_QUOTES | ENT_HTML5 ) . '" />';
	$content .= '<input type="image" src="img/next.png" class="next" title="' . _INSTALL_L47 . '" name="submit" value="' . _INSTALL_L47 . '" />';

	return $content;
}
