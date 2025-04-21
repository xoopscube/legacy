<?php
/**
 * Protector module update functions
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2025 The XOOPSCube Project
 * @license    GPL v2.0
 */

// Include necessary files
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

// Define the update function that will be called by the system
if (!function_exists('xoops_module_update_protector')) {
    
    function xoops_module_update_protector($module, $prev_version = null)
    {
        // Initialize messages array
        global $msgs;
        if (!is_array($msgs)) {
            $msgs = [];
        }
        
        // For Cube 2.1
        if (defined('XOOPS_CUBE_LEGACY')) {
            $root = XCube_Root::getSingleton();
            $root->mDelegateManager->add('Legacy.Admin.Event.ModuleUpdate.Protector.Success', 'protector_message_append_onupdate');
        }
        
        $mid = $module->getVar('mid');
        
        // Get the module dirname
        $mydirname = $module->getVar('dirname');
        
        // Scan for available proxy plugins and update config options
        protector_update_proxy_plugin_options($mid);
        
        // Ensure the module has notification capability
        $module_handler = xoops_gethandler('module');
        $module_obj = $module_handler->get($mid);
        
        if (is_object($module_obj)) {
            // Set hasnotification to 1
            $module_obj->setVar('hasnotification', 1);
            if ($module_handler->insert($module_obj)) {
                $msgs[] = "Notification capability enabled for Protector module.";
            } else {
                $msgs[] = "Failed to enable notification capability for Protector module.";
            }
        }
        
		// TEMPLATES (all templates have been already removed by modulesadmin)
		$tplfile_handler = xoops_gethandler( 'tplfile' );

		$tpl_path = __DIR__ . '/templates';

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
						$msgs[] = '<span style="color:#ff0000;">ERROR: Could not insert template <b>' . htmlspecialchars( $mydirname . '_' . $file ) . '</b> to the database.</span>';
					} else {
						$tplid = $tplfile->getVar( 'tpl_id' );

						$msgs[] = 'Template <b>' . htmlspecialchars( $mydirname . '_' . $file ) . '</b> added to the database. (ID: <b>' . $tplid . '</b>)';
						// generate compiled file
						include_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';

						include_once XOOPS_ROOT_PATH . '/class/template.php';

						if ( ! xoops_template_touch( $tplid ) ) {
							$msgs[] = '<span style="color:#ff0000;">ERROR: Failed compiling template <b>' . htmlspecialchars( $mydirname . '_' . $file ) . '</b>.</span>';
						} else {
							$msgs[] = 'Template <b>' . htmlspecialchars( $mydirname . '_' . $file ) . '</b> compiled.</span>';
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
    
    // Function to scan for proxy plugins and update config options
    function protector_update_proxy_plugin_options($mid) {
        global $msgs;
        
        // Get available plugins
        $plugins_dir = XOOPS_TRUST_PATH . '/modules/protector/plugins/proxy';
        $available_plugins = [];
        
        // Check if plugins directory exists
        if (!is_dir($plugins_dir)) {
            $msgs[] = '<span style="color:#ff0000;">WARNING: Proxy plugins directory not found at: ' . $plugins_dir . '</span>';
            
            // Try to create the directory
            if (@mkdir($plugins_dir, 0755, true)) {
                $msgs[] = 'Created proxy plugins directory at: ' . $plugins_dir;
                
                // Create an index.php file for security
                $index_content = "<?php\nheader('HTTP/1.0 403 Forbidden');\nexit('Access Denied');\n";
                if (file_put_contents($plugins_dir . '/index.php', $index_content)) {
                    $msgs[] = 'Created security index.php in plugins directory';
                } else {
                    $msgs[] = '<span style="color:#ff0000;">WARNING: Failed to create security index.php in plugins directory</span>';
                }
            } else {
                $msgs[] = '<span style="color:#ff0000;">ERROR: Failed to create proxy plugins directory. Please create it manually and ensure it is writable.</span>';
                return;
            }
        }
        
        if (is_dir($plugins_dir)) {
            $msgs[] = 'Scanning for proxy plugins in: ' . $plugins_dir;
            
            // Check if directory is readable
            if (!is_readable($plugins_dir)) {
                $msgs[] = '<span style="color:#ff0000;">ERROR: Proxy plugins directory is not readable. Please check permissions.</span>';
                return;
            }
            
            $dir = @opendir($plugins_dir);
            if (!$dir) {
                $msgs[] = '<span style="color:#ff0000;">ERROR: Failed to open proxy plugins directory for reading.</span>';
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
                    $msgs[] = 'Found plugin: ' . $plugin_name;
                }
            }
            closedir($dir);
        }
        
        // Update the module config options
        if (!empty($available_plugins)) {
            try {
                $db = XoopsDatabaseFactory::getDatabaseConnection();
                
                // Find the proxy_plugins_enabled config
                $sql = "SELECT conf_id FROM " . $db->prefix('config') . " 
                       WHERE conf_name = 'proxy_plugins_enabled' 
                       AND conf_modid = " . $mid;
                $result = $db->query($sql);
                
                if ($result && $db->getRowsNum($result) > 0) {
                    $row = $db->fetchArray($result);
                    $conf_id = $row['conf_id'];
                    
                    // Clear existing options
                    $db->queryF("DELETE FROM " . $db->prefix('configoption') . " 
                               WHERE conf_id = " . $conf_id);
                    
                    // Add new options
                    foreach ($available_plugins as $key => $value) {
                        $sql = "INSERT INTO " . $db->prefix('configoption') . " 
                               (confop_name, confop_value, conf_id) 
                               VALUES (" . $db->quoteString($value) . ", " . $db->quoteString($key) . ", $conf_id)";
                        if (!$db->queryF($sql)) {
                            $msgs[] = '<span style="color:#ff0000;">ERROR: Failed to insert option for plugin: ' . $key . '</span>';
                        }
                    }
                    
                    $msgs[] = 'Updated proxy_plugins_enabled options with ' . count($available_plugins) . ' plugins.';
                } else {
                    $msgs[] = '<span style="color:#ff0000;">WARNING: Config proxy_plugins_enabled not found. Plugin selection may not work correctly.</span>';
                }
            } catch (Exception $e) {
                $msgs[] = '<span style="color:#ff0000;">ERROR: Exception while updating plugin options: ' . $e->getMessage() . '</span>';
            }
        } else {
            $msgs[] = 'No proxy plugins found. You can add plugins to: ' . $plugins_dir;
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
                        if ($plugin_name !== 'index') {
                            $plugin_options[$plugin_name] = $plugin_name;
                        }
                    }
                }
                closedir($dir);
                
                // Update the options
                $plugin_config_obj->setVar('conf_options', serialize($plugin_options));
                $config_handler->insertConfig($plugin_config_obj);
            }
        }
    }
    
    // Call the function during update
    if (isset($module) && is_object($module)) {
        updateProxyPluginOptions($module);
    }
}

// Function to append messages to the update log
if (!function_exists('protector_message_append_onupdate')) {
    function protector_message_append_onupdate(&$module_obj, &$log)
    {
        if (is_array(@$GLOBALS['msgs'])) {
            foreach ($GLOBALS['msgs'] as $message) {
                $log->add(strip_tags($message));
            }
        }
    }
}

// Include the permissions update file and update permissions
if (!function_exists('protector_update_permissions')) {
    require_once __DIR__ . '/include/updateperms.inc.php';
}
protector_update_permissions($mydirname);
