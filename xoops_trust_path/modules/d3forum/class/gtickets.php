<?php
/**
 * D3Forum module for XCL
 * GIJOE's Ticket Class (based on Marijuana's Oreteki XOOPS) Nobunobu's suggestions are applied
 * @package    D3Forum
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

if ( ! class_exists( 'XoopsGTicket' ) ) {
	class XoopsGTicket {

		public $_errors = [];

		public $_latest_token = '';

		public $messages = [];

		public function __construct() {
			global $xoopsConfig;

			// language file
			if ( defined( 'XOOPS_ROOT_PATH' ) && ! empty( $xoopsConfig['language'] ) && ! strstr( $xoopsConfig['language'], '/' ) ) {

				if ( file_exists( dirname( __DIR__ ) . '/language/' . $xoopsConfig['language'] . '/gticket_messages.phtml' ) ) {
					include dirname( __DIR__ ) . '/language/' . $xoopsConfig['language'] . '/gticket_messages.phtml';
				}
			}

			// default messages
			if ( empty( $this->messages ) ) {
				$this->messages = [
					'err_general'       => 'GTicket Error',
					'err_nostubs'       => 'No stubs found',
					'err_noticket'      => 'No ticket found',
					'err_nopair'        => 'No valid ticket-stub pair found',
					'err_timeout'       => 'Time out',
					'err_areaorref'     => 'Invalid area or referer',
//					'fmt_prompt4repost' => 'error(s) found:<br><span style="background-color:red;font-weight:bold;color:white;">%s</span><br>Confirm it.<br>And do you want to post again?',
                    'fmt_prompt4repost' => '<div class="error">Error(s) found : <span style="background-color:red;font-weight:bold;color:white;">%s</span><br>Confirm Form Resubmission.</div>',

					'btn_repost'        => 'repost',
				];
			}
		}

		// render form as plain html
		public function getTicketHtml( $salt = '', $timeout = 1800, $area = '' ) {
			return '<input type="hidden" name="XOOPS_G_TICKET" value="' . $this->issue( $salt, $timeout, $area ) . '">';
		}

		// returns an object of XoopsFormHidden including theh ticket
		public function getTicketXoopsForm( $salt = '', $timeout = 1800, $area = '' ) {
			return new XoopsFormHidden( 'XOOPS_G_TICKET', $this->issue( $salt, $timeout, $area ) );
		}

		// add a ticket as Hidden Element into XoopsForm
		public function addTicketXoopsFormElement( &$form, $salt = '', $timeout = 1800, $area = '' ) {
			$form->addElement( new XoopsFormHidden( 'XOOPS_G_TICKET', $this->issue( $salt, $timeout, $area ) ) );
		}

		// returns an array for xoops_confirm() ;
		public function getTicketArray( $salt = '', $timeout = 1800, $area = '' ) {
			return [ 'XOOPS_G_TICKET' => $this->issue( $salt, $timeout, $area ) ];
		}

		// return GET parameter string.
		public function getTicketParamString( $salt = '', $noamp = false, $timeout = 1800, $area = '' ) {
			return ( $noamp ? '' : '&amp;' ) . 'XOOPS_G_TICKET=' . $this->issue( $salt, $timeout, $area );
		}

		// issue a ticket
		public function issue( $salt = '', $timeout = 1800, $area = '' ) {
			global $xoopsModule;

			// create a token
			[ $usec, $sec ] = explode( ' ', microtime() );

			$appendix_salt = empty( $_SERVER['PATH'] ) ? XOOPS_DB_NAME : $_SERVER['PATH'];

			$token = crypt( $salt . $usec . $appendix_salt . $sec, XOOPS_DB_PREFIX );

			$this->_latest_token = $token;

			if ( empty( $_SESSION['XOOPS_G_STUBS'] ) ) {
				$_SESSION['XOOPS_G_STUBS'] = [];
			}

			// limit max stubs 10
			if ( count( $_SESSION['XOOPS_G_STUBS'] ) > 10 ) {
				$_SESSION['XOOPS_G_STUBS'] = array_slice( $_SESSION['XOOPS_G_STUBS'], - 10 );
			}

			// record referer if browser send it
			$referer = empty( $_SERVER['HTTP_REFERER'] ) ? '' : $_SERVER['REQUEST_URI'];

			// area as module's dirname
			if ( ! $area && is_object( @$xoopsModule ) ) {
				$area = $xoopsModule->getVar( 'dirname' );
			}

			// store stub
			$_SESSION['XOOPS_G_STUBS'][] = [
				'expire'  => time() + $timeout,
				'referer' => $referer,
				'area'    => $area,
				'token'   => $token
			];

			// paid md5ed token as a ticket
			return md5( $token . XOOPS_DB_PREFIX );
		}

		// check a ticket
		public function check( $post = true, $area = '', $allow_repost = true ) {
			global $xoopsModule;

			$this->_errors = [];

			// CHECK: stubs are not stored in session
			if ( ! is_array( @$_SESSION['XOOPS_G_STUBS'] ) ) {
				$this->_errors[]           = $this->messages['err_nostubs'];
				$_SESSION['XOOPS_G_STUBS'] = [];
			}

			// get key&val of the ticket from a user's query
			$ticket = $post ? @$_POST['XOOPS_G_TICKET'] : @$_GET['XOOPS_G_TICKET'];

			// CHECK: no tickets found
			if ( empty( $ticket ) ) {
				$this->_errors[] = $this->messages['err_noticket'];
			}

			// gargage collection & find a right stub
			$stubs_tmp = $_SESSION['XOOPS_G_STUBS'];

			$_SESSION['XOOPS_G_STUBS'] = [];

			foreach ( $stubs_tmp as $stub ) {
				// default lifetime 30min
				if ( $stub['expire'] >= time() ) {
					if ( md5( $stub['token'] . XOOPS_DB_PREFIX ) === $ticket ) {
						$found_stub = $stub;
					} else {
						// store the other valid stubs into session
						$_SESSION['XOOPS_G_STUBS'][] = $stub;
					}
				} else {
					if ( md5( $stub['token'] . XOOPS_DB_PREFIX ) === $ticket ) {
						// not CSRF but Time-Out
						$timeout_flag = true;
					}
				}
			}

			// CHECK: the right stub found or not
			if ( empty( $found_stub ) ) {
				if ( empty( $timeout_flag ) ) {
					$this->_errors[] = $this->messages['err_nopair'];
				} else {
					$this->_errors[] = $this->messages['err_timeout'];
				}
			} else {
				// set area if necessary
				// area as module's dirname
				if ( ! $area && is_object( @$xoopsModule ) ) {
					$area = $xoopsModule->getVar( 'dirname' );
				}

				// check area or referer
				if ( @$found_stub['area'] == $area ) {
					$area_check = true;
				}
				if ( ! empty( $found_stub['referer'] ) && strstr( @$_SERVER['HTTP_REFERER'], $found_stub['referer'] ) ) {
					$referer_check = true;
				}

				if ( empty( $area_check ) && empty( $referer_check ) ) { // loose
					$this->_errors[] = $this->messages['err_areaorref'];
				}
			}

			if ( ! empty( $this->_errors ) ) {
				if ( $allow_repost ) {
					// repost form
					$this->draw_repost_form( $area );
					exit;
				} else {
					// failed
					$this->clear();

					return false;
				}
			} else {
				// all green
				return true;
			}
		}

		// draw form for repost
		public function draw_repost_form( $area = '' ) {
			// Notify which file is broken
			if ( headers_sent() ) {
				restore_error_handler();
				set_error_handler( [ &$this, 'errorHandler4FindOutput' ] );
				header( 'Dummy: for warning' );
				restore_error_handler();
				exit;
			}

			error_reporting( 0 );
			while ( ob_get_level() ) {
				ob_end_clean();
			}

			$table = '<table>';

			$form = '<form action="?' . htmlspecialchars( @$_SERVER['QUERY_STRING'], ENT_QUOTES ) . '" method="post" >';

			foreach ( $_POST as $key => $val ) {
				if ( 'XOOPS_G_TICKET' == $key ) {
					continue;
				}
				if ( is_array( $val ) ) {
					list( $tmp_table, $tmp_form ) = $this->extract_post_recursive( htmlspecialchars( $key, ENT_QUOTES ), $val );
					$table .= $tmp_table;
					$form  .= $tmp_form;
				} else {
					$table .= '<tr><th>' . htmlspecialchars( $key, ENT_QUOTES ) . '</th><td>' . htmlspecialchars( $val, ENT_QUOTES ) . '</td></tr>' . "\n";
					$form  .= '<input type="hidden" name="' . htmlspecialchars( $key, ENT_QUOTES ) . '" value="' . htmlspecialchars( $val, ENT_QUOTES ) . '">' . "\n";
				}
			}
			$table .= '</table>';

			$form  .= $this->getTicketHtml( __LINE__, 300, $area ) . '<input type="submit" value="' . $this->messages['btn_repost'] . '"></form>';

//			echo '<html><head><title>' . $this->messages['err_general'] . '</title><style>table,td,th {border:solid black 1px; border-collapse:collapse;}</style></head><body>' . sprintf( $this->messages['fmt_prompt4repost'], $this->getErrors() ) . $table . $form . '</body></html>';
            echo '<!doctype html><html class="no-js" lang="en"><head><meta charset="utf-8"><title>' . $this->messages['err_general'] . '</title><link rel="stylesheet" href="'.XOOPS_URL.'/common/css/x-layout.css"><link rel="stylesheet" href="'.XOOPS_URL.'/modules/legacy/admin/theme/style.css"></script><style>body{background:hsl(219, 15%, 16%);color:#abc;margin:2em auto;max-width:80vw;}table,td,th {border:solid black 1px; border-collapse:collapse;}</style></head><body>' . sprintf( $this->messages['fmt_prompt4repost'], $this->getErrors() ) . $table . $form . '</body></html>';

        }

		public function extract_post_recursive( $key_name, $tmp_array ) {
			$table = '';

			$form = '';

			foreach ( $tmp_array as $key => $val ) {
				if ( is_array( $val ) ) {
					list( $tmp_table, $tmp_form ) = $this->extract_post_recursive( $key_name . '[' . htmlspecialchars( $key, ENT_QUOTES ) . ']', $val );
					$table .= $tmp_table;
					$form  .= $tmp_form;
				} else {
					$table .= '<tr><th>' . $key_name . '[' . htmlspecialchars( $key, ENT_QUOTES ) . ']</th><td>' . htmlspecialchars( $val, ENT_QUOTES ) . '</td></tr>' . "\n";
					$form  .= '<input type="hidden" name="' . $key_name . '[' . htmlspecialchars( $key, ENT_QUOTES ) . ']" value="' . htmlspecialchars( $val, ENT_QUOTES ) . '">' . "\n";
				}
			}

			return [ $table, $form ];
		}

		// clear all stubs
		public function clear() {
			$_SESSION['XOOPS_G_STUBS'] = [];
		}

		// Ticket Using
		public function using() {
			if ( ! empty( $_SESSION['XOOPS_G_STUBS'] ) ) {
				return true;
			} else {
				return false;
			}
		}

		// return errors
		public function getErrors( $ashtml = true ) {
			if ( $ashtml ) {
				$ret = '';
				foreach ( $this->_errors as $msg ) {
					$ret .= "$msg<br>\n";
				}
			} else {
				$ret = $this->_errors;
			}

			return $ret;
		}

		public function errorHandler4FindOutput( $errNo, $errStr, $errFile, $errLine ) {
			if ( preg_match( '?' . preg_quote( XOOPS_ROOT_PATH ) . '([^:]+)\:(\d+)?', $errStr, $regs ) ) {
				echo 'Irregular output! check the file ' . htmlspecialchars( $regs[1] ) . ' line ' . htmlspecialchars( $regs[2] );
			} else {
				echo 'Irregular output! check language files etc.';
			}

			return;
		}
		// end of class
	}

// create a instance in global scope
	$GLOBALS['xoopsGTicket'] = new XoopsGTicket();

}

if ( ! function_exists( 'admin_refcheck' ) ) {

//Admin Referer Check By Marijuana(Rev.011)
	function admin_refcheck( $chkref = '' ) {
		if ( empty( $_SERVER['HTTP_REFERER'] ) ) {
			return true;
		} else {
			$ref = $_SERVER['HTTP_REFERER'];
		}
		$cr = XOOPS_URL;
		if ( '' != $chkref ) {
			$cr .= $chkref;
		}

		return ! ( 0 !== strpos( $ref, $cr ) );
	}

}
