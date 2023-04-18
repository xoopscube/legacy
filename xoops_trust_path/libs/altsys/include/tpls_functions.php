<?php
/**
 * Altsys library (UI-Components) for D3 modules
 *
 * @package    Altsys
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

include_once XOOPS_ROOT_PATH . '/class/template.php';

include_once __DIR__ . '/altsys_functions.php';

/**
 * @param     $tplset
 * @param     $tpl_file
 * @param     $tpl_source
 * @param int $lastmodified
 *
 * @return bool
 */
function tplsadmin_import_data( $tplset, $tpl_file, $tpl_source, $lastmodified = 0 ) {

	$db =& XoopsDatabaseFactory::getDatabaseConnection();

	// check the file is valid template

	[ $count ] = $db->fetchRow( $db->query( 'SELECT COUNT(*) FROM ' . $db->prefix( 'tplfile' ) . " WHERE tpl_tplset='default' AND tpl_file='" . addslashes( $tpl_file ) . "'" ) );

	if ( ! $count ) {
		return false;
	}

	// check the template exists in the tplset
	if ( 'default' != $tplset ) {
		[ $count ] = $db->fetchRow( $db->query( 'SELECT COUNT(*) FROM ' . $db->prefix( 'tplfile' ) . " WHERE tpl_tplset='" . addslashes( $tplset ) . "' AND tpl_file='" . addslashes( $tpl_file ) . "'" ) );

		if ( $count <= 0 ) {
			// copy from 'default' to the tplset

			$result = $db->query( 'SELECT * FROM ' . $db->prefix( 'tplfile' ) . " WHERE tpl_tplset='default' AND tpl_file='" . addslashes( $tpl_file ) . "'" );

			while ( false !== ( $row = $db->fetchArray( $result ) ) ) {
				$db->queryF( 'INSERT INTO '
				             . $db->prefix( 'tplfile' )
				             . " SET tpl_refid='"
				             . addslashes( $row['tpl_refid'] )
				             . "',tpl_module='"
				             . addslashes( $row['tpl_module'] )
				             . "',tpl_tplset='"
				             . addslashes( $tplset )
				             . "',tpl_file='"
				             . addslashes( $tpl_file )
				             . "',tpl_desc='"
				             . addslashes( $row['tpl_desc'] )
				             . "',tpl_type='"
				             . addslashes( $row['tpl_type'] )
				             . "'" );

				$tpl_id = $db->getInsertId();

				$db->queryF( 'INSERT INTO ' . $db->prefix( 'tplsource' ) . " SET tpl_id='$tpl_id', tpl_source=''" );
			}
		}
	}

	// UPDATE just tpl_lastmodified and tpl_source

	$drs = $db->query( 'SELECT tpl_id FROM ' . $db->prefix( 'tplfile' ) . " WHERE tpl_tplset='" . addslashes( $tplset ) . "' AND tpl_file='" . addslashes( $tpl_file ) . "'" );

	while ( [$tpl_id] = $db->fetchRow( $drs ) ) {
		$db->queryF( 'UPDATE ' . $db->prefix( 'tplfile' ) . " SET tpl_lastmodified='" . addslashes( $lastmodified ) . "',tpl_lastimported=UNIX_TIMESTAMP() WHERE tpl_id='$tpl_id'" );

		$db->queryF( 'UPDATE ' . $db->prefix( 'tplsource' ) . " SET tpl_source='" . addslashes( $tpl_source ) . "' WHERE tpl_id='$tpl_id'" );

		altsys_template_touch( $tpl_id );
	}

	return true;
}

/**
 * @param $lines
 *
 * @return string
 */
function tplsadmin_get_fingerprint( $lines ) {
	$str = '';

	foreach ( $lines as $line ) {
		if ( trim( $line ) ) {
			$str .= md5( trim( $line ) );
		}
	}

	return md5( $str );
}

/**
 * @param        $tplset_from
 * @param        $tplset_to
 * @param string $whr_append
 */
