<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Author
 * @license    GPL v2.0
 */

require_once dirname( __DIR__ ) . '/include/main_functions.php';
require_once dirname( __DIR__ ) . '/include/common_functions.php';
require_once dirname( __DIR__ ) . '/include/transact_functions.php';
require_once dirname( __DIR__ ) . '/include/import_functions.php';
require_once dirname( __DIR__ ) . '/include/history_functions.php';
require_once dirname( __DIR__ ) . '/class/PicoTextSanitizer.class.php';
require_once dirname( __DIR__ ) . '/class/gtickets.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

$myts = &PicoTextSanitizer::sGetInstance();

$db = XoopsDatabaseFactory::getDatabaseConnection();

//
// transaction stage
//

// extras output
if ( !empty( $_POST['extras_output'] ) && is_array( $_POST['action_selects'] ) ) {
	$extra_rows = [];
	$columns    = [ 'id' => 0, 'content_id' => 0, 'type' => '', 'created' => '', 'modified' => '' ];
	foreach ( $_POST['action_selects'] as $extra_id => $value ) {
		if ( empty( $value ) ) {
			continue;
		}
		$extra_id  = (int) $extra_id;
		$extra_row = $db->fetchArray( $db->query( 'SELECT ce.*,o.vpath,o.subject AS content_subject FROM ' . $db->prefix( $mydirname . '_content_extras' ) . ' ce LEFT JOIN ' . $db->prefix( $mydirname . '_contents' ) . " o ON o.content_id=ce.content_id WHERE content_extra_id=$extra_id" ) );
		$data      = pico_common_unserialize( $extra_row['data'] );
		if ( !is_array( $data ) ) {
			$data = [ $extra_row['data'] ];
		}
		$extra_rows[] = [
			                'id'         => (int) $extra_row['content_extra_id'],
			                'content_id' => (int) $extra_row['content_id'],
			                'type'       => $extra_row['extra_type'],
			                'created'    => formatTimestamp( $extra_row['created_time'] ),
			                'modified'   => formatTimestamp( $extra_row['modified_time'] ),
		                ] + $data;
		$columns      += $data;
	}

	$out = '';
	foreach ( array_keys( $columns ) as $col ) {
		$out .= '"' . str_replace( '"', '""', $col ) . '",';
	}
	$out = substr( $out, 0, - 1 ) . "\n";

	if ( function_exists( 'easiestml' ) ) {
		$out = easiestml( $out );
	}

	foreach ( $extra_rows as $extra_row ) {
		foreach ( array_keys( $columns ) as $col ) {
			$val = pico_admin_make_summary4extras( @$extra_row[ $col ] );
			$out .= '"' . str_replace( '"', '""', $val ) . '",';
		}
		$out = substr( $out, 0, - 1 ) . "\n";
	}

	if ( function_exists( 'mb_convert_encoding' ) ) {
		$out = mb_convert_encoding( $out, _MD_PICO_CSVENCODING, _CHARSET );
	}

	header( 'Cache-Control: public' ); // for IE
	header( 'Pragma: public' ); // for IE
	header( 'Content-Type: application/force-download' );
	header( 'Content-Disposition: attachment; filename=' . $mydirname . '_extras_' . date( 'Ymd' ) . '.csv' );
	header( 'Content-Description: File Transfer' );
	echo $out;
	exit;
}

// extras delete
if ( ! empty( $_POST['extras_delete'] ) && ! empty( $_POST['action_selects'] ) ) {
	if ( ! $xoopsGTicket->check( true, 'pico_admin' ) ) {
		redirect_header( XOOPS_URL . '/', 2, $xoopsGTicket->getErrors() );
	}

	foreach ( $_POST['action_selects'] as $extra_id => $value ) {
		if ( empty( $value ) ) {
			continue;
		}
		$extra_id = (int) $extra_id;
		$db->query( 'DELETE FROM ' . $db->prefix( $mydirname . '_content_extras' ) . " WHERE content_extra_id=$extra_id" );
	}

	redirect_header( XOOPS_URL . "/modules/$mydirname/admin/index.php?page=extras", 1, _MD_A_PICO_MSG_DELETED );
	exit;
}

//
// form stage
//

