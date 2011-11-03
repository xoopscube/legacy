<?php

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

class HypXoopsTplHook extends XCube_ActionFilter
{
	function preBlockFilter()
	{
		$this->mRoot->mDelegateManager->add( 'XoopsTpl.New' , array( $this , 'hook' ) , XCUBE_DELEGATE_PRIORITY_6 ) ;
	}

	function hook( &$xoopsTpl )
	{
		global $xoopsConfig ;

		$target_dir = XOOPS_TRUST_PATH.'/libs/smartyplugins';
		if(is_dir($target_dir)) {
			$_1st = array_shift($xoopsTpl->plugins_dir);
			if (defined('LEGACY_BASE_VERSION') && version_compare(LEGACY_BASE_VERSION, '2.2.1.0', '>=')) {

				// XCL >= 2.2.1 (Revision >= 982 Feature Request #3165296 - Replace resource.db.php with HD version)
				// see http://xoopscube.svn.sourceforge.net/viewvc/xoopscube/Package_Legacy/branches/r2_2_00-branch/xoops_trust_path/libs/smarty/plugins/resource.db.php?revision=982&view=markup

				if ($_1st === $target_dir) {
					$_1st = array_shift($xoopsTpl->plugins_dir);
				}
				// regist 2nd
				array_unshift($xoopsTpl->plugins_dir, $_1st, $target_dir);
			} else {
				// regist first
				if ($_1st !== $target_dir) {
					array_unshift($xoopsTpl->plugins_dir, $target_dir, $_1st);
				} else {
					array_unshift($xoopsTpl->plugins_dir, $_1st) ;
				}
			}
		}
		$compile_id = substr(XOOPS_URL, 7) . '-' . $xoopsConfig['template_set'] . '-' . $xoopsConfig['theme_set'] ;
		$xoopsTpl->compile_id = $compile_id ;
		$xoopsTpl->_compile_id = $compile_id ;
	}
}
