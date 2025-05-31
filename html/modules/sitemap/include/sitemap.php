<?php
/**
 * Sitemap blocks
 * Automated Sitemap and XML file for search engines
 * @package    Sitemap
 * @version    2.5.0
 * @author     gigamaster, 2020 XCL/PHP7
 * @author     Ryuji
 * @author     chanoir
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';

function sitemap_show()
{
    global $xoopsUser, $xoopsConfig, $sitemap_configs;
    $myts = MyTextSanitizer::getInstance();
    $block = ['modules' => []];
    $plugin_dir = XOOPS_ROOT_PATH . "/modules/sitemap/plugins/";

    $default_changefreq = $sitemap_configs['default_changefreq'] ?? 'weekly';
    $default_priority = $sitemap_configs['default_priority'] ?? '0.5';
    $show_module_sublinks_config = !empty($sitemap_configs['show_module_sublinks']);
    $show_subcategories_config = !empty($sitemap_configs['show_subcategoris']);

    $system_modules_to_always_exclude = ['legacy', 'legacyRender', 'bannerstats', 'user', 'profile', 'protector', 'system', 'message'];

    $invisible_weights = [];
    if (isset($sitemap_configs['invisible_weights']) && trim($sitemap_configs['invisible_weights']) !== '') {
        $invisible_weights = explode(',', $sitemap_configs['invisible_weights']);
    }
    $invisible_dirnames_from_config = [];
    if (isset($sitemap_configs['invisible_dirnames']) && trim($sitemap_configs['invisible_dirnames']) !== '') {
        $invisible_dirnames_from_config = explode(',', str_replace(' ', '', $sitemap_configs['invisible_dirnames']));
    }

    $module_handler = xoops_gethandler('module');
    $criteria = new CriteriaCompo(new Criteria('hasmain', 1));
    $criteria->add(new Criteria('isactive', 1));
    $modules = $module_handler->getObjects($criteria, true);
    $moduleperm_handler = xoops_gethandler('groupperm');
    $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
    $read_allowed = $moduleperm_handler->getItemIds('module_read', $groups);

    foreach (array_keys($modules) as $i) {
        $module = $modules[$i];
        $mod_dirname = $module->getVar('dirname');
        $module_id = $module->getVar('mid');

        if ($mod_dirname === 'sitemap' ||
            in_array($mod_dirname, $system_modules_to_always_exclude, true) ||
            !in_array($module_id, $read_allowed) ||
            in_array($module->getVar('weight'), $invisible_weights) ||
            in_array($mod_dirname, $invisible_dirnames_from_config, true)) {
            continue;
        }

        $module_base_url = XOOPS_URL . '/modules/' . $mod_dirname . '/';
        $current_module_sitemap_data = [
            'id' => $module_id,
            'name' => $module->getVar('name'),
            'directory' => $mod_dirname,
            'sublinks' => [],
            'parent' => []
        ];

        if ($show_module_sublinks_config) {
            $sublinks_raw = $module->subLink();
            if (is_array($sublinks_raw)) {
                foreach ($sublinks_raw as $sublink_raw_item) {
                    if (is_array($sublink_raw_item) && isset($sublink_raw_item['name']) && isset($sublink_raw_item['url'])) {
                        $sublink_url_relative = trim($sublink_raw_item['url']);
                        if (empty($sublink_url_relative)) continue;

                        $sublink_loc_abs = $module_base_url . ltrim($sublink_url_relative, '/');
                        $escaped_sublink_loc_abs = htmlspecialchars($sublink_loc_abs, ENT_XML1);
                        $current_module_sitemap_data['sublinks'][] = [
                            'name'       => $myts->makeTboxData4Show($sublink_raw_item['name']),
                            'url'        => $escaped_sublink_loc_abs, // For HTML template
                            'loc'        => $escaped_sublink_loc_abs, // For XML template
                            'lastmod'    => null,
                            'changefreq' => $default_changefreq,
                            'priority'   => $default_priority
                        ];
                    }
                }
            }
        }

        $plugin_data_from_module = null;
        $mydirname = $mod_dirname;
        $mytrustdirname = '';
        $trust_path_file = XOOPS_ROOT_PATH . "/modules/" . $mydirname . "/mytrustdirname.php";
        if (defined('XOOPS_TRUST_PATH') && file_exists($trust_path_file)) {
            @include $trust_path_file; 
        }
        
        $plugin_function_name_base = !empty($mytrustdirname) ? $mytrustdirname : $mod_dirname;
        $plugin_file_loaded = false;

        $plugin_paths_to_try = [
            XOOPS_ROOT_PATH . "/modules/" . $mod_dirname . "/include/sitemap.plugin.php",
            (!empty($mytrustdirname) ? XOOPS_TRUST_PATH . "/modules/" . $mytrustdirname . "/include/sitemap.plugin.php" : null),
            $plugin_dir . $mod_dirname . ".php"
        ];
        
        foreach ($plugin_paths_to_try as $idx => $mod_plugin_file_path) {
            if ($mod_plugin_file_path === null || !file_exists($mod_plugin_file_path)) {
                continue;
            }

            require_once $mod_plugin_file_path;
            if (function_exists("b_sitemap_" . $plugin_function_name_base)) {
                $plugin_data_from_module = call_user_func("b_sitemap_" . $plugin_function_name_base, $mydirname);
                $plugin_file_loaded = true;
            }
            break; 
        }

        $processed_parent_locs = [];

        if (isset($plugin_data_from_module["parent"]) && is_array($plugin_data_from_module["parent"])) {
            foreach ($plugin_data_from_module["parent"] as $plugin_item) {
                $item_name_to_use = null;
                $item_link_to_use = null;

                if (is_array($plugin_item)) {
                    if (!empty($plugin_item['name'])) { $item_name_to_use = $plugin_item['name']; }
                    elseif (!empty($plugin_item['title'])) { $item_name_to_use = $plugin_item['title']; }

                    if (!empty($plugin_item['link'])) { $item_link_to_use = $plugin_item['link']; }
                    elseif (!empty($plugin_item['url'])) { $item_link_to_use = $plugin_item['url']; }
                }

                $item_link_relative_trimmed = trim((string)$item_link_to_use);

                if (!is_array($plugin_item) || empty($item_link_relative_trimmed) || !isset($item_name_to_use)) {
                    error_log("Sitemap: Module '" . $mod_dirname . "' plugin item skipped (empty link/name after mapping/trim): " . print_r($plugin_item, true));
                    continue; 
                }

                $item_loc_abs = '';
                if (strpos($item_link_relative_trimmed, '://') !== false) { $item_loc_abs = $item_link_relative_trimmed; }
                elseif (substr($item_link_relative_trimmed, 0, 1) === '/') { $item_loc_abs = XOOPS_URL . $item_link_relative_trimmed; }
                else { $item_loc_abs = $module_base_url . ltrim($item_link_relative_trimmed, '/'); }

                $escaped_item_loc_abs = htmlspecialchars($item_loc_abs, ENT_XML1);

                if (in_array($escaped_item_loc_abs, $processed_parent_locs)) {
                    error_log("Sitemap: Module '" . $mod_dirname . "' plugin returned duplicate parent loc, skipping: " . $escaped_item_loc_abs);
                    continue;
                }
                $processed_parent_locs[] = $escaped_item_loc_abs;

                $parent_entry = [
                    'url'        => $escaped_item_loc_abs, // For HTML template
                    'loc'        => $escaped_item_loc_abs, // For XML template
                    'title'      => $myts->makeTboxData4Show($item_name_to_use),
                    'lastmod'    => isset($plugin_item['time']) ? gmdate('Y-m-d\TH:i:s\Z', (int)$plugin_item['time']) : null,
                    'changefreq' => $plugin_item['changefreq'] ?? $default_changefreq,
                    'priority'   => $plugin_item['priority'] ?? $default_priority,
                    'child'      => []
                ];

                if (isset($plugin_item['child']) && is_array($plugin_item['child'])) {
                    foreach ($plugin_item['child'] as $plugin_sub_item) {
                        $sub_item_name_to_use = null;
                        $sub_item_link_to_use = null;
                        if(is_array($plugin_sub_item)) {
                            if (!empty($plugin_sub_item['name'])) { $sub_item_name_to_use = $plugin_sub_item['name']; }
                            elseif (!empty($plugin_sub_item['title'])) { $sub_item_name_to_use = $plugin_sub_item['title']; }

                            if (!empty($plugin_sub_item['link'])) { $sub_item_link_to_use = $plugin_sub_item['link']; }
                            elseif (!empty($plugin_sub_item['url'])) { $sub_item_link_to_use = $plugin_sub_item['url']; }
                        }
                        
                        $sub_item_link_relative_trimmed = trim((string)$sub_item_link_to_use);

                        if (!is_array($plugin_sub_item) || empty($sub_item_link_relative_trimmed) || !isset($sub_item_name_to_use)) {
                            error_log("Sitemap: Module '" . $mod_dirname . "' plugin returned malformed child item (empty link/name after mapping/trim): " . print_r($plugin_sub_item, true));
                            continue; 
                        }
                        $sub_item_loc_abs = '';
                        if (strpos($sub_item_link_relative_trimmed, '://') !== false) { $sub_item_loc_abs = $sub_item_link_relative_trimmed; }
                        elseif (substr($sub_item_link_relative_trimmed, 0, 1) === '/') { $sub_item_loc_abs = XOOPS_URL . $sub_item_link_relative_trimmed; }
                        else { $sub_item_loc_abs = $module_base_url . ltrim($sub_item_link_relative_trimmed, '/'); }
                        
                        $escaped_sub_item_loc_abs = htmlspecialchars($sub_item_loc_abs, ENT_XML1);

                        $parent_entry['child'][] = [
                            'url'        => $escaped_sub_item_loc_abs, 
                            'loc'        => $escaped_sub_item_loc_abs, 
                            'title'      => $myts->makeTboxData4Show($sub_item_name_to_use),
                            'lastmod'    => isset($plugin_sub_item['time']) ? gmdate('Y-m-d\TH:i:s\Z', (int)$plugin_sub_item['time']) : null,
                            'changefreq' => $plugin_sub_item['changefreq'] ?? $default_changefreq,
                            'priority'   => $plugin_sub_item['priority'] ?? $default_priority
                        ];
                    }
                }
                $current_module_sitemap_data['parent'][] = $parent_entry;
            }
        } elseif (!$plugin_file_loaded && $module->getVar('hasmain') == 1 && !in_array($mod_dirname, $system_modules_to_always_exclude, true) ) {
            $fallback_loc_abs = $module_base_url . 'index.php';
            $escaped_fallback_loc_abs = htmlspecialchars($fallback_loc_abs, ENT_XML1);
            if (!in_array($escaped_fallback_loc_abs, $processed_parent_locs)) {
                 $current_module_sitemap_data['parent'][] = [
                    'url'        => $escaped_fallback_loc_abs, // For HTML template
                    'loc'        => $escaped_fallback_loc_abs, // For XML template
                    'title'      => $myts->makeTboxData4Show($module->getVar('name')),
                    'lastmod'    => null, 
                    'changefreq' => $default_changefreq,
                    'priority'   => $default_priority,
                    'child'      => []
                ];
            }
        }
        
        if (!empty($current_module_sitemap_data['parent']) || !empty($current_module_sitemap_data['sublinks'])) {
            $block['modules'][$module_id] = $current_module_sitemap_data;
        }
    }
    return $block;
}

function sitemap_get_categories_map($table, $id_name, $pid_name, $title_name_in_db, $url_path_segment, $order = "")
{
    global $sitemap_configs;
    $mytree = new XoopsTree($table, $id_name, $pid_name);
    $xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();
    $sitemap_result_data = ['parent' => []];
    $myts = MyTextSanitizer::getInstance();

    $default_changefreq = $sitemap_configs['default_changefreq'] ?? 'weekly';
    $default_priority = $sitemap_configs['default_priority'] ?? '0.5';
    $show_subcategories_config = !empty($sitemap_configs['show_subcategoris']);

    $i = 0;
    $sql = "SELECT `$id_name`, `$title_name_in_db` FROM `" . $xoopsDB->prefix($table) . "` WHERE `$pid_name`=0";
    if ($order !== '') {
        $sql .= " ORDER BY `$order`";
    }

    $result = $xoopsDB->query($sql);
    if ($result) {
        while ($row = $xoopsDB->fetchArray($result)) {
            $catid = $row[$id_name];
            $name_from_db = $row[$title_name_in_db];
            $item_loc_abs = XOOPS_URL . (substr($url_path_segment, 0, 1) === '/' ? '' : '/') . $url_path_segment . $catid;
            $escaped_item_loc_abs = htmlspecialchars($item_loc_abs, ENT_XML1);

            $sitemap_result_data['parent'][$i] = [
                'id'         => $catid,
                'title'      => $myts->makeTboxData4Show($name_from_db),
                'url'        => $escaped_item_loc_abs, // For HTML template
                'loc'        => $escaped_item_loc_abs, // For XML template
                'lastmod'    => null,
                'changefreq' => $default_changefreq,
                'priority'   => $default_priority,
                'child'      => []
            ];

            if ($show_subcategories_config) {
                $j = 0;
                $child_ary = $mytree->getChildTreeArray($catid, $order);
                if (is_array($child_ary)) {
                    foreach ($child_ary as $child) {
                        if (!is_array($child) || !isset($child[$id_name]) || !isset($child[$title_name_in_db])) continue;

                        $sub_name_from_db = $child[$title_name_in_db];
                        $sub_item_loc_abs = XOOPS_URL . (substr($url_path_segment, 0, 1) === '/' ? '' : '/') . $url_path_segment . $child[$id_name];
                        $escaped_sub_item_loc_abs = htmlspecialchars($sub_item_loc_abs, ENT_XML1);
                        $sitemap_result_data['parent'][$i]['child'][$j] = [
                            'id'         => $child[$id_name],
                            'title'      => $myts->makeTboxData4Show($sub_name_from_db),
                            'image'      => ((strlen($child['prefix']) + 1 > 3) ? 4 : strlen($child['prefix']) + 1),
                            'url'        => $escaped_sub_item_loc_abs, // For HTML template
                            'loc'        => $escaped_sub_item_loc_abs, // For XML template
                            'lastmod'    => null,
                            'changefreq' => $default_changefreq,
                            'priority'   => $default_priority
                        ];
                        $j++;
                    }
                }
            }
            $i++;
        }
    }
    return $sitemap_result_data;
}
