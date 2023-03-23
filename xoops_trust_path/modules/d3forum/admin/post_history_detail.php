<?php
/**
 * D3Forum module for XCL
 * @package    D3Forum
 * @version    XCL 2.3.3
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */


$db = XoopsDatabaseFactory::getDatabaseConnection();
//
// form stage
//
$history_id = (int) @$_GET['history_id'];

[ $data_serialized ] = $db->fetchRow( $db->query( 'SELECT data FROM ' . $db->prefix( $mydirname . '_post_histories' ) . " WHERE history_id=$history_id" ) );

$data = @unserialize( $data_serialized );

if ( empty( $data ) ) {
	exit;
}

$sql = 'INSERT INTO ' . $db->prefix( $mydirname . '_posts' ) . ' SET ' . "\n";

foreach ( $data as $key => $val ) {
	$sql .= "`$key`=" . $db->quoteString( $val ) . ",\n";
}
$sql = substr( $sql, 0, - 2 ) . ';';

//
// display stage
//

xoops_cp_header();

include __DIR__ . '/mymenu.php';

echo nl2br( htmlspecialchars( $sql, ENT_QUOTES ) );

xoops_cp_footer();
