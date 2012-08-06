<?php

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

//
// definitions
//

define( 'XUGJ_ASSIGN_FMT_MENU_ID' , 'menu_level%d_%s_%s' ) ;
define( 'XUGJ_ASSIGN_FMT_MENU_CLASS' , 'menu_level%d' ) ;
define( 'SITE_SALT' , substr( md5( XOOPS_URL . XOOPS_DB_PREFIX ) , -4 ) ) ;
$theme_name = basename( dirname(__FILE__) ) ;

// root controllers
@include_once XOOPS_ROOT_PATH.'/language/'.$GLOBALS['xoopsConfig']['language'].'/user.php' ;
$root_controllers = array(
	'/register.php' => array( 'name' => @_US_USERREG ) ,
	'/userinfo.php' => array( 'name' => @_US_PROFILE ) ,
	'/edituser.php' => array( 'name' => @_US_EDITPROFILE ) ,
	'/viewpmsg.php' => array( 'name' => @_US_INBOX ) ,
	'/readpmsg.php' => array( 'name' => @_US_INBOX , 'url' => XOOPS_URL.'/viewpmsg.php' ) ,
	'/notifications.php' => array( 'name' => @_NOT_NOTIFICATION ) ,
	'/search.php' => array( 'name' => @_SR_SEARCH ) ,
) ;

// the best method is to be assigned by module self.  especially D3 modules have to assign this :-)

// the second best method to get breadcrumbs (rebuilding from an assigned var) NOT IMPLEMENTED
$modcat_assigns = array(
	// 0=>var_name, 1=>separator
	'mydownloads' => array( 'category_path' , '&nbsp;:&nbsp;' ) ,
	'mylinks' => array( 'category_path' , '&nbsp;:&nbsp;' ) ,
) ;

// the last(worst) method to get breadcrumbs (querying parents recursively)
$modcat_trees = array(
	// 0=>table, 1=>col4id, 2=>col4pid, 3=>col4name, 4=>GET index, 5=>_tpl_vars, 6=>url_fmt
	'AMS'=>array('ams_topics','topic_id','topic_pid','topic_title','storytopic',null,'index.php?storytopic=%d'),
	'articles'=>array('articles_cat','id','cat_parent_id','cat_name','cat_id',null,'index.php?cat_id=%d'),
	'booklists'=>array('mybooks_cat','cid','pid','title','cid',null,'viewcat.php?cid=%d'),
	'catads'=>array('catads_cat','cat_id','pid','title','cat_id',null,'adslist.php?cat_id=%d'),
	'debaser'=>array('debaser_genre','genreid','subgenreid','genretitle','genreid',null,'genre.php?genreid=%d'),
	'myAds'=>array('ann_categories','cid','pid','title','cid',null,'index.php?pa=view&amp;cid=%d'),
	'myalbum'=>array('myalbum_cat','cid','pid','title','cid','photo.cid','viewcat.php?cid=%d'),
	'mydownloads'=>array('mydownloads_cat','cid','pid','title','cid',null,'viewcat.php?cid=%d'),
	'mylinks'=>array('mylinks_cat','cid','pid','title','cid',null,'viewcat.php?cid=%d'),
	'mymovie'=>array('mymovie_cat','cid','pid','title','cid',null,'viewcat.php?cid=%d'),
	'news'=>array('topics','topic_id','topic_pid','topic_title','storytopic',null,'index.php?storytopic=%d'),
	'piCal'=>array('pical_cat','cid','pid','cat_title','cid',null,'index.php?cid=%d'),
	'plzXoo'=>array('plzxoo_category','cid','pid','name','cid','category.cid','index.php?cid=%d'),
	'smartfaq'=>array('smartfaq_categories','categoryid','parentid','name','categoryid',null,'category.php?categoryid=%d'),
	'smartsection'=>array('smartsection_categories','categoryid','parentid','name','categoryid',null,'category.php?categoryid=%d'),
	'tutorials'=>array('tutorials_categorys','cid','scid','cname','cid',null,'listutorials?cid=%d'),
	'weblinks'=>array('weblinks_category','cid','pid','title','cid',null,'viewcat.php?cid=%d'),
	'weblog'=>array('weblog_category','cat_id','cat_pid','cat_title','cat_id',null,'index.php?cat_id=%d'),
	'wfdownloads'=>array('wfdownloads_cat','cid','pid','title','cid',null,'viewcat.php?cid=%d'),
	'wfsection'=>array('wfs_category','id','pid','title','category',null,'viewarticles.php?category=%d'),
	'wordpress'=>array('wp_categories','cat_ID','category_parent','cat_name','cat',null,'index.php?cat=%d'),
	'xcgal'=>array('xcgal_categories','cid','parent','name','cat',null,'index.php?cat=%d'),
	'xfsection'=>array('xfs_category','id','pid','title','category',null,'index.php?category=%d'),
) ;

