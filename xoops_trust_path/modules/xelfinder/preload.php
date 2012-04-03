<?php

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

if( ! preg_match( '/^[0-9a-zA-Z_-]+$/' , $mydirname ) ) exit ;

if( ! class_exists( 'xelfinderPreloadBase' ) ) {

class xelfinderPreloadBase extends XCube_ActionFilter {
	function preBlockFilter() {
		$root =& XCube_Root::getSingleton();
		$root->mDelegateManager->delete('Legacypage.Imagemanager.Access','Legacy_EventFunction::imageManager');
		$root->mDelegateManager->add('Legacypage.Imagemanager.Access',
									array($this, 'overRideDefaultImageManager'),
									XCUBE_DELEGATE_PRIORITY_FIRST);
		$this->mRoot->mDelegateManager->add('Legacy_TextFilter.MakeXCodeConvertTable',
									array($this, 'addXCodeConvertTable'),
									XCUBE_DELEGATE_PRIORITY_NORMAL - 1);
	}

	function overRideDefaultImageManager() {
		$mydirname = $this->mydirname;
		$mydirpath = $this->mydirpath;
		require dirname(__FILE__).'/manager.php';
	}
	
	function addXCodeConvertTable(&$patterns, &$replacements) {
		$patterns[] = '/\[siteimg align=([\'"]?)(left|center|right)\\1]([^"\(\)\'<>]*)\[\/siteimg\]/U';
		$rep = '<img src="'.XOOPS_URL.'/\\3" align="\\2" alt="" />';
		$replacements[0][] = $rep;
		$replacements[1][] = $rep;
		
		$patterns[] = '/\[siteimg]([^"\(\)\'<>]*)\[\/siteimg\]/U';
		$rep = '<img src="'.XOOPS_URL.'/\\1" alt="" />';
		$replacements[0][] = $rep;
		$replacements[1][] = $rep;
	}
}

}

eval( 'class '.ucfirst( $mydirname ).'_xelfinderPreload extends xelfinderPreloadBase { var $mydirname = \''.$mydirname.'\' ; var $mydirpath = \''.$mydirpath.'\' ; }' ) ;