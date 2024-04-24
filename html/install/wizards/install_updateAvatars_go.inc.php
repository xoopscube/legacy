<?php
/**
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.4.0
 * @author     kilica, 2008/09/25
 * @copyright  (c) 2005-2024 The XOOPSCube Project
 * @license    GPL 2.0
 */

unset( $xoopsOption['nocommon'] );

include( '../mainfile.php' );
echo '<h2>wizard/install_updateAvatars_go.inc</h2>';

$content = '';
$title   = _INSTALL_L156;

$avatars = getImageFileList( XOOPS_ROOT_PATH . '/images/avatar/users/' );

$xoopsDB->query( 'UPDATE ' . $xoopsDB->prefix( 'users' ) . " SET user_avatar='blank.png'" ); // @TODO avatar image

$avt_handler =& xoops_gethandler( 'avatar' );
if ( ! defined( 'XOOPS_UPLOAD_PATH' ) ) {
	define( 'XOOPS_UPLOAD_PATH', '../uploads' );
}
foreach ( $avatars as $avatar_file ) {
	if ( preg_match( '/^([0-9]+)\\.([a-zA-Z]+)$/',
		$avatar_file,
		$matched ) ) {
		$user_id = (int) $matched[1];
		if ( ( $user_id > 0 ) && ( false !== $fp = fopen( '../images/avatar/users/' . $avatar_file, 'rb' ) ) ) {
			$binary = fread( $fp, filesize( '../images/avatar/users/' . $avatar_file ) );
			fclose( $fp );
			$newavatar = uniqid( 'cavt', true ) . '.' . strtolower( $matched[2] );
			if ( false !== $fp = fopen( XOOPS_UPLOAD_PATH . '/' . $newavatar, 'wb' ) ) {
				if ( - 1 !== fwrite( $fp, $binary ) ) {
					$error = false;
					if ( ! $xoopsDB->query( 'UPDATE ' . $xoopsDB->prefix( 'users' ) . " SET user_avatar='" . $newavatar . "' WHERE uid=" . $user_id ) ) {
						$error = true;
					} else {
						$avatar =& $avt_handler->create();
						$avatar->setVar( 'avatar_file', $newavatar );
						$avatar->setVar( 'avatar_name', 'custom' );
						$avatar->setVar( 'avatar_mimetype', '' );
						$avatar->setVar( 'avatar_display', 1 );
						$avatar->setVar( 'avatar_type', 'C' );
						if ( ! $avt_handler->insert( $avatar ) ) {
							$error = true;
						} else {
							$avt_handler->addUser( $avatar->getVar( 'avatar_id' ), $user['uid'] );
						}
					}
					if ( false !== $error ) {
						$content .= _NGIMG . sprintf( _INSTALL_L153, $avatar_file ) . '<br>';
						@unlink( XOOPS_UPLOAD_PATH . '/' . $newavatar );
					} else {
						$content .= _OKIMG . sprintf( _INSTALL_L154, $avatar_file ) . '<br>';
					}
				} else {
					$content .= _NGIMG . sprintf( _INSTALL_L153, $avatar_file ) . '<br>';
					$xoopsDB->query( 'UPDATE ' . $xoopsDB->prefix( 'users' ) . " SET user_avatar='blank.png' WHERE uid=" . $user_id );
				}
				fclose( $fp );
			}
		} else {
			$content .= _NGIMG . sprintf( _INSTALL_L152, $avatar_file ) . '<br>';
		}
	}
}

$b_next = [ 'finish', _INSTALL_L14 ];

include './install_tpl.php';