global $xoopsUser, $xoopsModule, $xoopsOption, $xoopsConfig;

$dirname = is_object( @$xoopsModule ) ? $xoopsModule->getVar('dirname') : '' ;
$modname = is_object( @$xoopsModule ) ? $xoopsModule->getVar('name') : '' ;

// for compatibility of 2.0.x from xoops.org
$this->assign( array(
	'xoops_modulename' => $modname ,
	'xoops_dirname' => $dirname ,
) ) ;
//-- for cache by group
$menu_cache_file = XOOPS_TRUST_PATH.'/cache/theme_'.$theme_name.'_menus_'.SITE_SALT;

// PM
if( is_object( @$xoopsUser ) ) {
	if (defined('XOOPS_CUBE_LEGACY')) {
		$url = null;
		$root = XCube_Root::getSingleton();
		$service =& $root->mServiceManager->getService('privateMessage');
		if ($service != null) {
			$client =& $root->mServiceManager->createClient($service);
			$url = $client->call('getPmInboxUrl', array('uid' => $root->mContext->mXoopsUser->get('uid')));
			if ($url != null) {
				$xugj_pm_new_count = $client->call('getCountUnreadPM', array('uid' => $root->mContext->mXoopsUser->get('uid')));
				if(intval($xugj_pm_new_count)>0){
					$root->mLanguageManager->loadModuleMessageCatalog('message');
					$xugj_pm_new_message = XCube_Utils::formatString(_MD_MESSAGE_NEWMESSAGE, $xugj_pm_new_count);
					$this->assign( 'xugj_pm_new_message' , $xugj_pm_new_message."<br/><a href='".$url."'>"._MD_MESSAGE_TEMPLATE15."</a>" ) ;
				}
				$this->assign( 'xugj_pm_new_count' , intval($xugj_pm_new_count) ) ;
				$this->assign( 'xugj_pm_inbox_url' , $url ) ;
			}
		}else{
			$pm_handler =& xoops_gethandler('privmessage' ,true) ;
			if (is_object($pm_handler)){
				$criteria = new CriteriaCompo(new Criteria('read_msg', 0));
				$criteria->add(new Criteria('to_userid', $root->mContext->mXoopsUser->get('uid')));
				$this->assign( 'pm' , array( new_messages => $pm_handler->getCount( $criteria ) ) ) ;
			}
		}
	}else{
		$pm_handler =& xoops_gethandler('privmessage') ;
		$criteria = new CriteriaCompo(new Criteria('read_msg', 0));
		$criteria->add(new Criteria('to_userid', $xoopsUser->getVar('uid')));
		$this->assign( 'pm' , array( new_messages => $pm_handler->getCount( $criteria ) ) ) ;
	}
}

// groups
if( is_object( @$xoopsUser ) ) {
	$member_handler =& xoops_gethandler( 'member' ) ;
	$groups = $member_handler->getGroupsByUser( $xoopsUser->getVar('uid') , true ) ;
	foreach( $groups as $group ) {
		$groups4assign[] = array( 'id' => $group->getVar('groupid') , 'name' => $group->getVar('name') ) ;
		$menu_cache_file .= $group->getVar('groupid');
	}
} else {
	$groups4assign[] = array( 'id' => XOOPS_GROUP_ANONYMOUS , 'name' => _GUESTS ) ;
	$menu_cache_file .= '0';
}
$this->assign( "xugj_groups" , $groups4assign ) ;
$menu_cache_file .= $GLOBALS['xoopsConfig']['language'].'.php' ;

// for speed up hack
if( ! empty( $_SESSION['redirect_message'] ) ) {
	if( empty( $this->_tpl_vars['xoops_ccblocks'] ) ) $this->_tpl_vars['xoops_ccblocks'] = array() ;
	array_unshift( $this->_tpl_vars['xoops_ccblocks'] , array( 'title' => 'Message' , 'content' => '<font color="red">'.$_SESSION['redirect_message'].'</font>' , 'weight' => 0 ) ) ;
	$this->_tpl_vars['xoops_showcblock'] = 1 ;
	$is_redirected = true ;

	$this->assign( 'redirect_message',$_SESSION['redirect_message'] ) ;
	$this->assign( 'is_redirected',TRUE ) ;
	$target_block = (isset($params['target_block']))? strtolower($params['target_block']) : 'none';
	unset( $_SESSION['redirect_message'] ) ;
}