function tplsadmin_copy_templates_db2db( $tplset_from, $tplset_to, $whr_append = '1' ) {
	global $db;

	// get tplfile and tplsource

	$result = $db->query( "SELECT tpl_refid,tpl_module,'"
	                      . addslashes( $tplset_to )
	                      . "',tpl_file,tpl_desc,tpl_lastmodified,tpl_lastimported,tpl_type,tpl_source FROM "
	                      . $db->prefix( 'tplfile' )
	                      . ' NATURAL LEFT JOIN '
	                      . $db->prefix( 'tplsource' )
	                      . " WHERE tpl_tplset='"
	                      . addslashes( $tplset_from )
	                      . "' AND ($whr_append)" );

	while ($row = $db->fetchArray($result)) {
		$tpl_source = array_pop( $row );

		$drs = $db->query( 'SELECT tpl_id FROM ' . $db->prefix( 'tplfile' ) . " WHERE tpl_tplset='" . addslashes( $tplset_to ) . "' AND ($whr_append) AND tpl_file='" . addslashes( $row['tpl_file'] ) . "' AND tpl_refid='" . addslashes( $row['tpl_refid'] ) . "'" );

		if ( ! $db->getRowsNum( $drs ) ) {
			// INSERT mode
			$sql = 'INSERT INTO ' . $db->prefix( 'tplfile' ) . ' (tpl_refid,tpl_module,tpl_tplset,tpl_file,tpl_desc,tpl_lastmodified,tpl_lastimported,tpl_type) VALUES (';
			foreach ( $row as $colval ) {
				$sql .= "'" . addslashes( $colval ) . "',";
			}

			$db->query( mb_substr( $sql, 0, - 1 ) . ')' );

			$tpl_id = $db->getInsertId();

			$db->query( 'INSERT INTO ' . $db->prefix( 'tplsource' ) . " SET tpl_id='$tpl_id', tpl_source='" . addslashes( $tpl_source ) . "'" );

			altsys_template_touch( $tpl_id );
		} else {
			while ( [$tpl_id] = $db->fetchRow( $drs ) ) {
				// UPDATE mode

				$db->query( 'UPDATE '
				            . $db->prefix( 'tplfile' )
				            . " SET tpl_refid='"
				            . addslashes( $row['tpl_refid'] )
				            . "',tpl_desc='"
				            . addslashes( $row['tpl_desc'] )
				            . "',tpl_lastmodified='"
				            . addslashes( $row['tpl_lastmodified'] )
				            . "',tpl_lastimported='"
				            . addslashes( $row['tpl_lastimported'] )
				            . "',tpl_type='"
				            . addslashes( $row['tpl_type'] )
				            . "' WHERE tpl_id='$tpl_id'" );

				$db->query( 'UPDATE ' . $db->prefix( 'tplsource' ) . " SET tpl_source='" . addslashes( $tpl_source ) . "' WHERE tpl_id='$tpl_id'" );

				altsys_template_touch( $tpl_id );
			}
		}
	}
}


/**
 * @param        $tplset_to
 * @param string $whr_append
 */
