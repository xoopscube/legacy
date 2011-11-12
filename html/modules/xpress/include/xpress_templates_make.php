<?php

function xpress_templates_make($mid,$mydirname)
{
	$msgs = array();
	// TEMPLATES
	$tplfile_handler =& xoops_gethandler( 'tplfile' ) ;
	$tpl_path = XOOPS_ROOT_PATH . '/modules/' . $mydirname . '/templates' ;
	
	//copy  template file from source
	if( $handler = @opendir( $tpl_path . '/source/' ) ) {
		while( ( $file = readdir( $handler ) ) !== false ) {
			if( substr( $file , 0 , 1 ) == '.' ) continue ;
			if(!is_template($file)) continue ;
			$file_path = $tpl_path . '/source/' . $file ;
			if( is_file( $file_path ) ) {
				$target_file_name = $mydirname . '_' . $file;
				$target_file_path = $tpl_path . '/'.$target_file_name;
				$rcd = @unlink($target_file_path);
				if ($mydirname != 'xpress') {		//old version file delete
					$rcd = @unlink($tpl_path . '/xpress'.$file);
				}
//				$rcd = rename($file_path, $target_file_path);
				$rcd = copy($file_path, $target_file_path);
				if ($rcd){
					$msgs[] = 'Template <b>'.htmlspecialchars($target_file_path).'</b> copy from ' . htmlspecialchars($file) . '<br />';
				} else {
					$msgs[] = '<span style="color:#ff0000;">ERROR: Could not copy template <b>'.htmlspecialchars($target_file_name).'</b> from ' . htmlspecialchars($file). '(check templates directory permision (777))</span><br />';
				}					
			}
		}
		closedir( $handler ) ;
	}
	
	// template added to the database.
	if( $handler = @opendir( $tpl_path . '/' ) ) {
		while( ( $file = readdir( $handler ) ) !== false ) {
			if( substr( $file , 0 , 1 ) == '.' ) continue ;
			$file_path = $tpl_path . '/' . $file ;
			$file_name = $file ;
			$pattern = '^' . $mydirname . '_';
			if (preg_match('/' . $pattern . '/' , $file_name, $match)){
				if( is_file( $file_path ) ) {
					$msgs[] = xpress_create_db_template($file_name,$file_path,$mydirname,$mid);
				}
			}
		}
		closedir( $handler ) ;
	}
	
	include_once XOOPS_ROOT_PATH.'/class/xoopsblock.php' ;
	include_once XOOPS_ROOT_PATH.'/class/template.php' ;
	xoops_template_clear_module_cache( $mid ) ;

	return $msgs;
}

function xpress_create_db_template($file_name,$file_path,$mydirname,$mid){
	if( is_file( $file_path ) ) {
		$tplfile_handler =& xoops_gethandler( 'tplfile' ) ;
		$tplfile =& $tplfile_handler->create() ;
		$mtime = intval( @filemtime( $file_path ) ) ;
		$tplfile->setVar( 'tpl_source' , file_get_contents( $file_path ) , true ) ;
		$tplfile->setVar( 'tpl_refid' , $mid ) ;
		$tplfile->setVar( 'tpl_tplset' , 'default' ) ;
		$tplfile->setVar( 'tpl_file' ,  $file_name ) ;
		$tplfile->setVar( 'tpl_desc' , '' , true ) ;
		$tplfile->setVar( 'tpl_module' , $mydirname ) ;
		$tplfile->setVar( 'tpl_lastmodified' , $mtime ) ;
		$tplfile->setVar( 'tpl_lastimported' , 0 ) ;
		$tplfile->setVar( 'tpl_type' , 'module' ) ;
		if( ! $tplfile_handler->insert( $tplfile ) ) {
			$msg = '<span style="color:#ff0000;">ERROR: Template Could not added to the database. <b>'.htmlspecialchars($file_name).'</b> to the database.</span><br />';
		} else {
			$tplid = $tplfile->getVar( 'tpl_id' ) ;
			$msg = 'Template <b>'.htmlspecialchars($file_name) .'</b> added to the database. (ID: <b>'.$tplid.'</b>)<br />';
			// generate compiled file
			include_once XOOPS_ROOT_PATH.'/class/xoopsblock.php' ;
			include_once XOOPS_ROOT_PATH.'/class/template.php' ;
			if( ! xoops_template_touch( $tplid ) ) {
				$msg = '<span style="color:#ff0000;">ERROR: Failed compiling template <b>'.htmlspecialchars($file_name).'</b>.</span><br />';
			} else {
				$msg = 'Template <b>'.htmlspecialchars($file_name).'</b> compiled.</span><br />';
			}
		}
	}
	return $msg;
}

function is_template($file_name){
	if (empty($file_name)) return false;
	
	$temp_list = array(
		'recent_comments_block.html',
		'recent_posts_content_block.html',
		'recent_posts_list_block.html',
		'calender_block.html',
		'popular_posts_block.html',
		'archives_block.html',
		'authors_block.html',
		'page_block.html',
		'search_block.html',
		'tag_cloud_block.html',
		'category_block.html',
		'meta_block.html' ,
		'sidebar_block.html' ,
		'widget_block.html' ,
		'enhanced_block.html' ,
		'blog_list_block.html' ,
		'global_recent_posts_list_block.html' ,
		'global_recent_comments_block.html',
		'global_popular_posts_block.html',
		'index.html',
	);
	foreach($temp_list as $ck_file){
		if (strcmp($ck_file,$file_name) ==  0) return true;
	}
	return false;
}

function xpress_clean_templates_file($mydirname,$mod_version)
{
	$tpl_path = XOOPS_ROOT_PATH . '/modules/' . $mydirname . '/templates/' ;
	$msgs = array();
	if( $handler = @opendir( $tpl_path) ) {
		while( ( $file = readdir( $handler ) ) !== false ) {
			if( substr( $file , 0 , 1 ) == '.' ) continue ;
			if ( strcmp($file,'source')==0 ) continue ;
			if ( strcmp($file,'index.html')==0 ) continue ;
			$target_file_path = $tpl_path . $file;
			if (is_dir($target_file_path)){
				rmDirectory($target_file_path);
				$msgs[] = 'Template <b>'.htmlspecialchars($file).'</b> directory deleted.</span><br />';
			} else {
				if ($mod_version >= 200){
					$pattern = '^' . $mydirname . '_';
					if (preg_match('/' . $pattern . '/' , $file, $match))  continue ;
				}
				$rcd = @unlink($target_file_path);
				$msgs[] = 'Template <b>'.htmlspecialchars($file).'</b> file deleted.</span><br />';
			}
		}
		closedir( $handler ) ;
	}
	return $msgs;
}

function rmDirectory($dir){
	if ($handle = opendir("$dir")){
		while (false !== ($item = readdir($handle))){
			if ($item != "." && $item != ".."){
				if (is_dir("$dir/$item")){
					rmDirectory("$dir/$item");
				}else{
					unlink("$dir/$item");
				}
			}
		}
		closedir($handle);
		rmdir($dir);
	}
}

?>