// xoops_breadcrumbs
if( ! is_array( @$this->_tpl_vars['xoops_breadcrumbs'] ) ) {
	$breadcrumbs = array() ;
	// root controllers
	if( ! is_object( @$xoopsModule ) ) {
		$page = strrchr( $_SERVER['SCRIPT_NAME'] , '/' ) ;
		if( isset( $root_controllers[ $page ] ) ) {
			$breadcrumbs[] = $root_controllers[ $page ] ;
		}
	} else {
		// default
		$breadcrumbs[] = array( 'url' => XOOPS_URL."/modules/$dirname/" , 'name' => $modname ) ;
		if( isset( $modcat_assigns[ $dirname ] ) && strlen( $tplvar = xugj_assign_get_tpl_vars( $this , $modcat_assigns[ $dirname ][0] ) ) ) {
			// get from breadcrumbs for each modules (the second best)
			$tplvars_info = $modcat_assigns[ $dirname ] ;
			$bc_tmps = explode( $tplvars_info[1] , $tplvar ) ;
			array_shift( $bc_tmps ) ;
			foreach( $bc_tmps as $bc_tmp ) {
				if( preg_match( '#href\=([\"\']?)(.*)\\1>(.*)\<\/a\>#' , $bc_tmp , $regs ) ) {
					$breadcrumbs[] = array(
						'name' => $regs[3] ,
						'url' => $regs[2] ,
					) ;
				}
			}
			if( $tplvars_info[2] ) xugj_assign_clear_tpl_vars( $this , $tplvars_info[0] ) ;
		} else if( isset( $modcat_trees[ $dirname ] ) ) {
			// category tree (the last method)
			$tree_info = $modcat_trees[ $dirname ] ;
			if( @$_GET[ $tree_info[4] ] > 0 ) $id_val = intval( $_GET[ $tree_info[4] ] ) ;
			else if( ! empty( $tree_info[5] ) ) $id_val = xugj_assign_get_tpl_vars( $this , $tree_info[5] ) ;
			if( ! empty( $id_val ) ) $breadcrumbs = array_merge( $breadcrumbs , xugj_assign_get_breadcrumbs_by_tree( $tree_info[0] , $tree_info[1] , $tree_info[2] , $tree_info[3] , $id_val , XOOPS_URL.'/modules/'.$dirname.'/'.$tree_info[6] ) ) ;
		}
		if( ! in_array( @$this->_tpl_vars['xoops_pagetitle'] , array( $modname , $breadcrumbs[sizeof($breadcrumbs)-1]['name'] ) ) ) {
			$breadcrumbs[] = array( 'name' => $this->_tpl_vars['xoops_pagetitle'] ) ;
		}
	}
	$this->assign( "xoops_breadcrumbs" , $breadcrumbs ) ;
}

function xugj_assign_get_breadcrumbs_by_tree( $table , $id_col , $pid_col , $name_col , $id_val , $url_fmt , $paths = array() )
{
	$db =& Database::getInstance() ;

	$sql = "SELECT `$pid_col`,`$name_col` FROM ".$db->prefix($table)." WHERE `$id_col`=".intval($id_val) ;
	$result = $db->query( $sql ) ;
	if( $db->getRowsNum( $result ) == 0 ) return $paths ;
	list( $pid , $name ) = $db->fetchRow( $result ) ;
	$paths = array_merge( array( array(
		'name' => htmlspecialchars( $name , ENT_QUOTES ) ,
		'url' => sprintf( $url_fmt , $id_val ) ,
	) ) , $paths ) ;

	return xugj_assign_get_breadcrumbs_by_tree( $table , $id_col , $pid_col , $name_col , $pid , $url_fmt , $paths ) ;
}


function xugj_assign_get_tpl_vars( &$smarty , $dot_expression )
{
	$indexes = explode( '.' , $dot_expression ) ;
	$current_array = $smarty->_tpl_vars ;
	foreach( $indexes as $index ) {
		$current_array = @$current_array[ $index ] ;
	}

	return $current_array ;
}


