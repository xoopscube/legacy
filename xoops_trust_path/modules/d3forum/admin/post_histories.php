<?php
/**
 * D3Forum module for XCL
 * @package    D3Forum
 * @version    XCL 2.4.0
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

require_once dirname( __DIR__ ) . '/include/main_functions.php';
require_once dirname( __DIR__ ) . '/include/common_functions.php';
require_once dirname( __DIR__ ) . '/class/d3forum.textsanitizer.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

$myts = D3forumTextSanitizer::sGetInstance();

$db = XoopsDatabaseFactory::getDatabaseConnection();

//
// form stage
//
$pos = (int) @$_GET['pos'];

$num = empty( $_GET['num'] ) ? 50 : (int) $_GET['num'];

$request = d3forum_common_simple_request( [ 'p.topic_id' => 'int', 'p.post_id' => 'int', 'ph.data' => 'like' ] );

[ $hits ] = $db->fetchRow( $db->query( 'SELECT COUNT(*) FROM ' . $db->prefix( $mydirname . '_post_histories' ) . ' ph LEFT JOIN ' . $db->prefix( $mydirname . '_posts' ) . " p ON ph.post_id=p.post_id WHERE {$request['whr']}" ) );

$result = $db->query( 'SELECT ph.history_id,ph.post_id,ph.history_time,ph.data,p.subject FROM ' . $db->prefix( $mydirname . '_post_histories' ) . ' ph LEFT JOIN ' . $db->prefix( $mydirname . '_posts' ) . " p ON ph.post_id=p.post_id WHERE {$request['whr']} ORDER BY ph.history_time DESC LIMIT $pos,$num" );

$navi_obj = new XoopsPageNav( $hits, $num, $pos, 'pos', htmlspecialchars( 'page=post_histories&' . $request['query'] ) );

$histories4assign = [];

while ( [$history_id, $post_id, $history_time, $data_serialized, $subject] = $db->fetchRow( $result ) ) {

	$histories4assign[] = [
		'id'                     => $history_id,
		'post_id'                => $post_id,
		'history_time'           => $history_time,
		'history_time_formatted' => formatTimestamp( $history_time ),
		'data'                   => unserialize( $data_serialized ),
		'subject_raw'            => $subject,
	];
}


//
// display stage
//

xoops_cp_header();

include __DIR__ . '/mymenu.php';

$tpl = new XoopsTpl();

$tpl->assign( [
		'mydirname'    => $mydirname,
		'mod_name'     => $xoopsModule->getVar( 'name' ),
		'mod_url'      => XOOPS_URL . '/modules/' . $mydirname,
		'mod_imageurl' => XOOPS_URL . '/modules/' . $mydirname . '/' . $xoopsModuleConfig['images_dir'],
		'mod_config'   => $xoopsModuleConfig,
		'histories'    => $histories4assign,
		'pagenavi'     => $navi_obj->renderNav(),
		'requests'     => $request['requests'],
	]
);

$tpl->display( 'db:' . $mydirname . '_admin_post_histories.html' );

xoops_cp_footer();
