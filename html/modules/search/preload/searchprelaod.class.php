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
			$myts =& MyTextSanitizer::getInstance();
			$action	= isset($_REQUEST['action']) 	? $myts->stripSlashesGPC($_REQUEST['action']) 	: "search";
			$query	= isset($_REQUEST['query']) 	? $myts->stripSlashesGPC($_REQUEST['query']) 	: "";
			if (isset($_REQUEST['showcontext'])){
				$showcontext=  "" ;
			}else{
				$showcontext=  "&showcontext=1" ;
			}
			$mydirname = basename(dirname(dirname(__FILE__)));
			if (empty($query) && $action == "results"){
				header("Location: ".XOOPS_URL."/modules/".$mydirname."/index.php");
				exit();
			}else{
				$query = rawurlencode($query);
				header("Location: ".XOOPS_URL."/modules/".$mydirname."/index.php?".$query.$showcontext);
				exit();
			}

		}
	}
}
eval('class '.ucfirst(basename(dirname(dirname(__FILE__)))).'_searchprelaod extends Search_Preload_BASE{}');


?>