$menus = array() ;
@include $menu_cache_file ;
if( empty( $menus ) ) {
	// cache menus
	$module_handler =& xoops_gethandler( 'module' ) ;
	$criteria = new CriteriaCompo( new Criteria( 'hasmain' , 1 ) ) ;
	$criteria->add( new Criteria( 'isactive' , 1 ) ) ;
	$criteria->add( new Criteria( 'weight' , 0 , '>' ) ) ;
	$modules =& $module_handler->getObjects( $criteria , true ) ;
	$moduleperm_handler =& xoops_gethandler( 'groupperm' ) ;
	$group_handler =& xoops_gethandler( 'group' ) ;
	$groups =& $group_handler->getObjects() ;
	$config_handler =& xoops_gethandler( 'config' ) ;

	// backup
	if( is_object( @$GLOBALS['xoopsModule'] ) ) {
		$xoopsModuleBackup =& $GLOBALS['xoopsModule'] ;
		$xoopsModuleConfigBackup =& $GLOBALS['xoopsModuleConfig'] ;
	}

	foreach( $groups as $group ) {
		$groupid = $group->getVar('groupid') ;
		$read_allowed = $moduleperm_handler->getItemIds( 'module_read' , $groupid ) ;
		foreach( $modules as $module ) {
			if( in_array( $module->getVar('mid') , $read_allowed ) ) {
				$GLOBALS['xoopsModule'] =& $module ;
				$module->loadInfo( $module->getVar('dirname') ) ;
				$sub = $module->getInfo('sub') ;
				xugj_assign_process_sub_recursively( $sub , $module->getVar('dirname') ) ;
				$menus[$groupid][ $module->getVar('dirname') ] = array(
					'name' => $module->getVar('name') ,
					'dirname' => $module->getVar('dirname') ,
					'url' => '' ,
					'sub' => $sub ,
				) ;
			}
		}
	}

	// restore
	if( is_object( @$xoopsModuleBackup ) ) {
		$GLOBALS['xoopsModule'] =& $xoopsModuleBackup ;
		$GLOBALS['xoopsModuleConfig'] =& $xoopsModuleBackup ;
	}

	ob_start() ;
	var_export( $menus ) ;
	$menus4cache = ob_get_contents() ;
	ob_end_clean() ;

	$fp = fopen( $menu_cache_file , 'wb' ) ;
	if( empty( $fp ) ) return ;
	fwrite( $fp , "<?php\n\$menus = ".$menus4cache.";\n?>" ) ;
	fclose( $fp ) ;
}


$united_menus = array() ;
foreach( $groups4assign as $group_tmp ) {
	if( is_array( @$menus[ $group_tmp['id'] ] ) ) $united_menus += $menus[ $group_tmp['id'] ] ;
}

$this->assign( 'xugj_menus' , $united_menus ) ;

function xugj_assign_process_sub_recursively( &$items , $dirname = '' , $level = 1 )
{
	if( is_array( $items ) ) foreach( array_keys( $items ) as $i ) {
		$items[$i]['dirname'] = $dirname ;
		$items[$i]['id'] = sprintf( XUGJ_ASSIGN_FMT_MENU_ID , $level , $dirname , substr( md5( $items[$i]['url'] ) , -4 ) ) ;
		$items[$i]['class'] = sprintf( XUGJ_ASSIGN_FMT_MENU_CLASS , $level , $dirname ) ;
		if( ! empty( $items[$i]['sub'] ) ) xugj_assign_process_sub_recursively( $items[$i]['sub'] , $dirname , $level + 1 ) ;
	}
}

// xugj_menu_uls
$this->assign( 'xugj_menu_uls' , xugj_assign_display_menu_ul_recursively( $united_menus ) ) ;
function xugj_assign_display_menu_ul_recursively( $level_menus , $dirname = '' )
{
	$ret = "<ul>\n" ;
	foreach( $level_menus as $menu ) {
		if( ! empty( $menu['dirname'] ) ) $dirname = $menu['dirname'] ;
		$ret .= "<li>\n<a href=\"".XOOPS_URL.'/modules/'.$dirname.'/'.$menu['url']."\">".$menu['name']."</a>\n" ;
		if( ! empty( $menu['sub'] ) && is_array( $menu['sub'] ) ) $ret .= xugj_assign_display_menu_ul_recursively( $menu['sub'] , $dirname ) ;
		$ret .= "</li>\n" ;
	}
	return $ret . "</ul>\n" ;
}
// xugj_trustdirname
$xugj_trustdirname = false;
if (isset($GLOBALS['mytrustdirname'])){
	$xugj_trustdirname = $GLOBALS['mytrustdirname'];
}elseif (is_object($GLOBALS['xoopsModule'])){
	$GLOBALS['xoopsModule']->loadInfo( $GLOBALS['xoopsModule']->getVar('dirname') ) ;
	$xugj_trustdirname = $GLOBALS['xoopsModule']->getInfo('trust_dirname') ;
}
$this->assign( 'xugj_trustdirname' , $xugj_trustdirname) ;

?>