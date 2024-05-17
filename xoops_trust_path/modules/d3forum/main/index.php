<?php
/**
 * D3Forum module for XCL
 *
 * @package    D3Forum
 * @version    XCL 2.4.0
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Author
 * @license    GPL v2.0
 */

include dirname( __DIR__ ) . '/include/common_prepend.php';

// branches (TODO viewallforum)
if ( ! empty( $_GET['post_id'] ) ) {
	include dirname( __DIR__ ) . '/include/viewpost.php';
} else if ( ! empty( $_GET['topic_id'] ) ) {
	include dirname( __DIR__ ) . '/include/listposts.php';
} else if ( ! empty( $_GET['forum_id'] ) ) {
	include dirname( __DIR__ ) . '/include/listtopics.php';
} else if ( ! empty( $_GET['cat_id'] ) ) {
	include dirname( __DIR__ ) . '/include/listforums.php';
} else if ( isset( $_GET['cat_ids'] ) ) {
	include dirname( __DIR__ ) . '/include/listtopics_over_categories.php';
} else {
	include dirname( __DIR__ ) . '/include/listcategories.php';
}

// form elements or javascripts for anti-SPAM
if ( d3forum_common_is_necessary_antispam( $xoopsUser, $xoopsModuleConfig ) ) {
	$antispam_obj    = d3forum_common_get_antispam_object( $xoopsModuleConfig );
	$antispam4assign = $antispam_obj->getHtml4Assign();
} else {
	$antispam4assign = [];
}

$xoopsTpl->assign(
	[
		'mydirname'             => $mydirname,
		'mod_url'               => XOOPS_URL . '/modules/' . $mydirname,
		'mod_imageurl'          => XOOPS_URL . '/modules/' . $mydirname . '/' . $xoopsModuleConfig['images_dir'],
		'mod_config'            => $xoopsModuleConfig,
		'xoops_config'          => $xoopsConfig,
		'uid'                   => $uid,
		'postorder'             => $postorder,
		'icon_meanings'         => $d3forum_icon_meanings,
		'antispam'              => $antispam4assign,
		'forum_jumpbox_options' => d3forum_make_jumpbox_options( $mydirname, $whr_read4cat, $whr_read4forum, @$forum_row['forum_id'] ),
		'xoops_module_header'   => '<link rel="stylesheet" type="text/css" media="all" href="' . str_replace( '{mod_url}', XOOPS_URL . '/modules/' . $mydirname, $xoopsModuleConfig['css_uri'] ) . '">' . $xoopsTpl->get_template_vars( 'xoops_module_header' ),
	]
);

// display
// For XCL 2.2 Call addMeta //nao-pon
if ( $d3forum_meta_description ) {

	if ( defined( 'LEGACY_MODULE_VERSION' ) && version_compare( LEGACY_MODULE_VERSION, '2.2', '>=' ) ) {

		$xclRoot = XCube_Root::getSingleton();

		$headerScript = $xclRoot->mContext->getAttribute( 'headerScript' );

		$headerScript->addMeta( 'description', $d3forum_meta_description );

	} elseif ( isset( $xoTheme ) && is_object( $xoTheme ) ) {

		$xoTheme->addMeta( 'meta', 'description', $d3forum_meta_description );
	}
}

include XOOPS_ROOT_PATH . '/footer.php';
