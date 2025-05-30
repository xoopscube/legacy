<?php
/**
 * Protector module for XCL
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL, PHP8.2
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

eval( ' function xoops_module_uninstall_' . $mydirname . '( $module ) { return protector_onuninstall_base( $module , \'' . $mydirname . '\' ) ; } ' );


if ( ! function_exists( 'protector_onuninstall_base' ) ) {
	function protector_onuninstall_base( $module, $mydirname ) {
		// transations on module uninstall

		global $ret; // TODO :-D

		// for Cube 2.1
		if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
			$root =& XCube_Root::getSingleton();
			$root->mDelegateManager->add( 'Legacy.Admin.Event.ModuleUninstall.' . ucfirst( $mydirname ) . '.Success', 'protector_message_append_onuninstall' );
			$ret = [];
		} else {
			if ( ! is_array( $ret ) ) {
				$ret = [];
			}
		}

		$db  =& Database::getInstance();
		$mid = $module->getVar( 'mid' );

		// TABLES (loading mysql.sql)
		$sql_file_path = __DIR__ . '/sql/mysql.sql';
		$prefix_mod    = $db->prefix() . '_' . $mydirname;
		if ( file_exists( $sql_file_path ) ) {
			$ret[]     = 'SQL file found at <b>' . htmlspecialchars( $sql_file_path ) . '</b>.<br > Deleting tables...<br>';
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

		// TEMPLATES (Not necessary because modulesadmin removes all templates)
		/* $tplfile_handler =& xoops_gethandler( 'tplfile' ) ;
		$templates =& $tplfile_handler->find( null , 'module' , $mid ) ;
		$tcount = count( $templates ) ;
		if( $tcount > 0 ) {
			$ret[] = 'Deleting templates...' ;
			for( $i = 0 ; $i < $tcount ; $i ++ ) {
				if( ! $tplfile_handler->delete( $templates[$i] ) ) {
					$ret[] = '<span style="color:#ff0000;">ERROR: Could not delete template '.$templates[$i]->getVar('tpl_file','s').' from the database. Template ID: <b>'.$templates[$i]->getVar('tpl_id','s').'</b></span><br>';
				} else {
					$ret[] = 'Template <b>'.$templates[$i]->getVar('tpl_file','s').'</b> deleted from the database. Template ID: <b>'.$templates[$i]->getVar('tpl_id','s').'</b><br>';
				}
			}
		}
		unset($templates); */


		return true;
	}

	function protector_message_append_onuninstall( &$module_obj, &$log ) {
		if ( is_array( @$GLOBALS['ret'] ) ) {
			foreach ( $GLOBALS['ret'] as $message ) {
				$log->add( strip_tags( $message ) );
			}
		}

		// use mLog->addWarning() or mLog->addError() if necessary
	}
}
