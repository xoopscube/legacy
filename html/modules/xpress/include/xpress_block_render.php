<?php
if(!defined('XPRESS_BLOCK_RENDER_FUNCTION_READ')){
	define('XPRESS_BLOCK_RENDER_FUNCTION_READ',1);
	require_once dirname( __FILE__ ) .'/xml.php' ;
	require_once dirname( __FILE__ ) .'/xpress_cache.php' ;
	global $xoops_config;
	
	if (!is_object($xoops_config)){ // is call other modules
		require_once dirname(dirname( __FILE__ )) .'/class/config_from_xoops.class.php' ;
		$xoops_config = new ConfigFromXoops;
	}
	
	function xpress_block_cache_write($mydirname,$block_name,$block)
	{
			$xml = xpress_XML_serialize($block);
			$xml_name = $block_name . '.xml';
			if (WPLANG == 'ja_EUC'){
				$xml = str_replace('<?xml version="1.0" ?>', '<?xml version="1.0" encoding="EUC-JP" ?>' , $xml);
			}
			xpress_cache_write($mydirname,$xml_name,$xml);
	}
	function xpress_block_cache_read($mydirname,$block_name)
	{
		$xml_name = $block_name . '.xml';
		$xml_data = xpress_cache_read($mydirname,$xml_name);
		
		$GLOBALS['DO_LIBXML_PATCH'] = get_xpress_mod_config($mydirname,'libxml_patch');
		
		// The character-code not treatable exists when 'XML_unserialize' of PHP5 processes EUC-JP. 
		// And, the result is returned by character-code UTF-8. 
		// Measures
		// After the character-code is converted into UTF-8, XML_unserialize will be processed. 
		if ( strstr($xml_data, '<?xml version="1.0" encoding="EUC-JP" ?>') !== false
			 && version_compare(PHP_VERSION, '5.0.0', '>') )
		{
			$xml_data = str_replace('<?xml version="1.0" encoding="EUC-JP" ?>', '<?xml version="1.0" encoding="UTF-8" ?>', $xml_data);
			$ans = mb_convert_variables('UTF-8' , 'EUC-JP', &$xml_data); //EUC-JP to UTF-8
			$ret = @xpress_XML_unserialize($xml_data);
			$ans = mb_convert_variables('EUC-JP' , 'UTF-8', &$ret); //UTF-8 to EUC-JP
		} else {
			$ret = xpress_XML_unserialize($xml_data);
		}
		return $ret;
	}
	
	function get_block_id($mydirname,$func_file,$options)
	{
		$options_string = '';
		$mid = get_block_mid($mydirname);
		foreach ($options as $val){
			if (!empty($options_string)) $options_string .='|';
			$options_string .= $val;
		}
			$xoopsDB =& Database::getInstance();
			$block_tbl = $xoopsDB->prefix('newblocks');	
			$module_dir = XOOPS_ROOT_PATH . '/modules/' . $mydirname;

			$sql = "SELECT bid FROM $block_tbl WHERE (mid = $mid) AND (func_file LIKE '$func_file') AND (options LIKE '$options_string')";
			$result =  $xoopsDB->query($sql, 0, 0);
			if ($xoopsDB->getRowsNum($result)  > 0){
				$row = $xoopsDB->fetchArray($result);
				$block_id = $row['bid'];
			}
			return $block_id;
	}

	function get_block_mid($mydirname)
	{
			$xoopsDB =& Database::getInstance();
			$modules_tbl = $xoopsDB->prefix('modules');

			$sql = "SELECT mid FROM $modules_tbl WHERE dirname = '$mydirname'";
			$result =  $xoopsDB->query($sql, 0, 0);
			if ($xoopsDB->getRowsNum($result)  > 0){
				$row = $xoopsDB->fetchArray($result);
				$mid = $row['mid'];
			}
			return $mid;
	}

	function get_xpress_theme_name($mydirname)
	{
		global $wpdb;
		
		if (is_null($wpdb)){
			$xoopsDB =& Database::getInstance();
			$wp_prefix = preg_replace('/wordpress/','wp',$mydirname);

			$module_tbl = $xoopsDB->prefix($wp_prefix).'_options';
			$theme_name = '';

			$sql = "SELECT option_value FROM $module_tbl WHERE option_name LIKE 'template'";
			$result =  $xoopsDB->query($sql, 0, 0);
			if ($xoopsDB->getRowsNum($result)  > 0){
				$row = $xoopsDB->fetchArray($result);
				$theme_name = $row['option_value'];
			}
		} else {
			$theme_name = get_option('template');
		}
		return $theme_name;
	}

	function xpress_block_header_set($mydirname = '')
	{
		require_once( dirname( __FILE__ ).'/xpress_block_header.php' );	
		$xml = xpress_block_header_cache_read($mydirname);
		$block_header = $xml['block_header'];
		if (!empty($block_header)){
			$tplVars =& $GLOBALS['xoopsTpl']->get_template_vars();
			if(array_key_exists('xoops_block_header', $tplVars)) {
				if (!strstr($tplVars['xoops_block_header'],$block_header)) {
					$GLOBALS['xoopsTpl']->assign('xoops_block_header',$tplVars['xoops_block_header'].$block_header);
				}
			} else {
				$GLOBALS['xoopsTpl']->assign('xoops_block_header',$block_header);
			}
		}
	}
	
	
    function xpress_block_cache_found($mydirname,$block_name)
    {
    	global $xoops_config;
    	
		$cache_dir = $xoops_config->xoops_cache_path . '/';
    	$xml_name = $block_name . '.xml';

        $filename = $cache_dir .$mydirname . '_' . $xml_name;
		$cache_time = 0;
//        if (file_exists($filename) && ((time() - filemtime($filename)) < $cache_time)) {
        if (file_exists($filename)) {
            return true;
       } else {
			return false;
		}
    } 
	
	function xpress_block_render($mydirname,$block_function_name,$options)
	{
		global $wpdb,$xoops_config,$xoopsUserIsAdmin;
		$func_file = $block_function_name;
		$call_theme_function_name = str_replace(".php", "", $block_function_name);
		$inc_theme_file_name = $call_theme_function_name . '_theme.php';
		$cache_title = str_replace(".php", "", $block_function_name);
		$blockID =get_block_id($mydirname,$func_file,$options);		

		$this_block_url = '/' . $mydirname . '/';
		$call_url = $_SERVER['REQUEST_URI'];
		$block['err_message'] = '';

		if (strstr($call_url , $this_block_url) !== false && strstr($call_url , $this_block_url . 'admin/') === false){
			$block_theme_file = get_block_file_path($mydirname,$inc_theme_file_name);
			require_once $block_theme_file['file_path'];
			$block = $call_theme_function_name($options);		//The block name and the called function name should be assumed to be the same name. 
			if (!empty($block_theme_file['error']))
				$block['err_message'] .= $block_theme_file['error'];
		} else {
			if (xpress_block_cache_found($mydirname,$cache_title. $blockID)){
				$xml = xpress_block_cache_read($mydirname,$cache_title. $blockID);
				$block = $xml['block'];
			} else {
				$block['err_message'] .= sprintf(_MB_XP2_BLOCK_CACHE_ERR, '<a href="' . XOOPS_URL . '/modules/' . $mydirname . '">' . $mydirname .'</a>');
			}
		}

		if(!cache_is_writable()){
			$block['err_message']  ='<span style="color:#ff0000">';
			$block['err_message'] .= _MB_XP2_CACHE_NOT_WRITABLE ;
			if($xoopsUserIsAdmin){
				$block['err_message'] .=  " ($cache_dir)";
				$block['err_message'] .= '</span>';
			}
		}
		xpress_block_header_set($mydirname);
		$block['request_uri'] = $_SERVER['REQUEST_URI'];
		$temp_option = @explode(':' , $options[1]);
		
		if (isset($temp_option[1])) {
			$templates_file = $options[1];
		} else {
			$templates_file = 'db:'.$mydirname. '_' . str_replace(".php", ".html", $block_function_name);
		}
		
		$tpl = new XoopsTpl() ;
		$tpl->template_dir = $xoops_config->module_path . '/templates';
		if (!$tpl->template_exists($templates_file)){
			$src_file_path = $xoops_config->module_path . '/templates/' .$mydirname. '_' . str_replace(".php", ".html", $block_function_name);
			$templates_file = add_xpress_tpl($mydirname,$templates_file,$src_file_path);
		}
		$tpl->assign( 'block' , $block ) ;
		$ret['content'] = $tpl->fetch( $templates_file ) ;
		if(preg_match('/\S/',$ret['content'])){
			return $ret ;
		}else {
			return null;
		}
	}
	
	function add_xpress_tpl($mydirname,$templates='',$src_file_path){
		global $wpdb,$xoops_config , $xoops_db;
		
		$mid = get_block_mid($mydirname);

		$temp_parm = explode(':' , $templates);
		if (empty($temp_parm[1])) {
			$filename=$temp_parm[0];
			$type = 'db';
		} else  {
			$filename=$temp_parm[1];
			$type = $temp_parm[0];
		}
		$temp_file_path = $xoops_config->module_path . '/templates/'. $filename;
		$pattern = '^' . $mydirname . '_';
		if (preg_match('/' . $pattern . '/' , $filename, $match)){ // file prefix check
			if (!file_exists($temp_file_path)){		// Repetition check
				if (file_exists($src_file_path)){	// source file check
					$rcd = copy($src_file_path, $temp_file_path);
				}
			}
			return  'file:' . $filename;
		}
		return $templates;
	}
	
	function xpress_block_cache_refresh($mydirname)
	{
		global $xoops_db;
		$mid = get_xpress_modid();
		
		// It is a block that needs cache arranged outside the module. 
		// Only the block arranged outside the module is detected here.
		$newblocks = get_xoops_prefix() . "newblocks";
		$block_module_link = get_xoops_prefix(). "block_module_link";
		$sql  = "SELECT * FROM $newblocks LEFT JOIN $block_module_link ON {$newblocks}.bid = {$block_module_link}.block_id ";
		$sql .= "WHERE {$newblocks}.mid = $mid AND {$newblocks}.visible = 1 AND {$block_module_link}.module_id != $mid ";
		$sql .= "GROUP BY {$newblocks}.bid";

		$blocks = $xoops_db->get_results($sql);
		require_once get_xpress_dir_path() . '/include/xpress_block_render.php';

		foreach($blocks as $block){
			$func_file = $block->func_file;
			$call_theme_function_name = str_replace(".php", "", $func_file);
			$inc_theme_file_name = str_replace(".php", "", $func_file) . '_theme.php';
			$cache_title = str_replace(".php", "", $func_file);
			$blockID = $block->bid;
			$options = explode("|", $block->options);

			$block_theme_file = get_block_file_path($mydirname,$inc_theme_file_name);
			require_once $block_theme_file['file_path'];
			$render = $call_theme_function_name($options);		//The block name and the called function name should be assumed to be the same name. 			
			$render_array['block'] = $render;
			$render_array['block']['options'] = $block->options;
			if (!empty($block_theme_file['error']))
				$render_array['block']['err_message'] = $block_theme_file['error'];
			if(cache_is_writable()){
				if (xpress_block_cache_found($mydirname,$cache_title. $blockID)){	
					$render_serialize = xpress_XML_serialize($render_array);
					$render_md5 = md5($render_serialize);

					$cache_serialize = xpress_cache_read($mydirname,$cache_title. $blockID.'.xml');
					$cache_md5 = md5($cache_serialize);
					
					if ($render_md5 != $cache_md5){
						xpress_block_cache_write($mydirname,$cache_title. $blockID, $render_array);
					}
				} else {
					xpress_block_cache_write($mydirname,$cache_title. $blockID, $render_array);
				}
			}
		}
	}
	
	function xpress_unnecessary_block_cache_delete($mydirname)
	{
		global $xoops_db,$xoops_config;
		
		$mid = get_xpress_modid();
		$sql = "SELECT bid,options,func_file FROM " . get_xoops_prefix() . "newblocks WHERE mid = $mid AND visible = 1";
		$blocks = $xoops_db->get_results($sql);
		require_once get_xpress_dir_path() . '/include/xpress_block_render.php';

		$pattern =$mydirname . '_block_header';
		foreach($blocks as $block){
			$cache_file_name = $mydirname . '_'. str_replace(".php", "", $block->func_file) . $block->bid;
			if (!empty($pattern))  $pattern .= '|';
			$pattern .= $cache_file_name;
		}
		$pattern = '(' . $pattern . ')';
		
		$cache_dir = $xoops_config->xoops_cache_path . '/';
		$cache_time = 0;
        if ($dh = opendir($cache_dir)) {
            while (($file = readdir($dh)) !== false) {
                if (preg_match('/^' . preg_quote($mydirname) . '_/', $file)) {
                	if(! preg_match('/' . $pattern . '/', $file)) {
                    	unlink($cache_dir.$file);
                    }
                } 
            } 
            closedir($dh);
        } 
    } 
    
    function get_xpress_mod_config($mydirname,$conf_name=''){
		$module_handler =& xoops_gethandler('module');
		$xoopsModule =& $module_handler->getByDirname($mydirname);
		$mid = $xoopsModule->getVar('mid');
		$xoopsDB =& Database::getInstance();
		$db_config = $xoopsDB->prefix('config');
	    
		$wu_sql  = 	"SELECT conf_value FROM  $db_config ";
		$wu_sql .=	"WHERE (conf_modid = $mid ) AND (conf_name LIKE '$conf_name')";
		$wu_res = $xoopsDB->queryF($wu_sql, 0, 0);
			
		if ($wu_res === false){
			return 0;
		} else {
			$xu_row = $xoopsDB->fetchArray($wu_res);
			return $xu_row['conf_value'];
		}
	}	
}	
?>