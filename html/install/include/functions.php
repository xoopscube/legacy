<?php
/**
 * Installer default functions
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2008/08/28
 * @author
 * @copyright  (c) 2000-2003 XOOPS.org
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
		foreach ( explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) as $al ) {
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
	setcookie( 'install_lang', $language, ['expires' => ini_get( 'session.cookie_lifetime' ), 'path' => ini_get( 'session.cookie_path' ), 'domain' => ini_get( 'session.cookie_domain' ), 'secure' => ini_get( 'session.cookie_secure' ), 'httponly' => ini_get( 'session.cookie_httponly' )]
    );

	return $language;
}

/*
 * gets list of name of directories inside a directory
 */
function getDirList( $dirname ) {
	$dirlist = [];
	if ( is_dir( $dirname ) && $handle = opendir( $dirname ) ) {
		while ( false !== ( $file = readdir( $handle ) ) ) {
			if ( ! preg_match( '/^[.]{1,2}$/', $file ) && 'cvs' !== strtolower( $file ) && is_dir( $dirname . $file ) ) {
				$dirlist[ $file ] = $file;
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
	}

	$lines = file( $path );
	if ( ! $lines ) {
		$ret = false;

		return $ret;
	}
	$ret = '';
	foreach ( $lines as $i => $iValue ) {
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
		$content .= '<a href="javascript:void(0);" onclick=\'location.href="index.php?op='
            . htmlspecialchars( $option[0], ENT_QUOTES | ENT_HTML5 )
            . '"\' class="wizard-back" style="display:inline-block;vertical-align:top;">
            <svg xmlns="http://www.w3.org/2000/svg" title="' . _INSTALL_L42
            . '" aria-hidden="true" focusable="false" width="1em" height="1em" style="vertical-align: -0.125em;" viewBox="0 0 24 24">
            <path d="M17 13H8.75L12 16.25l-.664.75l-4.5-4.5l4.5-4.5l.664.75L8.75 12H17v1zm-15-.5a9.5 9.5 0 1 1 19 0a9.5 9.5 0 0 1-19 0zm1 0a8.5 8.5 0 1 0 17 0a8.5 8.5 0 0 0-17 0z" fill="currentColor"></path>
            </svg></a>';
	} else {
		$content .= '<a href="javascript:history.back();" class="wizard-back" style="display:inline-block;vertical-align:top;">
            <svg xmlns="http://www.w3.org/2000/svg" title="' . _INSTALL_L42
            . '" aria-hidden="true" focusable="false" width="1em" height="1em" style="vertical-align: -0.125em;" viewBox="0 0 24 24">
            <path d="M17 13H8.75L12 16.25l-.664.75l-4.5-4.5l4.5-4.5l.664.75L8.75 12H17v1zm-15-.5a9.5 9.5 0 1 1 19 0a9.5 9.5 0 0 1-19 0zm1 0a8.5 8.5 0 1 0 17 0a8.5 8.5 0 0 0-17 0z" fill="currentColor"></path>
            </svg></a>';
	}
	if ( isset( $option[1] ) && '' != $option[1] ) {
		$content .= '<label class="wizard-back-label">' . htmlspecialchars( $option[1], ENT_QUOTES | ENT_HTML5 ) . '</label>';
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

	return '<a href="javascript:void(0);" onclick=\'location.href="index.php?op='
        . htmlspecialchars( $op, ENT_QUOTES | ENT_HTML5 )
        . '"\' class="wizard-reload" style="display:inline-block;vertical-align:top;">
        <svg xmlns="http://www.w3.org/2000/svg" title="' . _INSTALL_L200 . '" aria-hidden="true" focusable="false" width="1em" height="1em" style="vertical-align: -0.125em;" viewBox="0 0 24 24">
        <path d="M4.996 5h5v5h-1V6.493a6.502 6.502 0 0 0 2.504 12.5a6.5 6.5 0 0 0 1.496-12.827V5.142A7.5 7.5 0 1 1 7.744 6H4.996V5z" fill="#face74"/>
        </svg></a>';
}

function b_next( $option = null ) {
	if ( ! isset( $option ) || ! is_array( $option ) ) {
		return '';
	}
	$content = '';
	if ( isset( $option[1] ) && '' !== $option[1] ) {
		$content .= '<label class="wizard-next-label">' . htmlspecialchars( $option[1], ENT_QUOTES | ENT_HTML5 ) . '</label>';
	}
	$content .= '<input type="hidden" name="op" value="' . htmlspecialchars( $option[0], ENT_QUOTES | ENT_HTML5 ) . '">';
	$content .= '<button type="submit" class="wizard-next" title="' . _INSTALL_L47
        . '" name="submit" value="' . _INSTALL_L47 . '">
        <svg xmlns="http://www.w3.org/2000/svg" title="' . _INSTALL_L47
        . '" aria-hidden="true" focusable="false" width="1em" height="1em" style="vertical-align: -0.125em;" viewBox="0 0 24 24">
        <path d="M6.003 12h8.25l-3.25-3.25l.664-.75l4.5 4.5l-4.5 4.5l-.664-.75l3.25-3.25h-8.25v-1zm15 .5a9.5 9.5 0 1 1-19 0a9.5 9.5 0 0 1 19 0zm-1 0a8.5 8.5 0 1 0-17 0a8.5 8.5 0 0 0 17 0z" fill="currentColor"></path>
        </svg></button>';

	return $content;
}
