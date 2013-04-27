<?php
// Use X-update install checker
define('LEGACY_INSTALLERCHECKER_ACTIVE', false);

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
	
	public static function ini_string_to_bytes($val) {
		$val = trim(strval($val));
		if ($val === '-1') $val = 0;
		if ($val) {
			// for ex. 1mb, 1KB
			$val = rtrim($val, 'bB');
			$last = strtolower(substr($val, -1));
			switch($last) {
				case 't':
					$val *= 1024;
				case 'g':
					$val *= 1024;
				case 'm':
					$val *= 1024;
				case 'k':
					$val *= 1024;
			}
			$val = floor($val);
		}
		return $val;
	}
	
	public static function setMemoryLimit() {
		$memory_limit = self::ini_string_to_bytes(ini_get('memory_limit'));
		if ($memory_limit < 33554432) {
			@ ini_set('memory_limit' ,'32M');
		}
	}
}

// set memory_limit
CorePackPreload::setMemoryLimit();
