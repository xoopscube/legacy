<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * @package    Altsys
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */


eval( ' function xoops_module_install_' . $mydirname . '( $module ) { return altsys_oninstall_base( $module , "' . $mydirname . '" ) ; } ' );

if ( ! function_exists( 'altsys_oninstall_base' ) ) {
	/**
	 * @param $module
	 * @param $mydirname
	 *
	 * @return bool
	 */

	if ( ! function_exists( 'altsys_oninstall_base' ) ) {
		function altsys_oninstall_base( $module, $mydirname ) {
			$pieces = null;
   // transactions on module install

			global $ret; // TODO :-D

			// for Cube 2.1
			if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
				$root = XCube_Root::getSingleton();
				$root->mDelegateManager->add( 'Legacy.Admin.Event.ModuleInstall.' . ucfirst( $mydirname ) . '.Success', 'altsys_message_append_oninstall' );
				$ret = [];
			} else {
				if ( ! is_array( $ret ) ) {
					$ret = [];
				}
			}

			$db = XoopsDatabaseFactory::getDatabaseConnection();

			$mid = $module->getVar( 'mid' );

			// ALTSYS SPECIFIC PART
			// set weight=0
			$db->queryF( 'UPDATE ' . $db->prefix( 'modules' ) . " SET weight=0 WHERE mid=$mid" );


			// TABLES (loading mysql.sql)
			$sql_file_path = __DIR__ . '/sql/mysql.sql';
			$prefix_mod    = $db->prefix() . '_' . $mydirname;
			if ( file_exists( $sql_file_path ) ) {
				//$ret[] = 'SQL file found at <b>' . htmlspecialchars($sql_file_path) . '</b>.<br> Creating tables...';
				$ret[] = 'SQL file found at <b>' . htmlspecialchars( $sql_file_path, ENT_QUOTES | ENT_HTML5 ) . '</b>.<br> Creating tables...';

				if ( is_file( XOOPS_ROOT_PATH . '/class/database/oldsqlutility.php' ) ) {
					include_once XOOPS_ROOT_PATH . '/class/database/oldsqlutility.php';
					$sqlutil = new OldSqlUtility();
				} else {
					include_once XOOPS_ROOT_PATH . '/class/database/sqlutility.php';
					$sqlutil = new SqlUtility();
				}

				$sql_query = trim( file_get_contents( $sql_file_path ) );
				$sqlutil->splitMySqlFile( $pieces, $sql_query );
				$created_tables = [];
				foreach ( $pieces as $piece ) {
					$prefixed_query = $sqlutil->prefixQuery( $piece, $prefix_mod );
					if ( ! $prefixed_query ) {
						$ret[] = 'Invalid SQL <b>' . htmlspecialchars( $piece ) . '</b><br>';

						return false;
					}
					if ( ! $db->query( $prefixed_query[0] ) ) {
						$ret[] = '<b>' . htmlspecialchars( $db->error() ) . '</b><br>';

						//var_dump( $db->error() ) ;
						return false;
					} else if ( ! in_array( $prefixed_query[4], $created_tables ) ) {
						$ret[]            = 'Table <b>' . htmlspecialchars( $prefix_mod . '_' . $prefixed_query[4] ) . '</b> created.<br>';
						$created_tables[] = $prefixed_query[4];
					} else {
						$ret[] = 'Data inserted to table <b>' . htmlspecialchars( $prefix_mod . '_' . $prefixed_query[4] ) . '</b>.</br>';
					}
				}
			}

			// TEMPLATES
			$tplfile_handler = xoops_gethandler( 'tplfile' );
			$tpl_path        = __DIR__ . '/templates';
			if ( $handler = @opendir( $tpl_path . '/' ) ) {
				while ( false !== ( $file = readdir( $handler ) ) ) {
					if ( '.' == substr( $file, 0, 1 ) ) {
						continue;
					}
					$file_path = $tpl_path . '/' . $file;
					if ( is_file( $file_path ) ) {
						$mtime   = (int) @filemtime( $file_path );
						$tplfile = $tplfile_handler->create();
						$tplfile->setVar( 'tpl_source', file_get_contents( $file_path ), true );
						$tplfile->setVar( 'tpl_refid', $mid );
						$tplfile->setVar( 'tpl_tplset', 'default' );
						$tplfile->setVar( 'tpl_file', $mydirname . '_' . $file );
						$tplfile->setVar( 'tpl_desc', '', true );
						$tplfile->setVar( 'tpl_module', $mydirname );
						$tplfile->setVar( 'tpl_lastmodified', $mtime );
						$tplfile->setVar( 'tpl_lastimported', 0 );
						$tplfile->setVar( 'tpl_type', 'module' );
						if ( ! $tplfile_handler->insert( $tplfile ) ) {
							$ret[] = '<span style="color:#ff0000;">ERROR: Could not insert template <b>' . htmlspecialchars( $mydirname . '_' . $file ) . '</b> to the database.</span><br>';
						} else {
							$tplid = $tplfile->getVar( 'tpl_id' );
							$ret[] = 'Template <b>' . htmlspecialchars( $mydirname . '_' . $file ) . '</b> added to the database. (ID: <b>' . $tplid . '</b>)<br>';
							require_once XOOPS_TRUST_PATH . '/libs/altsys/include/altsys_functions.php';
							altsys_clear_templates_c();
						}
					}
				}
				closedir( $handler );
			}
			include_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
			include_once XOOPS_ROOT_PATH . '/class/template.php';
			xoops_template_clear_module_cache( $mid );

			return true;
		}

		function altsys_message_append_oninstall( &$module_obj, &$log ) {
			if ( is_array( @$GLOBALS['ret'] ) ) {
				foreach ( $GLOBALS['ret'] as $message ) {
					$log->add( strip_tags( $message ) );
				}
			}

			// use mLog->addWarning() or mLog->addError() if necessary
		}
	}
}
