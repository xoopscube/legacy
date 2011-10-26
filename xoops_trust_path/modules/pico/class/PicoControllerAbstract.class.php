<?php

class PicoControllerAbstract {

var $mydirname = '' ;
var $mytrustdirname = 'pico' ;
var $assign = array() ;
var $mod_config = array() ;
var $uid = 0 ;
var $currentCategoryObj = null ;
var $permissions = array() ;
var $is_need_header_footer = true ;
var $template_name = '' ;
var $html_header = '' ;
var $contentObjs = array() ;

function PicoControllerAbstract( &$currentCategoryObj )
{
	global $xoopsUser ;

	$this->currentCategoryObj =& $currentCategoryObj ;
	$this->mydirname = $currentCategoryObj->mydirname ;
	$this->mod_config = $currentCategoryObj->getOverriddenModConfig() ;
	$this->uid = is_object( $xoopsUser ) ? $xoopsUser->getVar('uid') : 0 ;

	$picoPermission =& PicoPermission::getInstance() ;
	$this->permissions = $picoPermission->getPermissions( $this->mydirname ) ;
	$this->assign = array(
		'mymodname' => htmlspecialchars( $currentCategoryObj->mod_name , ENT_QUOTES ) ,
		'mydirname' => $this->mydirname ,
		'mytrustdirname' => $this->mytrustdirname ,
		'mod_url' => XOOPS_URL.'/modules/'.$this->mydirname ,
		'mod_imageurl' => XOOPS_URL.'/modules/'.$this->mydirname.'/'.$this->mod_config['images_dir'] ,
		'xoops_config' => $GLOBALS['xoopsConfig'] ,
		'mod_config' => $this->mod_config ,
		'uid' => $this->uid ,
	) ;
	$this->template_name = $this->mydirname.'_index.html' ;
}

function execute( $request )
{
	// abstract (must override it)
}

function render( $target = null )
{
	require_once XOOPS_ROOT_PATH.'/class/template.php' ;
	$tpl = new XoopsTpl() ;
	$tpl->assign( $this->getAssign() ) ;
	$tpl->assign( 'xoops_module_header' , pico_main_render_moduleheader( $this->mydirname , $GLOBALS['xoopsModuleConfig'] , $this->getHtmlHeader() ) ) ;
	$tpl->display( $this->getTemplateName() ) ;
}

function isNeedHeaderFooter()
{
	return $this->is_need_header_footer ;
}

function getTemplateName()
{
	$template_name = $this->template_name ;

	// calling a delegate for replacing the main template
	if( class_exists( 'XCube_DelegateUtils' ) ) {
		XCube_DelegateUtils::raiseEvent( 'ModuleClass.Pico.Controller.GetTemplateName' , $this->mydirname , new XCube_Ref( $template_name ) ) ;
	}

	return $template_name ;
}

function getAssign()
{
	foreach( $this->contentObjs as $index => $contentObj ) {
		if( ! is_object( $contentObj ) ) continue ;
		if( $contentObj->need_filter_body ) {
			$this->assign[$index]['body'] = $contentObj->filterBody( $contentObj->getData4html() ) ;
		}
	}

	return $this->assign ;
}

function getHtmlHeader()
{
	return $this->html_header ;
}





}

?>