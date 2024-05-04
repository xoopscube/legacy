<?php
/**
 * Sitemap
 * Automated Sitemap and XML file for search engines
 * @package    Sitemap
 * @version    2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Ryuji
 * @author     chanoir
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v.2.0
 */

include_once(XOOPS_ROOT_PATH . '/class/xoopstree.php');

// body
function sitemap_show()
{
	global $xoopsUser, $xoopsConfig, $sitemap_configs ;
	$plugin_dir = XOOPS_ROOT_PATH . "/modules/sitemap/plugins/";

	// invisible weights
	$invisible_weights = [];
	if( trim( @$sitemap_configs['invisible_weights'] ) !== '' ) {
		$invisible_weights = explode( ',' , $sitemap_configs['invisible_weights'] ) ;
	}

	// invisible dirnames
	$invisible_dirnames = empty( $sitemap_configs['invisible_dirnames'] ) ? '' : str_replace( ' ' , '' , $sitemap_configs['invisible_dirnames'] ) . ',' ;

	$block = [];

//	@$block['lang_home'] = _MD_SITEMAP_HOME ;
//	@$block['lang_close'] = _CLOSE ;

	$module_handler =& xoops_gethandler('module');


	$criteria = new CriteriaCompo(new Criteria('hasmain', 1));
	$criteria->add(new Criteria('isactive', 1));

	$modules =& $module_handler->getObjects($criteria, true);
	$moduleperm_handler =& xoops_gethandler('groupperm');

	$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
	$read_allowed = $moduleperm_handler->getItemIds('module_read', $groups);

	foreach (array_keys($modules) as $i) {
		if (in_array($i, $read_allowed) && ! in_array($modules[$i]->getVar('weight'),$invisible_weights) && false === stripos($invisible_dirnames, $modules[$i]->getVar('dirname') . ',')) {
			if ($modules[$i]->getVar('dirname') === 'sitemap') {
				continue;
			}
			$block['modules'][$i]['id'] = $i;
			$block['modules'][$i]['name'] = $modules[$i]->getVar('name');
			$block['modules'][$i]['directory'] = $modules[$i]->getVar('dirname');

			$old_error_reporting = error_reporting() ;
			error_reporting( $old_error_reporting & (~E_NOTICE) ) ;
			$sublinks =& $modules[$i]->subLink();
			error_reporting( $old_error_reporting ) ;

			if ((is_countable($sublinks) ? count($sublinks) : 0) > 0) {
				foreach($sublinks as $sublink){
					$block['modules'][$i]['sublinks'][] = ['name' => $sublink['name'], 'url' => XOOPS_URL.'/modules/'.$modules[$i]->getVar('dirname').'/'.$sublink['url']];
				}
			} else {
				$block['modules'][$i]['sublinks'] = [];
			}
            // Autoload plugin by Ryuji
            // Dynamic include a module plugin and get the information.
            // Check if the plugin is prepared on the module side
			// plugin modules/DIRNAME/include/sitemap.plugin.php
			// lang   modules/DIRNAME/language/LANG/sitemap.php
			$mod = $modules[$i]->getVar("dirname");
			$mydirname = $mod ;

			// get $mytrustdirname for D3 modules
			$mytrustdirname = '' ;
			if( defined( 'XOOPS_TRUST_PATH' ) && file_exists( XOOPS_ROOT_PATH."/modules/".$mydirname."/mytrustdirname.php" ) ) {
				@include XOOPS_ROOT_PATH."/modules/".$mydirname."/mytrustdirname.php" ;
			}

			$mod_plugin_file = XOOPS_ROOT_PATH."/modules/".$mod."/include/sitemap.plugin.php";

			if(file_exists($mod_plugin_file)){
				// module side plugin under xoops_root_path (1st priority)
				$mod_plugin_lng = XOOPS_ROOT_PATH."/modules/".$mod."/language/".$xoopsConfig['language']."/sitemap.php";
				if(file_exists($mod_plugin_lng)){
					include_once($mod_plugin_lng);
				}else{
					$mod_plugin_lng = XOOPS_ROOT_PATH."/modules/".$mod."/language/english/sitemap.php";
					if(file_exists($mod_plugin_lng)){
						include_once($mod_plugin_lng);
					}
				}
				require_once $mod_plugin_file ;
				// call the function
				if (function_exists("b_sitemap_" . $mod)){
					$_tmp = call_user_func("b_sitemap_" . $mod , $mydirname );
					if (isset($_tmp["parent"])) {
						$block['modules'][$i]['parent'] = $_tmp["parent"];
					}
				}
			} elseif( ! empty( $mytrustdirname ) && file_exists( XOOPS_TRUST_PATH."/modules/".$mytrustdirname."/include/sitemap.plugin.php" ) ) {
				// D3 module's plugin under xoops_trust_path (2nd priority)
				$mod_plugin_lng = XOOPS_TRUST_PATH."/modules/".$mytrustdirname."/language/".$xoopsConfig['language']."/sitemap.php";
				if(file_exists($mod_plugin_lng)){
					include_once($mod_plugin_lng);
				}else{
					$mod_plugin_lng = XOOPS_TRUST_PATH."/modules/".$mytrustdirname."/language/english/sitemap.php";
					if(file_exists($mod_plugin_lng)){
						include_once($mod_plugin_lng);
					}
				}
				require_once XOOPS_TRUST_PATH."/modules/".$mytrustdirname."/include/sitemap.plugin.php" ;
				// call the function
				if (function_exists("b_sitemap_" . $mytrustdirname)){
					$_tmp = call_user_func("b_sitemap_" . $mytrustdirname , $mydirname );
					if (isset($_tmp["parent"])) {
						$block['modules'][$i]['parent'] = $_tmp["parent"];
					}
				}
			} else {
				// sitemap built-in plugin (last priority)
				$mod_plugin_dir = $plugin_dir ;
				$mod_plugin_file = $mod_plugin_dir . $mod . ".php";
				$mod_plugin_lng = $mod_plugin_dir . $xoopsConfig['language'] . ".lng.php";
                // include the language file
				if (file_exists($mod_plugin_lng)){
					include_once($mod_plugin_lng);
				}else{
					$mod_plugin_lng = $mod_plugin_dir . "english" . ".lng.php";
					if (file_exists($mod_plugin_lng)){
						include_once($mod_plugin_lng);
					}
				}
				// include the plugin and call the function
				if (file_exists($mod_plugin_file)){
					require_once $mod_plugin_file ;
					// call the function
					if (function_exists("b_sitemap_" . $mod)){
						$_tmp = call_user_func("b_sitemap_" . $mod , $mydirname );
						if (isset($_tmp["parent"])) {
							$block['modules'][$i]['parent'] = $_tmp["parent"];
						}
					}
				}
			}
		}
	}
	return $block;
}

