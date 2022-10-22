<?php
/**
 * Protector module for XCL
 *
 * @package    Protector
 * @version    XCL 2.3.1
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Authors
 * @license    GPL v2.0
 */

if ( file_exists( XOOPS_ROOT_PATH . '/class/database/drivers/' . XOOPS_DB_TYPE . '/database.php' ) ) {
	require_once XOOPS_ROOT_PATH . '/class/database/drivers/' . XOOPS_DB_TYPE . '/database.php';
} else {
	require_once XOOPS_ROOT_PATH . '/class/database/' . XOOPS_DB_TYPE . 'database.php';
}

require_once XOOPS_ROOT_PATH . '/class/database/database.php';

eval( 'class ProtectorMySQLDatabase_base extends Xoops' . ucfirst( XOOPS_DB_TYPE ) . 'DatabaseProxy{}' );

class ProtectorMySQLDatabase extends ProtectorMySQLDatabase_base {
	public $doubtful_requests = [];
	public $doubtful_needles = [
		// 'order by' ,
		'concat',
		'information_schema',
		'select',
		'union',
		'/*', /**/
		'--',
		'#',
	];

	public function __construct() {
		$protector               = Protector::getInstance();
		$this->doubtful_requests = $protector->getDblayertrapDoubtfuls();
		$this->doubtful_needles  = array_merge( $this->doubtful_needles, $this->doubtful_requests );
	}


	public function injectionFound( $sql ) {
		$protector = Protector::getInstance();

		$protector->last_error_type = 'SQL Injection';
		$protector->message         .= $sql;
		$protector->output_log( $protector->last_error_type );
		die( 'SQL Injection found' );
	}


	public function separateStringsInSQL( $sql ) {
		$sql            = trim( $sql );
		$sql_len        = strlen( $sql );
		$char           = '';
		$string_start   = '';
		$in_string      = false;
		$sql_wo_string  = '';
		$strings        = [];
		$current_string = '';

		for ( $i = 0; $i < $sql_len; $i ++ ) {
			$char = $sql[ $i ];
			if ( $in_string ) {
				while ( 1 ) {
					$new_i          = strpos( $sql, $string_start, $i );
					$current_string .= substr( $sql, $i, $new_i - $i + 1 );
					$i              = $new_i;
					if ( false === $i ) {
						break 2;
					} elseif (/* $string_start == '`' || */ '\\' != $sql[ $i - 1 ] ) {
						$string_start = '';
						$in_string    = false;
						$strings[]    = $current_string;
						break;
					} else {
						$j                 = 2;
						$escaped_backslash = false;
						while ( $i - $j > 0 && '\\' == $sql[ $i - $j ] ) {
							$escaped_backslash = ! $escaped_backslash;
							$j ++;
						}
						if ( $escaped_backslash ) {
							$string_start = '';
							$in_string    = false;
							$strings[]    = $current_string;
							break;
						} else {
							$i ++;
						}
					}
				}
			} elseif ( '"' == $char || "'" == $char ) { // dare to ignore ``
				$in_string      = true;
				$string_start   = $char;
				$current_string = $char;
			} else {
				$sql_wo_string .= $char;
			}
			// dare to ignore comment
			// because unescaped ' or " have been already checked in stage1
		}

		return [ $sql_wo_string, $strings ];
	}


	public function checkSql( $sql ) {
		list( $sql_wo_strings, $strings ) = $this->separateStringsInSQL( $sql );

		// stage1: addslashes() processed or not
		foreach ( $this->doubtful_requests as $request ) {
			if ( addslashes( $request ) != $request ) {
				if ( stristr( $sql, trim( $request ) ) ) {
					// check the request stayed inside of strings as whole
					$ok_flag = false;
					foreach ( $strings as $string ) {
						if ( strstr( $string, $request ) ) {
							$ok_flag = true;
							break;
						}
					}
					if ( ! $ok_flag ) {
						$this->injectionFound( $sql );
					}
				}
			}
		}

		// stage2: doubtful requests exists and outside of quotations ('or")
		// $_GET['d'] = '1 UNION SELECT ...'
		// NG: select a from b where c=$d
		// OK: select a from b where c='$d_escaped'
		// $_GET['d'] = '(select ... FROM)'
		// NG: select a from b where c=(select ... from)
		foreach ( $this->doubtful_requests as $request ) {
			if ( strstr( $sql_wo_strings, trim( $request ) ) ) {
				$this->injectionFound( $sql );
			}
		}

		// stage3: comment exists or not without quoted strings (too sensitive?)
		if ( preg_match( '/(\/\*|\-\-|\#)/', $sql_wo_strings, $regs ) ) {
			foreach ( $this->doubtful_requests as $request ) {
				if ( strstr( $request, $regs[1] ) ) {
					$this->injectionFound( $sql );
				}
			}
		}
	}


	public function &query( $sql, $limit = 0, $start = 0 ) {
		$sql4check = substr( $sql, 7 );
		foreach ( $this->doubtful_needles as $needle ) {
			if ( stristr( $sql4check, $needle ) ) {
				$this->checkSql( $sql );
				break;
			}
		}

		if ( ! defined( 'XOOPS_DB_PROXY' ) ) {
			$ret = parent::queryF( $sql, $limit, $start );
		} else {
			$ret = parent::query( $sql, $limit, $start );
		}

		return $ret;
	}
}
