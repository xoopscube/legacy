<?php

eval( 'function ' . $mydirname . '_notify_iteminfo( $category, $item_id )
{return protector_notify_base( \'' . $mydirname . '\' , $category , $item_id ) ;}' );

if ( ! function_exists( 'protector_notify_base' ) ) {

	/**
	 * @param $mydirname
	 * @param $category
	 * @param $item_id
	 *
	 * @return mixed
	 */
	function protector_notify_base( $mydirname, $category, $item_id ) {
		$item = [];
  include_once __DIR__ . '/include/common_functions.php';

		$db =& Database::getInstance();

		$module_handler =& xoops_gethandler( 'module' );
		$module         =& $module_handler->getByDirname( $mydirname );

		if ( 'global' == $category ) {
			$item['name'] = '';
			$item['url']  = '';

			return $item;
		}
	}
}