// function to get the category list of common patterns such as mylinks and news
function sitemap_get_categories_map($table, $id_name, $pid_name, $title_name, $url, $order = ""){
	global $sitemap_configs;
	$mytree = new XoopsTree($table, $id_name, $pid_name);
	$xoopsDB =& Database::getInstance();

	$sitemap = [];
	$myts =& MyTextSanitizer::getInstance();

	$i = 0;
	$sql = "SELECT `$id_name`,`$title_name` FROM `$table` WHERE `$pid_name`=0" ;
	if ($order !== '')
	{
		$sql .= " ORDER BY `$order`" ;
	}
	$result = $xoopsDB->query($sql);
	while ([$catid, $name] = $xoopsDB->fetchRow($result))
	{
        // Parent output
		$sitemap['parent'][$i]['id'] = $catid;
		$sitemap['parent'][$i]['title'] = $myts->makeTboxData4Show( $name ) ;
		$sitemap['parent'][$i]['url'] = $url.$catid;

        // Child output
        // Run only when displaying a subcategory by Ryuji
		if(@$sitemap_configs["show_subcategoris"]){
			$j = 0;
			$child_ary = $mytree->getChildTreeArray($catid, $order);
			foreach ($child_ary as $child)
			{
				$count = strlen($child['prefix']) + 1; // MEMO : depth of subcategory is set by the length of prefix
				$sitemap['parent'][$i]['child'][$j]['id'] = $child[$id_name];
				$sitemap['parent'][$i]['child'][$j]['title'] = $myts->makeTboxData4Show( $child[$title_name] ) ;
				$sitemap['parent'][$i]['child'][$j]['image'] = (($count > 3) ? 4 : $count);
				$sitemap['parent'][$i]['child'][$j]['url'] = $url.$child[$id_name];

				$j++;
			}
		}
		$i++;
	}
	return $sitemap;
}
