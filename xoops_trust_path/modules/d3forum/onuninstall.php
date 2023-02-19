<?php
/**
 * D3Forum module for XCL
 *
 * @package    D3Forum
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

eval( ' function xoops_module_uninstall_' . $mydirname . '( $module ) { return d3forum_onuninstall_base( $module , \'' . $mydirname . '\' ) ; } ' );

if ( ! function_exists( 'd3forum_onuninstall_base' ) ) {

	function d3forum_onuninstall_base( $module, $mydirname ) {
		// transations on module uninstall

		global $ret; // TODO :-D

		// for Cube 2.1
		if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {

			$root = XCube_Root::getSingleton();

			$root->mDelegateManager->add( 'Legacy.Admin.Event.ModuleUninstall.' . ucfirst( $mydirname ) . '.Success', 'd3forum_message_append_onuninstall' );

			$ret = [];

		} else {
			if ( ! is_array( $ret ) ) {
				$ret = [];
			}
		}

		$db = XoopsDatabaseFactory::getDatabaseConnection();

		$mid = $module->getVar( 'mid' );

		// TABLES (loading mysql.sql)
		$sql_file_path = __DIR__ . '/sql/mysql.sql';

		$prefix_mod = $db->prefix() . '_' . $mydirname;

		if ( file_exists( $sql_file_path ) ) {

			$ret[] = 'SQL file found at <b>' . htmlspecialchars( $sql_file_path ) . '</b>.<br> Deleting tables...<br>';

			$sql_lines = file( $sql_file_path );

			foreach ( $sql_lines as $sql_line ) {
				if ( preg_match( '/^CREATE TABLE \`?([a-zA-Z0-9_-]+)\`? /i', $sql_line, $regs ) ) {
					$sql = 'DROP TABLE ' . addslashes( $prefix_mod . '_' . $regs[1] );
					if ( ! $db->query( $sql ) ) {
						$ret[] = '<span style="color:#ff0000;">ERROR: Could not drop table <b>' . htmlspecialchars( $prefix_mod . '_' . $regs[1] ) . '<b>.</span><br>';
					} else {
						$ret[] = 'Table <b>' . htmlspecialchars( $prefix_mod . '_' . $regs[1] ) . '</b> dropped.<br>';
					}
				}
			}
		}

		return true;
	}

	function d3forum_message_append_onuninstall( &$module_obj, &$log ) {
		if ( is_array( @$GLOBALS['ret'] ) ) {
			foreach ( $GLOBALS['ret'] as $message ) {
				$log->add( strip_tags( $message ) );
			}
		}

		// use mLog->addWarning() or mLog->addError() if necessary
	}

}
