<?php
/**
 * Protector module for XCL - Administration panel.
 * @package    Protector
 * @version    XCL 2.3.3
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

require_once dirname( __DIR__ ) . '/class/gtickets.php';
require_once dirname( __DIR__ ) . '/class/dbIntegrate.php';

$db =& Database::getInstance();

// COPY TABLES
if ( ! empty( $_POST['copy'] ) && ! empty( $_POST['old_prefix'] ) ) {
	if ( preg_match( '/[^0-9A-Za-z_-]/', $_POST['new_prefix'] ) ) {
		die( 'wrong prefix' );
	}

	// Ticket check
	if ( ! $xoopsGTicket->check( true, 'protector_admin' ) ) {
		redirect_header( XOOPS_URL . '/', 3, $xoopsGTicket->getErrors() );
	}

	$new_prefix = empty( $_POST['new_prefix'] ) ? 'x' . substr( md5( time() ), - 5 ) : $_POST['new_prefix'];
	$old_prefix = $_POST['old_prefix'];

	$srs = $db->queryF( 'SHOW TABLE STATUS FROM `' . XOOPS_DB_NAME . '`' );

	if ( ! $db->getRowsNum( $srs ) ) {
		die( 'You are not allowed to copy tables' );
	}

	$count = 0;

	//while ( $row_table = $db->fetchArray( $srs ) ) {
while (false !== ($row_table = $db->fetchArray($srs))) {
		$count ++;
		$old_table = $row_table['Name'];
		if ( substr( $old_table, 0, strlen( $old_prefix ) + 1 ) !== $old_prefix . '_' ) {
			continue;
		}

		$new_table = $new_prefix . substr( $old_table, strlen( $old_prefix ) );

		$crs = $db->queryF( 'SHOW CREATE TABLE ' . $old_table );
		if ( ! $db->getRowsNum( $crs ) ) {
			echo "error: SHOW CREATE TABLE ($old_table)<br>\n";
			continue;
		}
		$row_create = $db->fetchArray( $crs );
		$create_sql = preg_replace( "/^CREATE TABLE `$old_table`/", "CREATE TABLE `$new_table`", $row_create['Create Table'], 1 );

		$crs = $db->queryF( $create_sql );
		if ( ! $crs ) {
			echo "error: CREATE TABLE ($new_table)<br>\n";
			continue;
		}

		$irs = $db->queryF( "INSERT INTO `$new_table` SELECT * FROM `$old_table`" );
		if ( ! $irs ) {
			echo "error: INSERT INTO ($new_table)<br>\n";
			continue;
		}
	}

	$_SESSION['protector_logger'] = $xoopsLogger->dumpQueries();

	redirect_header( 'index.php?page=prefix_manager', 1, _AM_MSG_DBUPDATED );
	exit;

// DUMP INTO A LOCAL FILE
} elseif ( ! empty( $_POST['prefix'] ) && ( ! empty( $_POST['backup'] ) || ! empty( $_POST['download_zip'] ) || ! empty( $_POST['download_tgz'] ) ) ) {
	if ( preg_match( '/[^0-9A-Za-z_-]/', $_POST['prefix'] ) ) {
		die( 'wrong prefix' );
	}

	// Ticket check
	if ( ! $xoopsGTicket->check( true, 'protector_admin' ) ) {
		redirect_header( XOOPS_URL . '/', 3, $xoopsGTicket->getErrors() );
	}

	$dbIntegrate = new protectorDbIntegrate( $db->conn );

	$prefix = $_POST['prefix'];//check line 61
//HACK by suin & nao-pon 2012/01/06
	while ( ob_get_level() > 0 ) {
		if ( ! ob_end_clean() ) {
			break;
		}
	}

	// get table list
	$srs = $db->queryF( 'SHOW TABLE STATUS FROM `' . XOOPS_DB_NAME . '`' );
	if ( ! $db->getRowsNum( $srs ) ) {
		die( 'You are not allowed to delete tables' );
	}

	$export_string = '';
	$tempfile      = tmpfile();
	while ( $row_table = $db->fetchArray( $srs ) ) {
		$table = $row_table['Name'];
		if ( substr( $table, 0, strlen( $prefix ) + 1 ) !== $prefix . '_' ) {
			continue;
		}
		$drs = $db->queryF( "SHOW CREATE TABLE `$table`" );
		list( , $create ) = $db->fetchRow( $drs );
		$line = "\nDROP TABLE IF EXISTS `$table`;\n" . $create . ";\n\n";
		if ( $tempfile ) {
			fwrite( $tempfile, $line );
		} else {
			$export_string .= $line;
		}
		$db->freeRecordSet( $drs );
		$result      = $db->query( "SELECT * FROM `$table`" );
		$fields_cnt  = $db->getFieldsNum( $result );
		$field_flags = [];
		for ( $j = 0; $j < $fields_cnt; $j ++ ) {
			$field_flags[ $j ] = $dbIntegrate->fieldFlags( $result, $j );
		}
		$search      = [ "\x00", "\x0a", "\x0d", "\x1a" ];
		$replace     = [ '\0', '\n', '\r', '\Z' ];
		$current_row = 0;
		while ( $row = $db->fetchRow( $result ) ) {
			$current_row ++;
			for ( $j = 0; $j < $fields_cnt; $j ++ ) {
				$fields_meta = $dbIntegrate->fetchField( $result, $j );
				// NULL
				if ( ! isset( $row[ $j ] ) || null === $row[ $j ] ) {
					$values[] = 'NULL';
					// a number
					// timestamp is numeric on some MySQL 4.1
				} elseif ( preg_match( '/^[0-9]+(?:\.[0-9]+)?$/', $row[ $j ] ) && 'timestamp' != $fields_meta->type ) {
					$values[] = $row[ $j ];
					// a binary field
					// Note: with mysqli, under MySQL 4.1.3, we get the flag
					// "binary" for those field types (I don't know why)
				} elseif ( stristr( $field_flags[ $j ], 'BINARY' )
				           && 'datetime' != $fields_meta->type
				           && 'date' != $fields_meta->type
				           && 'time' != $fields_meta->type
				           && 'timestamp' != $fields_meta->type
				) {
					// empty blobs need to be different, but '0' is also empty :-(
					if ( empty( $row[ $j ] ) && '0' != $row[ $j ] ) {
						$values[] = '\'\'';
					} else {
						$values[] = '0x' . bin2hex( $row[ $j ] );
					}
					// something else -> treat as a string
				} else {
					$values[] = '\'' . str_replace( $search, $replace, addslashes( $row[ $j ] ) ) . '\'';
				} // end if
			} // end for
			$line = "INSERT INTO `$table` VALUES (" . implode( ', ', $values ) . ");\n";
			if ( $tempfile ) {
				fwrite( $tempfile, $line );
			} else {
				$export_string .= $line;
			}
			unset( $values );
		} // end while
		$db->freeRecordSet( $result );
	}

	$sqlfile_name = $prefix . '_' . date( 'YmdHis' ) . '.sql';

	if ( $tempfile ) {
		rewind( $tempfile );
		while ( ! feof( $tempfile ) ) {
			$export_string .= fread( $tempfile, 8192 );
		}
		fclose( $tempfile );
	}

// by domifara to add action zip ,ta.gz download
	if ( ! empty( $_POST['download_zip'] ) ) {
		require_once XOOPS_ROOT_PATH . '/class/zipdownloader.php';
		$downloader = new XoopsZipDownloader();
		$downloader->addFileData( $export_string, $sqlfile_name, time() );
		$downloader->download( $sqlfile_name, true );
		exit;
	} elseif ( ! empty( $_POST['download_tgz'] ) ) {
		require_once XOOPS_ROOT_PATH . '/class/tardownloader.php';
		$downloader = new XoopsTarDownloader();
		$downloader->addFileData( $export_string, $sqlfile_name, time() );
		$downloader->download( $sqlfile_name, true );
		exit;
	}

//fix for mb_http_output setting and for any browsers
	if ( function_exists( 'mb_http_output' ) ) {
		mb_http_output( 'pass' );
	}
	header( 'Pragma: public' ); // required
	header( 'Expires: 0' );
	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Cache-Control: private', false ); // required for certain browsers
	header( 'Content-Type: Application/octet-stream' );
	header( 'Content-Disposition: attachment; filename="' . $sqlfile_name . '"' );
	header( 'Content-Transfer-Encoding: binary' );
	header( 'Content-Length: ' . strlen( $export_string ) );
	set_time_limit( 0 );
	echo $export_string;
	exit;

// DROP TABLES
} elseif ( ! empty( $_POST['delete'] ) && ! empty( $_POST['prefix'] ) ) {
	if ( preg_match( '/[^0-9A-Za-z_-]/', $_POST['prefix'] ) ) {
		die( 'wrong prefix' );
	}

	// Ticket check
	if ( ! $xoopsGTicket->check( true, 'protector_admin' ) ) {
		redirect_header( XOOPS_URL . '/', 3, $xoopsGTicket->getErrors() );
	}

	$prefix = $_POST['prefix'];

	// check if prefix is working
	if ( XOOPS_DB_PREFIX == $prefix ) {
		die( "You can't drop working tables" );
	}

	// check if prefix_xoopscomments exists
	$check_rs = $db->queryF( "SELECT * FROM {$prefix}_xoopscomments LIMIT 1" );
	if ( ! $check_rs ) {
		die( 'This is not a prefix for comments' );
	}

	// get table list
	$srs = $db->queryF( 'SHOW TABLE STATUS FROM `' . XOOPS_DB_NAME . '`' );
	if ( ! $db->getRowsNum( $srs ) ) {
		die( 'You are not allowed to delete tables' );
	}

	while ( $row_table = $db->fetchArray( $srs ) ) {
		$table = $row_table['Name'];
		if ( substr( $table, 0, strlen( $prefix ) + 1 ) !== $prefix . '_' ) {
			continue;
		}
		$drs = $db->queryF( "DROP TABLE `$table`" );
	}

	$_SESSION['protector_logger'] = $xoopsLogger->dumpQueries();

	redirect_header( 'index.php?page=prefix_manager', 1, _AM_MSG_DBUPDATED );
	exit;
}


// beginning of Output
xoops_cp_header();
include __DIR__ . '/mymenu.php';

// query
$srs = $db->queryF( 'SHOW TABLE STATUS FROM `' . XOOPS_DB_NAME . '`' );
if ( ! $db->getRowsNum( $srs ) ) {
	die( 'You are not allowed to copy tables' );
	xoops_cp_footer();
	exit;
}

// search prefixes
$tables   = [];
$prefixes = [];
while ( $row_table = $db->fetchArray( $srs ) ) {
	if ( '_users' === substr( $row_table['Name'], - 6 ) ) {
		$prefixes[] = [
			'name'    => substr( $row_table['Name'], 0, - 6 ),
			'updated' => $row_table['Update_time']
		];
	}
	$tables[] = $row_table['Name'];
}


// table
echo "<h2>" . _AM_H3_PREFIXMAN . "</h2>
    <div class='tips'>" . sprintf( _AM_TXT_HOWTOCHANGEDB, XOOPS_ROOT_PATH, XOOPS_DB_PREFIX ) . "</div>
    <table class='outer'>
    <thead>
	<tr>
		<th>PREFIX</th>
		<th>TABLES</th>
		<th>UPDATED</th>
		<th>COPY</th>
		<th class='list_control'>ACTIONS</th>
	</tr>
	</thead>";

foreach ( $prefixes as $prefix ) {

	// count the number of tables with the prefix
	$table_count       = 0;
	$has_xoopscomments = false;
	foreach ( $tables as $table ) {
		if ( $table == $prefix['name'] . '_xoopscomments' ) {
			$has_xoopscomments = true;
		}
		if ( substr( $table, 0, strlen( $prefix['name'] ) + 1 ) === $prefix['name'] . '_' ) {
			$table_count ++;
		}
	}

	// check if prefix_xoopscomments exists
	if ( ! $has_xoopscomments ) {
		continue;
	}

	$prefix4disp  = htmlspecialchars( $prefix['name'], ENT_QUOTES );
	$ticket_input = $xoopsGTicket->getTicketHtml( __LINE__, 1800, 'protector_admin' );

	if ( XOOPS_DB_PREFIX == $prefix['name'] ) {
		$del_button   = '';
		$style_append = 'background:transparent';
	} else {
		$del_button   = "<input class='button delete' type='submit' name='delete' value='delete' onclick='return confirm(\"" . _AM_CONFIRM_DELETE . "\")'>";
		$style_append = '';
	}

	echo "<tr>
		<td>$prefix4disp</td>
		<td>$table_count</td>
		<td>{$prefix['updated']}</td>
		<td>
			<form action='?page=prefix_manager' method='POST'>
				$ticket_input
				<input type='hidden' name='old_prefix' value='$prefix4disp'>
				<input type='text' name='new_prefix' size='8' maxlength='16'>
				<input type='submit' name='copy' value='copy'>
			</form>
		</td>
		<td class='list_control'>
			<form action='?page=prefix_manager' method='POST'>
				$ticket_input
				<input type='hidden' name='prefix' value='$prefix4disp'>
				$del_button
				<input class='button backup' type='submit' name='backup' value='backup' onclick='this.form.target=\"_blank\"'>";
	if ( function_exists( 'gzcompress' ) ) {
		echo "<input class='button download' type='submit' name='download_zip' value='zip' onclick='this.form.target=\"_blank\"'>";
	}
	if ( function_exists( 'gzencode' ) ) {
		echo "<input class='button download' type='submit' name='download_tgz' value='tar.gz' onclick='this.form.target=\"_blank\"'>";
	}
	echo "</form>
		</td>
	</tr>\n";
}

echo '</table>';

// Display Log if exists
if ( ! empty( $_SESSION['protector_logger'] ) ) {
	echo $_SESSION['protector_logger'];
	$_SESSION['protector_logger'] = '';
	unset( $_SESSION['protector_logger'] );
}

xoops_cp_footer();
