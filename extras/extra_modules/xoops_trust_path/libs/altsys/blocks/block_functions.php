<?php

require_once dirname(dirname(__FILE__)).'/include/altsys_functions.php' ;

function b_altsys_admin_menu_show( $options )
{
	global $xoopsUser ;

	$mydirname = empty( $options[0] ) ? 'altsys' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_block_admin_menu.html' : trim( $options[1] ) ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;
	if( ! is_object( @$xoopsUser ) ) return array() ;

	// coretype
	$coretype = altsys_get_core_type() ;

	// mid_selected
	if( is_object( @$GLOBALS["xoopsModule"] ) ) {
		$mid_selected = $GLOBALS["xoopsModule"]->getVar("mid") ;
		// for system->preferences
		if( $mid_selected == 1 && @$_GET["fct"] == "preferences" && @$_GET["op"] == "showmod" && ! empty( $_GET["mod"] ) ) $mid_selected = intval( $_GET["mod"] ) ;
	} else {
		$mid_selected = 0 ;
	}

	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();

	$module_handler =& xoops_gethandler('module');
	$current_module =& $module_handler->getByDirname($mydirname);
	$config_handler =& xoops_gethandler('config');
	$current_configs = $config_handler->getConfigList( $current_module->mid() ) ;
	$moduleperm_handler =& xoops_gethandler('groupperm');
	$admin_mids = $moduleperm_handler->getItemIds('module_admin', $xoopsUser->getGroups());
	$modules = $module_handler->getObjects( new Criteria( 'mid' , '('.implode( ',' , $admin_mids ) . ')' , 'IN' ) , true ) ;

	$block = array(
		'mydirname' => $mydirname ,
		'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
		'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$current_configs['images_dir'] ,
		'mod_config' => $current_configs ,
	) ;

	foreach( $modules as $mod ) {
		$mid = intval( $mod->getVar( 'mid' ) ) ;
		$dirname = $mod->getVar( 'dirname' ) ;
		$modinfo = $mod->getInfo() ;
		$submenus4assign = array() ;
		$adminmenu = array() ;
		$adminmenu4altsys = array() ;
		unset( $adminmenu_use_altsys ) ;
		@include XOOPS_ROOT_PATH.'/modules/'.$dirname.'/'.@$modinfo['adminmenu'] ;
		// from admin_menu.php etc.
		$adminmenu = array_merge( $adminmenu , $adminmenu4altsys ) ;
		foreach( $adminmenu as $sub ) {
			$link = empty( $sub['altsys_link'] ) ? $sub['link'] : $sub['altsys_link'] ;
			if( isset( $sub['show'] ) && $sub['show'] === false ) continue ;
			$submenus4assign[] = array(
				'title' => $myts->makeTboxData4Show( $sub['title'] ) ,
				'url' => XOOPS_URL.'/modules/'.$dirname.'/'.htmlspecialchars( $link , ENT_QUOTES ) ,
			) ;
		}

		// for modules overriding Module.class.php (eg. Analyzer for XC)
		if( empty( $submenus4assign ) && defined( 'XOOPS_CUBE_LEGACY' ) && ! empty( $modinfo['cube_style'] ) ) {
			$module_handler =& xoops_gethandler('module');
			$module =& $module_handler->get($mid);
			$moduleObj =& Legacy_Utils::createModule($module);
			$modinfo['adminindex'] = $moduleObj->getAdminIndex() ;
			$modinfo['adminindex_absolute'] = true ;
			foreach( $moduleObj->getAdminMenu() as $sub ) {
				if( @$sub['show'] === false ) continue ;
				$submenus4assign[] = array(
					'title' => $myts->makeTboxData4Show( $sub['title'] ) ,
					'url' => strncmp( $sub['link'] , 'http' , 4 ) === 0 ? htmlspecialchars( $sub['link'] , ENT_QUOTES ) : XOOPS_URL.'/modules/'.$dirname.'/'.htmlspecialchars( $sub['link'] , ENT_QUOTES ) ,
				) ;
			}
		} else if( empty( $adminmenu4altsys ) ) {

			// add preferences
			if( $mod->getVar('hasconfig') && ! in_array( $mod->getVar('dirname') , array( 'system' , 'legacy' ) ) ) {
				$submenus4assign[] = array(
					'title' => _PREFERENCES ,
					'url' => htmlspecialchars( altsys_get_link2modpreferences( $mid , $coretype ) , ENT_QUOTES ) ,
				) ;
			}

			// add help
			if( defined( 'XOOPS_CUBE_LEGACY' ) && ! empty( $modinfo['help'] ) ) {
				$submenus4assign[] = array(
					'title' => _HELP ,
					'url' => XOOPS_URL.'/modules/legacy/admin/index.php?action=Help&amp;dirname='.$dirname ,
				) ;
			}
		}

		$module4assign = array(
			'mid' => $mid ,
			'dirname' => $dirname ,
			'name' => $mod->getVar( 'name' ) ,
			'version_in_db' => sprintf( '%.2f' , $mod->getVar( 'version' ) / 100.0 ) ,
			'version_in_file' => sprintf( '%.2f' , $modinfo['version'] ) ,
			'description' => htmlspecialchars( @$modinfo['description'] , ENT_QUOTES ) ,
			'image' => htmlspecialchars( $modinfo['image'] , ENT_QUOTES ) ,
			'isactive' => $mod->getVar( 'isactive' ) ,
			'hasmain' => $mod->getVar( 'hasmain' ) ,
			'hasadmin' => $mod->getVar( 'hasadmin' ) ,
			'hasconfig' => $mod->getVar( 'hasconfig' ) ,
			'weight' => $mod->getVar( 'weight' ) ,
			'adminindex' => htmlspecialchars( @$modinfo['adminindex'] , ENT_QUOTES ) ,
			'adminindex_absolute' => @$modinfo['adminindex_absolute'] ,
			'submenu' => $submenus4assign ,
			'selected' => $mid == $mid_selected ? true : false ,
			'dot_suffix' => $mid == $mid_selected ? 'selected_opened' : 'closed' ,
		) ;
		$block['modules'][] = $module4assign ;
	}

	require_once XOOPS_TRUST_PATH.'/libs/altsys/class/D3Tpl.class.php' ;
	$tpl = new D3Tpl() ;
	$tpl->assign( 'block' , $block ) ;
	$ret['content'] = $tpl->fetch( $this_template ) ;
	return $ret ;
}


function b_altsys_admin_menu_edit( $options )
{
	$mydirname = empty( $options[0] ) ? 'd3forum' : $options[0] ;
	$this_template = empty( $options[1] ) ? 'db:'.$mydirname.'_block_admin_menu.html' : trim( $options[1] ) ;

	if( preg_match( '/[^0-9a-zA-Z_-]/' , $mydirname ) ) die( 'Invalid mydirname' ) ;

	$form = "
		<input type='hidden' name='options[0]' value='$mydirname' />
		<label for='this_template'>"._MB_ALTSYS_THISTEMPLATE."</label>&nbsp;:
		<input type='text' size='60' name='options[1]' id='this_template' value='".htmlspecialchars($this_template,ENT_QUOTES)."' />
		<br />
	\n" ;

	return $form;
}


?>
