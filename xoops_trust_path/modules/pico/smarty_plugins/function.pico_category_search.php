<?php

require_once XOOPS_TRUST_PATH.'/modules/pico/include/common_functions.php' ;
require_once XOOPS_TRUST_PATH.'/modules/pico/class/pico.textsanitizer.php' ;
require_once XOOPS_TRUST_PATH.'/modules/pico/class/PicoUriMapper.class.php' ;
require_once XOOPS_TRUST_PATH.'/modules/pico/class/PicoPermission.class.php' ;
require_once XOOPS_TRUST_PATH.'/modules/pico/class/PicoModelCategory.class.php' ;
require_once XOOPS_TRUST_PATH.'/modules/pico/class/PicoModelContent.class.php' ;

function smarty_function_pico_category_search( $params , &$smarty )
{
	$dir = @$params['dir'] . @$params['dirname'] ;
	$cat_title = @$params['title'] . @$params['cat_title'] ;
	$var_name = @$params['item'] . @$params['assign'] ;

	if( empty( $var_name ) ) {
		echo 'error '.__FUNCTION__.' [specify item]';
		return ;
	}

	if( empty( $dir ) ) $dir = $smarty->get_template_vars( 'mydirname' ) ;
	if( empty( $dir ) ) {
		echo 'error '.__FUNCTION__.' [specify dirname]';
		return ;
	}

	$mydirnames = explode( ',' , $dir ) ;

	$db =& Database::getInstance() ;
	$myts =& MyTextSanitizer::getInstance() ;
	$module_handler =& xoops_gethandler('module');
	$config_handler =& xoops_gethandler('config');

	$categories4assign = array() ;
	foreach( $mydirnames as $mydirname ) {

		$module =& $module_handler->getByDirname($mydirname);
		$configs = $config_handler->getConfigList( $module->getVar('mid') ) ;

		$sql = "SELECT * FROM ".$db->prefix($mydirname."_categories")." c WHERE c.cat_title='".mysql_real_escape_string($cat_title)."'" ;
		$result = $db->query( $sql ) ;
		while( $cat_row = $db->fetchArray( $result ) ) {
			$category4assign = array(
				'mod_mid' => $module->getVar('mid') ,
				'mod_dirname' => $mydirname ,
				'mod_name' => $module->getVar('name') ,
				'id' => intval( $cat_row['cat_id'] ) ,
				'link' => pico_common_make_category_link4html( $configs , $cat_row ) ,
				'title' => $myts->makeTboxData4Show( $cat_row['cat_title'] ) ,
				'desc' => $myts->displayTarea( $cat_row['cat_desc'] , 1 ) ,
				'paths_raw' => pico_common_unserialize( $cat_row['cat_path_in_tree'] ) ,
				'paths_value' => array_values( pico_common_unserialize( $cat_row['cat_path_in_tree'] ) ) ,
				'redundants' => pico_common_unserialize( $cat_row['cat_redundants'] ) ,
			) + $cat_row ;
	
			$categories4assign[] = $category4assign ;
		}
	}

	$smarty->assign( $var_name , $categories4assign ) ;
}

?>