function tplsadmin_copy_templates_f2db( $tplset_to, $whr_append = '1' ) {
	global $db;

	// get tplsource
	//$result = $db->query('SELECT * FROM ' . $db->prefix('tplfile') . "  WHERE tpl_tplset='default' AND ($whr_append)") ;

	$result = $db->query( 'SELECT * FROM ' . $db->prefix( 'tplfile' ) . "  WHERE tpl_tplset='default' AND ($whr_append)" );

	while ($row = $db->fetchArray($result)) {
		$basefilepath = tplsadmin_get_basefilepath( $row['tpl_module'], $row['tpl_type'], $row['tpl_file'] );

		$tpl_source = rtrim( implode( '', file( $basefilepath ) ) );

		$lastmodified = filemtime( $basefilepath );

		$drs = $db->query( 'SELECT tpl_id FROM ' . $db->prefix( 'tplfile' ) . " WHERE tpl_tplset='" . addslashes( $tplset_to ) . "' AND ($whr_append) AND tpl_file='" . addslashes( $row['tpl_file'] ) . "' AND tpl_refid='" . addslashes( $row['tpl_refid'] ) . "'" );

		if ( ! $db->getRowsNum( $drs ) ) {
			// INSERT mode
			$sql = 'INSERT INTO '
			       . $db->prefix( 'tplfile' )
			       . " SET tpl_refid='"
			       . addslashes( $row['tpl_refid'] )
			       . "',tpl_desc='"
			       . addslashes( $row['tpl_desc'] )
			       . "',tpl_lastmodified='"
			       . addslashes( $lastmodified )
			       . "',tpl_type='"
			       . addslashes( $row['tpl_type'] )
			       . "',tpl_tplset='"
			       . addslashes( $tplset_to )
			       . "',tpl_file='"
			       . addslashes( $row['tpl_file'] )
			       . "',tpl_module='"
			       . addslashes( $row['tpl_module'] )
			       . "'";

			$db->query( $sql );

			$tpl_id = $db->getInsertId();

			$db->query( 'INSERT INTO ' . $db->prefix( 'tplsource' ) . " SET tpl_id='$tpl_id', tpl_source='" . addslashes( $tpl_source ) . "'" );

			altsys_template_touch( $tpl_id );
		} else {
			while ( [$tpl_id] = $db->fetchRow( $drs ) ) {
				// UPDATE mode

				$db->query( 'UPDATE ' . $db->prefix( 'tplfile' ) . " SET tpl_lastmodified='" . addslashes( $lastmodified ) . "' WHERE tpl_id='$tpl_id'" );

				$db->query( 'UPDATE ' . $db->prefix( 'tplsource' ) . " SET tpl_source='" . addslashes( $tpl_source ) . "' WHERE tpl_id='$tpl_id'" );

				altsys_template_touch( $tpl_id );
			}
		}
	}
}

/**
 * @param $dirname
 * @param $type
 * @param $tpl_file
 *
 * @return string
 */
function tplsadmin_get_basefilepath( $dirname, $type, $tpl_file ) {
	$mytrustdirname = null;
 // module instance

	$path = $basefilepath = XOOPS_ROOT_PATH . '/modules/' . $dirname . '/templates/' . ( 'block' == $type ? 'blocks/' : '' ) . $tpl_file;

	if ( is_callable( 'Legacy_Utils::getTrustDirnameByDirname' ) ) {
		$mytrustdirname = Legacy_Utils::getTrustDirnameByDirname( $dirname );
	}
//    elseif ( ! defined( 'XOOPS_CUBE_LEGACY' ) ) {
//		$mytrustdirname = XOOPS_PATH;
//	}

	if ( defined( 'ALTSYS_TPLSADMIN_BASEPATH' ) ) {
		// Special hook

		$path = ALTSYS_TPLSADMIN_BASEPATH . '/' . substr( $tpl_file, strlen( $dirname ) + 1 );
	} elseif ( $mytrustdirname || @include XOOPS_ROOT_PATH . '/modules/' . $dirname . '/mytrustdirname.php' ) {
		// D3 module base
		if ( ! empty( $mytrustdirname ) ) {
			$mid_path = 'altsys' == $mytrustdirname ? '/libs/' : '/modules/';

			$path = XOOPS_TRUST_PATH . $mid_path . $mytrustdirname . '/templates/' . ( 'block' == $type ? 'blocks/' : '' ) . substr( $tpl_file, strlen( $dirname ) + 1 );

			//new for xcck etc.other trust_module

			if ( ! file_exists( $path ) ) {
				$path = XOOPS_TRUST_PATH . $mid_path . $mytrustdirname . '/templates/' . ( 'block' == $type ? 'blocks/' : '' ) . $tpl_file;

				if ( ! file_exists( $path ) ) {
					$path = $basefilepath;
				}
			}
		}
	}

	return $path;
}

/**
 * @param        $msg
 * @param string $target_dirname
 * @param int $wait
 */
function tplsadmin_die( $msg, $target_dirname = '', $wait = 2 ) {
	if ( 'post' === mb_strtolower( $_SERVER['REQUEST_METHOD'] ) ) {
		redirect_header( '?mode=admin&lib=altsys&page=mytplsadmin&dirname=' . $target_dirname, $wait, $msg );

		exit;
	}

	die( $msg );
}
