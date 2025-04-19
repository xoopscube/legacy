<?php
/**
 * Protector module for XCL
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka Gigamaster, 2020 XCL, PHP8.2
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

eval( ' function xoops_module_install_' . $mydirname . '( $module ) { return protector_oninstall_base( $module , \'' . $mydirname . '\' ) ; } ' );


if ( ! function_exists( 'protector_oninstall_base' ) ) {
  function protector_oninstall_base( $module, $mydirname ) {
    $pieces = null;
  // transations on module install

    global $ret; // TODO :-D

    // for Cube 2.1
    if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
      $root =& XCube_Root::getSingleton();
      $root->mDelegateManager->add( 'Legacy.Admin.Event.ModuleInstall.' . ucfirst( $mydirname ) . '.Success', 'protector_message_append_oninstall' );
      $ret = [];
    } else {
      if ( ! is_array( $ret ) ) {
        $ret = [];
      }
    }

    $db =& XoopsDatabaseFactory::getDatabaseConnection();

    $mid = $module->getVar( 'mid' );

        // Scan for available proxy plugins and update config options
        protector_update_proxy_plugin_options($mid);

    // TABLES (loading mysql.sql)
    $sql_file_path = __DIR__ . '/sql/mysql.sql';
    $prefix_mod    = $db->prefix() . '_' . $mydirname;

    if ( file_exists( $sql_file_path ) ) {
      $ret[] = 'SQL file found at <b>' . htmlspecialchars( $sql_file_path ) . '</b>.<br> Creating tables...';

        include_once XOOPS_ROOT_PATH . '/class/database/sqlutility.php';
        $sqlutil = new SqlUtility();

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
        } else {
          if ( ! in_array( $prefixed_query[4], $created_tables ) ) {
            $ret[]            = 'Table <b>' . htmlspecialchars( $prefix_mod . '_' . $prefixed_query[4] ) . '</b> created.<br>';
            $created_tables[] = $prefixed_query[4];
          } else {
            $ret[] = 'Data inserted to table <b>' . htmlspecialchars( $prefix_mod . '_' . $prefixed_query[4] ) . '</b>.</br>';
          }
        }
      }
    }

    // TEMPLATES
    $tplfile_handler =& xoops_gethandler( 'tplfile' );
    $tpl_path        = __DIR__ . '/templates';
    if ( $handler = @opendir( $tpl_path . '/' ) ) {
      while ( false !== ( $file = readdir( $handler ) ) ) {
        if ( '.' == substr( $file, 0, 1 ) ) {
          continue;
        }
        $file_path = $tpl_path . '/' . $file;
        if ( is_file( $file_path ) && in_array( strrchr( $file, '.' ), [ '.html', '.css', '.js' ] ) ) {
          $mtime   = (int) @filemtime( $file_path );
          $tplfile =& $tplfile_handler->create();
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
            // generate compiled file
            include_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
            include_once XOOPS_ROOT_PATH . '/class/template.php';
            if ( ! xoops_template_touch( $tplid ) ) {
              $ret[] = '<span style="color:#ff0000;">ERROR: Failed compiling template <b>' . htmlspecialchars( $mydirname . '_' . $file ) . '</b>.</span><br>';
            } else {
              $ret[] = 'Template <b>' . htmlspecialchars( $mydirname . '_' . $file ) . '</b> compiled.</span><br>';
            }
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

    // New function to scan for proxy plugins and update config options
    function protector_update_proxy_plugin_options($mid) {
        global $ret;

        // Get available plugins
        $plugins_dir = XOOPS_TRUST_PATH . '/modules/protector/plugins/proxy';
        $available_plugins = [];

        // Check if plugins directory exists
        if (!is_dir($plugins_dir)) {
            $ret[] = '<span style="color:#ff0000;">WARNING: Proxy plugins directory not found at: ' . htmlspecialchars($plugins_dir) . '</span>';

            // Try to create the directory
            if (@mkdir($plugins_dir, 0755, true)) {
                $ret[] = 'Created proxy plugins directory at: ' . htmlspecialchars($plugins_dir);

                // Create an index.php file for security
                $index_content = "<?php\nheader('HTTP/1.0 403 Forbidden');\nexit('Access Denied');\n";
                if (file_put_contents($plugins_dir . '/index.php', $index_content)) {
                    $ret[] = 'Created security index.php in plugins directory';
                } else {
                    $ret[] = '<span style="color:#ff0000;">WARNING: Failed to create security index.php in plugins directory</span>';
                }
            } else {
                $ret[] = '<span style="color:#ff0000;">ERROR: Failed to create proxy plugins directory. Please create it manually and ensure it is writable.</span>';
                return;
            }
        }

        if (is_dir($plugins_dir)) {
            $ret[] = 'Scanning for proxy plugins in: ' . htmlspecialchars($plugins_dir);

            // Check if directory is readable
            if (!is_readable($plugins_dir)) {
                $ret[] = '<span style="color:#ff0000;">ERROR: Proxy plugins directory is not readable. Please check permissions.</span>';
                return;
            }

            $dir = @opendir($plugins_dir);
            if (!$dir) {
                $ret[] = '<span style="color:#ff0000;">ERROR: Failed to open proxy plugins directory for reading.</span>';
                return;
            }

            while (($file = readdir($dir)) !== false) {
                if (substr($file, -4) === '.php') {
                    $plugin_name = substr($file, 0, -4);

                    // Skip index.php and only include files with Plugin_ prefix
                    if ($plugin_name === 'index' || strpos($plugin_name, 'Plugin_') !== 0) {
                        continue;
                    }

                    $available_plugins[$plugin_name] = $plugin_name;
                    $ret[] = 'Found plugin: ' . htmlspecialchars($plugin_name);
                }
            }
            closedir($dir);
        }

        // Update the module config options
        if (!empty($available_plugins)) {
            try {
                $db = XoopsDatabaseFactory::getDatabaseConnection(); // Ensure DB is available if needed later, though not directly used here
                $config_handler = xoops_gethandler('config');

                // Find the proxy_plugins_enabled config
                $criteria = new CriteriaCompo();
                $criteria->add(new Criteria('conf_modid', $mid));
                $criteria->add(new Criteria('conf_name', 'proxy_plugins_enabled'));
                $configs = $config_handler->getConfigs($criteria);

                if (count($configs) > 0) {
                    $config = $configs[0]; // Get the XoopsConfig object

                    // --- Start Fix ---
                    $new_options = []; // Array to hold the new option objects

                    // Create new option objects
                    foreach ($available_plugins as $key => $value) {
                        $option = $config_handler->createConfigOption();
                        $option->setVar('confop_name', $value); // The text displayed
                        $option->setVar('confop_value', $key);  // The value stored
                        $new_options[] = $option; // Add the object to the array
                        // Remove the incorrect line: $config->addOption($option);
                    }

                    // Set the entire array of new options
                    $config->setConfOptions($new_options);
                    // --- End Fix ---


                    // Save the updated config object (which now contains the new options)
                    if ($config_handler->insertConfig($config)) {
                        $ret[] = 'Updated proxy_plugins_enabled options with ' . count($available_plugins) . ' plugins.';
                    } else {
                        $ret[] = '<span style="color:#ff0000;">ERROR: Failed to update proxy_plugins_enabled options.</span>';
                    }
                } else {
                    $ret[] = '<span style="color:#ff0000;">WARNING: Config proxy_plugins_enabled not found. Plugin selection may not work correctly.</span>';
                }
            } catch (Exception $e) {
                $ret[] = '<span style="color:#ff0000;">ERROR: Exception while updating plugin options: ' . $e->getMessage() . '</span>';
            }
        } else {
            $ret[] = 'No proxy plugins found. You can add plugins to: ' . htmlspecialchars($plugins_dir);
        }
    }


  function protector_message_append_oninstall( &$module_obj, &$log ) {
    if ( is_array( @$GLOBALS['ret'] ) ) {
      foreach ( $GLOBALS['ret'] as $message ) {
        $log->add( strip_tags( $message ) );
      }
    }

    // use mLog->addWarning() or mLog->addError() if necessary
  }
}


// Update proxy plugin options
function updateProxyPluginOptions($module)
{
    $config_handler = xoops_getHandler('config');

    // Get the proxy_plugins_enabled config
    $plugin_config = $config_handler->getConfigsByCriteria(
        new CriteriaCompo([
            new Criteria('conf_modid', $module->getVar('mid')),
            new Criteria('conf_name', 'proxy_plugins_enabled')
        ])
    );

    if (!empty($plugin_config) && count($plugin_config) === 1) {
        $plugin_config_obj = $plugin_config[0];
        $plugin_options = [];

        // Get available plugins
        $plugins_dir = XOOPS_TRUST_PATH . '/modules/protector/plugins/proxy';
        if (is_dir($plugins_dir)) {
            $dir = opendir($plugins_dir);
            while (($file = readdir($dir)) !== false) {
                if (substr($file, -4) === '.php') {
                    $plugin_name = substr($file, 0, -4);
                    // Skip index.php and only include files with Plugin_ prefix
                    if ($plugin_name === 'index' || strpos($plugin_name, 'Plugin_') !== 0) {
                        continue;
                    }
                    $plugin_options[$plugin_name] = $plugin_name;
                }
            }
            closedir($dir);

            // Update the options using the correct method
            $new_options = [];
            foreach ($plugin_options as $key => $value) {
                $option = $config_handler->createConfigOption();
                $option->setVar('confop_name', $value);
                $option->setVar('confop_value', $key);
                $new_options[] = $option;
            }
            $plugin_config_obj->setConfOptions($new_options); // Use setConfOptions

            // Save the config item
            $config_handler->insertConfig($plugin_config_obj);
        }
    }
}

// Call the function during installation
if (isset($module) && is_object($module)) {
    updateProxyPluginOptions($module);
}

// Include the permissions update file and update permissions
if (!function_exists('protector_update_permissions')) {
    require_once __DIR__ . '/include/updateperms.inc.php';
}
protector_update_permissions($mydirname);
