<?php

// corepack version
include_once(XOOPS_ROOT_PATH . '/include/corepack_version.php');

class CorePackPreload extends XCube_ActionFilter
{
	function preBlockFilter()
	{
		$this->mRoot->mDelegateManager->add( 'XoopsTpl.New' , array( $this , 'tplhook' ) , XCUBE_DELEGATE_PRIORITY_6 ) ;
	}

	function tplhook( &$xoopsTpl )
	{
		if (! defined('HYP_COMMON_PRELOAD_CONF')) {
			$xoopsConfig = $this->mRoot->mContext->mXoopsConfig;

			$target_dir = XOOPS_TRUST_PATH.'/libs/smartyplugins';
			if(is_dir($target_dir)) {
				$_1st = array_shift($xoopsTpl->plugins_dir);
				if ($_1st === $target_dir) {
					$_1st = array_shift($xoopsTpl->plugins_dir);
				}
				// regist 2nd
				array_unshift($xoopsTpl->plugins_dir, $_1st, $target_dir);
			}

			$compile_id = substr(XOOPS_URL, 7) . '-' . $xoopsConfig['template_set'] . '-' . $xoopsConfig['theme_set'] ;
			$xoopsTpl->compile_id = $compile_id ;
			$xoopsTpl->_compile_id = $compile_id ;
		}
	}
}
