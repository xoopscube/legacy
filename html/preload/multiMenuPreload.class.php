<?php
/*
 * 2011/09/09 16:45
 * Multi-Menu preload for smarty insertion to theme
 * copyright(c) Yoshi Sakai at Bluemoon inc 2011
 * GPL ver3.0 All right reserved.
 */
if (!defined('XOOPS_ROOT_PATH')) exit();

include_once XOOPS_ROOT_PATH . '/modules/multiMenu/class/getMultiMenu.class.php';

class multiMenuPreload extends XCube_ActionFilter{
	function preBlockFilter(){
		$this->mRoot->mDelegateManager->add('Legacy_RenderSystem.SetupXoopsTpl', array(&$this, 'menuSmartyAssign'));
	}
	function menuSmartyAssign(&$xoopsTpl) {
    	$module_handler = & xoops_gethandler( 'module' );
		$module =& $module_handler->getByDirname("multiMenu");
		if ( !is_object( $module ) || !$module->getVar( 'isactive' ) ) {
			return NULL;
		}
		$gmm = new getMultiMenu();
		$options=array("40");
		$block = $gmm->getblock( $options, "multimenu0" . $gmm->theme_menu() ); 
		$xoopsTpl->assign( 'multiMenuToTheme' , $block ) ;	// Insert smarty for entire site theme
	}
}
?>