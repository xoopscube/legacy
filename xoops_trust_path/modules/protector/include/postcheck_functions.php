<?php
/**
 * Protector module for XCL
 *
 * @package    Protector
 * @version    XCL 2.3.3
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

function protector_postcommon() {
	global $xoopsUser, $xoopsModule;

	// configs writable check
	if ( '/admin.php' == @$_SERVER['REQUEST_URI'] && ! is_writable( dirname( __DIR__ ) . '/configs' ) ) {
		trigger_error( 'You should turn the directory ' . dirname( __DIR__ ) . '/configs writable', E_USER_WARNING );
	}

	// Protector object
	require_once dirname( __DIR__ ) . '/class/protector.php';
	$db        = Database::getInstance();
	$protector = Protector::getInstance();
	$protector->setConn( $db->conn );
	$protector->updateConfFromDb();
	$conf = $protector->getConf();
	if ( empty( $conf ) ) {
		return true;
	} // not installed yet

	// phpmailer vulnerability
	// https://larholm.com/2007/06/11/phpmailer-0day-remote-execution/
	if ( in_array( substr( XOOPS_VERSION, 0, 12 ), [ 'XOOPS 2.0.16', 'XOOPS 2.0.13', 'XOOPS 2.2.4' ] ) ) {
		$config_handler    = &xoops_gethandler( 'config' );
		$xoopsMailerConfig = &$config_handler->getConfigsByCat( XOOPS_CONF_MAILER );
		if ( 'sendmail' == $xoopsMailerConfig['mailmethod'] && 'ee1c09a8e579631f0511972f929fe36a' == md5_file( XOOPS_ROOT_PATH . '/class/mail/phpmailer/class.phpmailer.php' ) ) {
			echo '<strong>phpmailer security hole! Change the preferences of mail from "sendmail" to another, or upgrade the core right now! (message by protector)</strong>';
		}
	}

	// global enabled or disabled
	if ( ! empty( $conf['global_disabled'] ) ) {
		return true;
	}

	$last_ip                       = $_SESSION['protector_last_ip'] ?? '';
	$_SESSION['protector_last_ip'] = $protector->remote_ip;

	// group1_ips (groupid=1)
	if ( is_object( $xoopsUser ) && in_array( 1, $xoopsUser->getGroups() ) ) {
		$group1_ips = $protector->get_group1_ips( true );
		if ( implode( '', array_keys( $group1_ips ) ) ) {
			$group1_allow = $protector->ip_match( $group1_ips );
			if ( empty( $group1_allow ) ) {
				die( 'This account is disabled for your IP by Protector.<br>Clear cookie if you want to access this site as a guest.' );
			}
		}
	}

	// reliable ips
	$reliable_ips = @unserialize( @$conf['reliable_ips'] );
	if ( is_array( $reliable_ips ) ) {
		foreach ( $reliable_ips as $reliable_ip ) {
			if ( ! empty( $reliable_ip ) && preg_match( '/' . $reliable_ip . '/', $_SERVER['REMOTE_ADDR'] ) ) {
				return true;
			}
		}
	}

	// user information (uid and can be banned)
	if ( is_object( @$xoopsUser ) ) {
		$uid     = $xoopsUser->getVar( 'uid' );
		$can_ban = count( @array_intersect( $xoopsUser->getGroups(), @unserialize( @$conf['bip_except'] ) ) ) ? false : true;
	} else {
		// login failed check
		if ( ( ! empty( $_POST['uname'] ) && ! empty( $_POST['pass'] ) ) || ( ! empty( $_COOKIE['autologin_uname'] ) && ! empty( $_COOKIE['autologin_pass'] ) ) ) {
			$protector->check_brute_force();
		}
		$uid     = 0;
		$can_ban = true;
	}

	// If precheck has already judged that he should be banned
	if ( $can_ban && $protector->_should_be_banned ) {
		$protector->register_bad_ips();
	} elseif ( $can_ban && $protector->_should_be_banned_time0 ) {
		$protector->register_bad_ips( time() + $protector->_conf['banip_time0'] );
	}

	// DOS/CRAWLER skipping based on 'dirname' or getcwd()
	$dos_skipping  = false;
	$skip_dirnames = explode( '|', @$conf['dos_skipmodules'] );
	if ( ! is_array( $skip_dirnames ) ) {
		$skip_dirnames = [];
	}
	if ( is_object( @$xoopsModule ) ) {
		if ( in_array( $xoopsModule->getVar( 'dirname' ), $skip_dirnames ) ) {
			$dos_skipping = true;
		}
	} else {
		foreach ( $skip_dirnames as $skip_dirname ) {
			if ( $skip_dirname && strstr( getcwd(), $skip_dirname ) ) {
				$dos_skipping = true;
				break;
			}
		}
	}

	// module can controll DoS skipping
	if ( defined( 'PROTECTOR_SKIP_DOS_CHECK' ) ) {
		$dos_skipping = true;
	}

	// DoS Attack
	if ( empty( $dos_skipping ) && ! $protector->check_dos_attack( $uid, $can_ban ) ) {
		$protector->output_log( $protector->last_error_type, $uid, true, 16 );
	}

	// check session hi-jacking
	if ( $last_ip && is_object( $xoopsUser ) ) {
		$denyipmove = @unserialize( $conf['groups_denyipmove'] );
		if ( $denyipmove ) {
			$purge = false;
			if ( $protector->is_ipv6 ) {
				if ( false !== strpos( $last_ip, ':' ) ) {
					$protector_last_numip = str_replace( ':', '', $last_ip );
					$protector_last_numip = substr( $protector_last_numip, 0, @$conf['session_fixed_topbitv6'] / 4 );
					$remote_numip         = str_replace( ':', '', $protector->remote_ip );
					$remote_numip         = substr( $remote_numip, 0, @$conf['session_fixed_topbitv6'] / 4 );
					if ( $protector_last_numip !== $remote_numip ) {
						$purge = true;
					}
				} else {
					$purge = true;
				}
			} else {
				if ( false !== strpos( $last_ip, '.' ) ) {
					$ips                  = explode( '.', $last_ip );
					$protector_last_numip = @$ips[0] * 0x1000000 + @$ips[1] * 0x10000 + @$ips[2] * 0x100 + @$ips[3];
					$ips                  = explode( '.', $protector->remote_ip );
					$remote_numip         = @$ips[0] * 0x1000000 + @$ips[1] * 0x10000 + @$ips[2] * 0x100 + @$ips[3];
					$shift                = 32 - @$conf['session_fixed_topbit'];
					if ( $shift < 32 && $shift >= 0 && $protector_last_numip >> $shift != $remote_numip >> $shift ) {
						$purge = true;
					}
				} else {
					$purge = true;
				}
			}
			if ( $purge && count( array_intersect( $xoopsUser->getGroups(), $denyipmove ) ) ) {
				$protector->purge( true );
			}
		}
	}

	// SQL Injection "Isolated /*"
	if ( ! $protector->check_sql_isolatedcommentin( @$conf['isocom_action'] & 1 ) ) {
		if ( ( $conf['isocom_action'] & 8 ) && $can_ban ) {
			$protector->register_bad_ips();
		} elseif ( ( $conf['isocom_action'] & 4 ) && $can_ban ) {
			$protector->register_bad_ips( time() + $protector->_conf['banip_time0'] );
		}
		$protector->output_log( 'ISOCOM', $uid, true, 32 );
		if ( $conf['isocom_action'] & 2 ) {
			$protector->purge();
		}
	}

	// SQL Injection "UNION"
	if ( ! $protector->check_sql_union( @$conf['union_action'] & 1 ) ) {
		if ( ( $conf['union_action'] & 8 ) && $can_ban ) {
			$protector->register_bad_ips();
		} elseif ( ( $conf['union_action'] & 4 ) && $can_ban ) {
			$protector->register_bad_ips( time() + $protector->_conf['banip_time0'] );
		}
		$protector->output_log( 'UNION', $uid, true, 32 );
		if ( $conf['union_action'] & 2 ) {
			$protector->purge();
		}
	}

	if ( ! empty( $_POST ) ) {
		// SPAM Check
		if ( is_object( $xoopsUser ) ) {
			if ( ! $xoopsUser->isAdmin() && $conf['spamcount_uri4user'] ) {
				$protector->spam_check( (int) $conf['spamcount_uri4user'], $xoopsUser->getVar( 'uid' ) );
			}
		} elseif ( $conf['spamcount_uri4guest'] ) {
			$protector->spam_check( (int) $conf['spamcount_uri4guest'], 0 );
		}

		// filter plugins for POST on postcommon stage
		$protector->call_filter( 'postcommon_post' );
	}

	// register.php Protection
	if ( $_SERVER['SCRIPT_FILENAME'] == XOOPS_ROOT_PATH . '/register.php' ) {
		$protector->call_filter( 'postcommon_register' );
	}

	// Simple check for manupilations by FTP worm etc.
	if ( ! empty( $conf['enable_manip_check'] ) ) {
		$protector->check_manipulation();
	}
}
