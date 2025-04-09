<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

function b_pico_mywaitings_show( $options ) {
	$ret = [];
 global $xoopsUser;

	$mydirname     = empty( $options[0] ) ? 'pico' : $options[0];
	$this_template = empty( $options[1] ) ? 'db:' . $mydirname . '_block_mywaitings.html' : trim( $options[1] );

	if ( preg_match( '/[^0-9a-zA-Z_-]/', $mydirname ) ) {
		die( 'Invalid mydirname' );
	}

	$db = XoopsDatabaseFactory::getDatabaseConnection();

	( method_exists( 'MyTextSanitizer', 'sGetInstance' ) and $myts = &MyTextSanitizer::sGetInstance() ) || $myts = &( new MyTextSanitizer )->getInstance();

	$uid = is_object( @$xoopsUser ) ? $xoopsUser->getVar( 'uid' ) : 0;

	$module_handler = &xoops_gethandler( 'module' );
	$module         = &$module_handler->getByDirname( $mydirname );
	$config_handler = &xoops_gethandler( 'config' );
	$configs        = $config_handler->getConfigList( $module->mid() );

	$sql = 'SELECT o.content_id,o.subject_waiting,o.modified_time FROM ' . $db->prefix( $mydirname . '_contents' ) . " o WHERE ((o.poster_uid=$uid AND !visible) OR (o.modifier_uid=$uid AND visible)) AND approval=0 ORDER BY o.modified_time DESC";
	if ( ! $result = $db->query( $sql ) ) {
		echo $db->logger->dumpQueries();
		exit;
	}

	if ( $db->getRowsNum( $result ) <= 0 ) {
		return [];
	}

	$contents4assign = [];
	while ( $content_row = $db->fetchArray( $result ) ) {
		$contents4assign[] = [
			'id'                      => $content_row['content_id'],
			'subject_waiting_raw'     => $content_row['subject_waiting'],
			'modified_time'           => $content_row['modified_time'],
			'modified_time_formatted' => formatTimestamp( $content_row['modified_time'] ),
		];
	}

	// const pref
	$constpref = '_MB_' . strtoupper( $mydirname );

	$block = [
		'mydirname'    => $mydirname,
		'mod_url'      => XOOPS_URL . '/modules/' . $mydirname,
		'mod_imageurl' => XOOPS_URL . '/modules/' . $mydirname . '/' . $configs['images_dir'],
		'mod_config'   => $configs,
		'contents'     => $contents4assign,
	];

	if ( empty( $options['disable_renderer'] ) ) {

		require_once XOOPS_ROOT_PATH . '/class/template.php';

		$tpl = new XoopsTpl();

		$tpl->assign( 'block', $block );
		$ret['content'] = $tpl->fetch( $this_template );

		return $ret;
	}

	return $block;
}

function b_pico_mywaitings_edit( $options ) {
	$mydirname = empty( $options[0] ) ? 'pico' : $options[0];

	$this_template = empty( $options[1] ) ? 'db:' . $mydirname . '_block_mywaitings.html' : trim( $options[1] );

	if ( preg_match( '/[^0-9a-zA-Z_-]/', $mydirname ) ) {
		die( 'Invalid mydirname' );
	}

	require_once XOOPS_ROOT_PATH . '/class/template.php';
	$tpl = new XoopsTpl();
	$tpl->assign(
		[
			'mydirname'     => $mydirname,
			'this_template' => $this_template,
		]
	);

	return $tpl->fetch( 'db:' . $mydirname . '_blockedit_mywaitings.html' );
}
