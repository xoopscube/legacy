<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

require_once XOOPS_TRUST_PATH . '/modules/pico/include/common_functions.php';
require_once XOOPS_TRUST_PATH . '/modules/pico/class/PicoTextSanitizer.class.php';
require_once XOOPS_TRUST_PATH . '/modules/pico/class/PicoUriMapper.class.php';
require_once XOOPS_TRUST_PATH . '/modules/pico/class/PicoPermission.class.php';
require_once XOOPS_TRUST_PATH . '/modules/pico/class/PicoModelCategory.class.php';
require_once XOOPS_TRUST_PATH . '/modules/pico/class/PicoModelContent.class.php';
require_once XOOPS_TRUST_PATH . '/modules/pico/blocks/list.php';

function smarty_function_pico_list( $params, &$smarty ) {
	$mydirname = @$params['dir'] . @$params['dirname'];
	$cat_ids = @$params['id'] . @$params['cat_id'];
	$order = empty( $params['order'] ) ? 'o.created_time DESC' : $params['order'];
	$limit_params = @$params['limit'];
	$template = @$params['template'];
	$var_name = @$params['item'] . @$params['assign'];

	if ( empty( $mydirname ) ) {
		$mydirname = $smarty->get_template_vars( 'mydirname' );
	}
	if ( empty( $mydirname ) ) {
		echo 'error ' . __FUNCTION__ . ' [specify dirname]';

		return;
	}

	require_once XOOPS_ROOT_PATH . '/modules/' . $mydirname . '/blocks/blocks.php';

	if ( $var_name ) {
		// just assign
		$assigns = b_pico_list_show( [
			$mydirname,
			$cat_ids,
			$order,
			$limit_params,
			$template,
			'disable_renderer' => true
		] );
		$smarty->assign( $var_name, $assigns );
	} else {
		// display
		$block = b_pico_list_show( [ $mydirname, $cat_ids, $order, $limit_params, $template ] );
		echo @$block['content'];
	}
}
