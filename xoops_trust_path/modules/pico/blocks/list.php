<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

function b_pico_list_allowed_order() {
	return [
		'o.weight',
		'o.weight DESC',
		'o.created_time',
		'o.created_time DESC',
		'o.modified_time',
		'o.modified_time DESC',
		'o.viewed',
		'o.viewed DESC',
		'o.votes_sum',
		'o.votes_sum DESC',
		'o.votes_count',
		'o.votes_count DESC',
		'o.weight,o.created_time',
		'o.weight,o.created_time DESC',
		'o.weight,o.content_id',
		'o.weight,o.content_id DESC',
	];
}

function b_pico_list_show( $options ) {
	// options
	$mytrustdirname = basename( dirname( __DIR__ ) );

	$mydirname      = empty( $options[0] ) ? $mytrustdirname : $options[0];
	$categories     = '' === trim( @$options[1] ) ? [] : array_map( 'intval', explode( ',', $options[1] ) );
	$selected_order = empty( $options[2] ) || ! in_array( $options[2], b_pico_list_allowed_order(), true ) ? 'o.created_time DESC' : $options[2];
	$limit_offset   = empty( $options[3] ) ? '10' : preg_replace( '/[^0-9,]/', '', $options[3] );
	if ( strpos( $limit_offset, ',' ) !== false ) {
		[ $offset, $limit ] = array_map( 'intval', explode( ',', $limit_offset ) );
	} else {
		$offset = 0;
		$limit  = (int) $limit_offset;
	}
	$this_template = empty( $options[4] ) ? 'db:' . $mydirname . '_block_list.html' : trim( $options[4] );
	$display_body  = empty( $options[5] ) ? false : true;

	// mydirname check
	if ( preg_match( '/[^0-9a-zA-Z_-]/', $mydirname ) ) {
		die( 'Invalid mydirname' );
	}

	// content handler
	$content_handler = new PicoContentHandler( $mydirname );

	// contentObjects
	if ( 0 === count( $categories ) ) {
		// no category specified
		$contents4assign = $content_handler->getContents4assign( '1', $selected_order, $offset, $limit, false );
	} else if ( 1 === count( $categories ) ) {
		// single category
		$contents4assign = $content_handler->getContents4assign( 'o.cat_id=' . $categories[0], $selected_order, $offset, $limit, false );
	} else {
		// multi category
		$contents4assign = $content_handler->getContents4assign( 'o.cat_id IN (' . implode( ',', $categories ) . ')', $selected_order, $offset, $limit, false );
	}

	// compatibility for 1.5/1.6
	foreach ( array_keys( $contents4assign ) as $i ) {
		$contents4assign[ $i ]['body'] = $display_body ? $contents4assign[ $i ]['body_cached'] : '';
	}

	// module config (not overridden yet)
	$module_handler = &xoops_gethandler( 'module' );
	$module         = &$module_handler->getByDirname( $mydirname );
	$config_handler = &xoops_gethandler( 'config' );
	$configs        = $config_handler->getConfigList( $module->mid() );

	// const pref
	$constpref = '_MB_' . strtoupper( $mydirname );

	// make an array named 'block'
	$block = [
		'mytrustdirname'   => $mytrustdirname,
		'mydirname'        => $mydirname,
		'mod_url'          => XOOPS_URL . '/modules/' . $mydirname,
		'mod_imageurl'     => XOOPS_URL . '/modules/' . $mydirname . '/' . $configs['images_dir'],
		'mod_config'       => $configs,
		'contents'         => $contents4assign,
		'display_body'     => $display_body,
		'lang_category'    => constant( $constpref . '_CATEGORY' ),
		'lang_topcategory' => constant( $constpref . '_TOPCATEGORY' ),
	];

	if ( empty( $options['disable_renderer'] ) ) {
		// render it
		require_once XOOPS_ROOT_PATH . '/class/template.php';

		$tpl = new XoopsTpl();

		$tpl->assign( 'block', $block );
		$ret['content'] = $tpl->fetch( $this_template );

		return $ret;
	}

// just assign it
	return $block;
}

function b_pico_list_edit( $options ) {
	// options
	$mytrustdirname = basename( dirname( __DIR__ ) );

	$mydirname      = empty( $options[0] ) ? $mytrustdirname : $options[0];
	$categories     = '' === trim( @$options[1] ) ? [] : array_map( 'intval', explode( ',', $options[1] ) );
	$selected_order = empty( $options[2] ) || ! in_array( $options[2], b_pico_list_allowed_order(), true ) ? 'o.created_time DESC' : $options[2];
	$limit_offset   = empty( $options[3] ) ? '10' : preg_replace( '/[^0-9,]/', '', $options[3] );
	$this_template  = empty( $options[4] ) ? 'db:' . $mydirname . '_block_list.html' : trim( $options[4] );
	$display_body   = empty( $options[5] ) ? false : true;

	if ( preg_match( '/[^0-9a-zA-Z_-]/', $mydirname ) ) {
		die( 'Invalid mydirname' );
	}

	require_once XOOPS_ROOT_PATH . '/class/template.php';

	$tpl = new XoopsTpl();

	$tpl->assign(
		[
			'mydirname'           => $mydirname,
			'categories'          => $categories,
			'categories_imploded' => implode( ',', $categories ),
			'order_options'       => b_pico_list_allowed_order(),
			'selected_order'      => $selected_order,
			'contents_num'        => $limit_offset,
			'this_template'       => $this_template,
			'display_body'        => $display_body,
		]
	);

	return $tpl->fetch( 'db:' . $mydirname . '_blockedit_list.html' );
}