// requests for form
$extra_id   = (int) @$_GET['extra_id'];
$content_id = (int) @$_GET['content_id'];
$txt_raw    = $myts->stripSlashesGPC( @$_GET['txt'] );
$txt        = preg_replace( '/[%_]/', '', $txt_raw ?? '' );
$pos        = empty( $_GET['pos'] ) ? 0 : (int) $_GET['pos'];
$num        = empty( $_GET['num'] ) ? 30 : (int) $_GET['num'];
$order      = in_array( @$_GET['order'], [
	'ce.created_time',
	'ce.created_time',
	'ce.extra_type',
	'c.content_id'
] ) ? $_GET['order'] : 'ce.created_time DESC';
// create WHERE part
$whr_extra_id   = $extra_id > 0 ? "ce.content_extra_id=$extra_id" : '1';
$whr_content_id = $content_id > 0 ? "ce.content_id=$content_id" : '1';
$whr_txt        = $txt ? "ce.data LIKE '%" . addslashes( $txt ) . "%'" : '1';

// pre query
[ $hit ] = $db->fetchRow( $db->query( 'SELECT COUNT(*) FROM ' . $db->prefix( $mydirname . '_content_extras' ) . ' ce LEFT JOIN ' . $db->prefix( $mydirname . '_contents' ) . " o ON o.content_id=ce.content_id WHERE $whr_extra_id AND $whr_content_id AND $whr_txt" ) );

// pagenav
$pagenav     = '';
$pagenav_obj = new XoopsPageNav( $hit, $num, $pos, 'pos', "page=extras&amp;num=$num&amp;content_id=$content_id&amp;order=" . urlencode( $order ) . '&amp;txt=' . urlencode( $txt ) );
$pagenav     = $pagenav_obj->renderNav();

// main query
$ers = $db->query(
	'SELECT ce.*,o.vpath,o.subject AS content_subject FROM ' . $db->prefix( $mydirname . '_content_extras' ) . ' ce LEFT JOIN ' . $db->prefix( $mydirname . '_contents' ) . " o ON o.content_id=ce.content_id WHERE $whr_extra_id AND $whr_content_id AND $whr_txt ORDER BY $order LIMIT $pos,$num" );

$extras4assign = [];
while ( $extra_row = $db->fetchArray( $ers ) ) {
	$data = pico_common_unserialize( $extra_row['data'] );
	if ( empty( $data ) ) {
		$data = $extra_row['data'];
	}
	$extra4assign    = [
		'id'                     => (int) $extra_row['content_extra_id'],
		'link'                   => pico_common_make_content_link4html( $xoopsModuleConfig, $extra_row ),
		'extra_type_formatted'   => str_replace( '::', '<br>', htmlspecialchars( $extra_row['extra_type'], ENT_QUOTES ) ),
		'created_time_formatted' => formatTimestamp( $extra_row['created_time'] ),
		'data'                   => $data,
		'data_summary_short_raw' => xoops_substr( pico_admin_make_summary4extras( $data ), 0, 100 ),
		'data_summary_raw'       => pico_admin_make_summary4extras( $data ),
	];
	$extras4assign[] = $extra4assign + $extra_row;
}

//
// display stage
//

xoops_cp_header();

include __DIR__ . '/mymenu.php';

$tpl = new XoopsTpl();

$tpl->assign(
	[
		'mydirname'      => $mydirname,
		'mod_name'       => $xoopsModule->getVar( 'name' ),
		'mod_url'        => XOOPS_URL . '/modules/' . $mydirname,
		'mod_imageurl'   => XOOPS_URL . '/modules/' . $mydirname . '/' . $xoopsModuleConfig['images_dir'],
		'mod_config'     => $xoopsModuleConfig,
		'extras'         => $extras4assign,
		'num'            => $num,
		'order'          => $order,
		'pagenav'        => $pagenav,
		'content_id'     => $content_id,
		'txt_raw'        => $txt,
		'gticket_hidden' => $xoopsGTicket->getTicketHtml( __LINE__, 1800, 'pico_admin' ),
	]
);

if ( $extra_id ) {
	$tpl->display( 'db:' . $mydirname . '_admin_extras_detail.html' );
} else {
	$tpl->display( 'db:' . $mydirname . '_admin_extras.html' );
}

xoops_cp_footer();

//
// local function stage
//

function pico_admin_make_summary4extras( $data ) {
	$ret = '';
	if ( is_array( $data ) ) {
		if ( is_string( @$data[0] ) || empty( $data ) ) {
			// linear
			$ret = implode( ',', $data );
		} else {
			foreach ( $data as $key => $val ) {
				$ret .= pico_admin_easiestml( $key ) . ':' . pico_admin_make_summary4extras( $val ) . "\n";
			}
		}
	} else {
		$ret = $data;
	}

	return $ret;
}

function pico_admin_easiestml( $s ) {
	if ( function_exists( 'easiestml' ) ) {
		return easiestml( $s );
	}

	return $s;
}
