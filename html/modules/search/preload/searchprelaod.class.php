<?php

/**
* Override search preload
*/

if (!class_exists('Search_Preload_BASE')) {
	class Search_Preload_BASE extends XCube_ActionFilter
	{
		function preBlockFilter()
		{
			$root =& XCube_Root::getSingleton();
			$root->mDelegateManager->delete('Legacypage.Search.Access','Legacy_EventFunction::search');
			$root->mDelegateManager->add('Legacypage.Search.Access',
										 array($this, 'overRideDefaultSearch'),
										 XCUBE_DELEGATE_PRIORITY_FIRST);
		}

		function overRideDefaultSearch()
		{
			$query = isset($_SERVER['QUERY_STRING'])? $_SERVER['QUERY_STRING'] : '';
			if ($query) {
				$query = '?' . $query;
				if (isset($_REQUEST['showcontext'])){
					$query .= '&showcontext=' . ($_REQUEST['showcontext']? '1' : '0') ;
				}else{
					$query .= '&showcontext=1' ;
				}
			}
			$mydirname = basename(dirname(dirname(__FILE__)));
			while(ob_get_level()) {
				if (! ob_end_clean()) break;
			}
			header('Location: '.XOOPS_URL.'/modules/'.$mydirname.'/index.php' . $query);
			exit();

		}
	}
}
eval('class '.ucfirst(basename(dirname(dirname(__FILE__)))).'_searchprelaod extends Search_Preload_BASE{}');

