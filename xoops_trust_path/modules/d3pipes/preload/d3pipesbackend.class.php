<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

if (defined('XOOPS_CUBE_LEGACY')) {
	if (!class_exists('d3pipesBackendPreloadBase')) {
		class d3pipesBackendPreloadBase extends XCube_ActionFilter
		{
			function postFilter() {
				$this->mController->mRoot->mDelegateManager->add('Legacy_BackendAction.GetRSSItems', array( &$this , 'getRSSItems' )) ;

			}
			function getRSSItems(&$items) {
				
				$mydirname = $this->mydirname;

				$module_handler =& xoops_gethandler('module');
				$xoopsModule =& $module_handler->getByDirname($mydirname);
				$mid = $xoopsModule->getVar('mid');
				$config_handler =& xoops_gethandler('config');
				$xoopsModuleConfig =& $config_handler->getConfigsByCat(0, $mid);

				$pipe_id = isset($xoopsModuleConfig['backend_pipe_id'])? (int)$xoopsModuleConfig['backend_pipe_id'] : 0;				
				if (! $pipe_id) return;
				
				require_once dirname(dirname(__FILE__)) . '/include/common_prepend.inc.php';
				// single pipe
				$pipe4assign = d3pipes_common_get_pipe4assign( $mydirname , $pipe_id ) ;
				if( empty( $pipe4assign['main_rss'] ) ) {
					redirect_header( XOOPS_URL.'/modules/'.$mydirname.'/' , 3 , _MD_D3PIPES_ERR_INVALIDPIPEID ) ;
					exit ;
				}
				// fetch entries
				$entries = d3pipes_common_fetch_entries( $mydirname , $pipe4assign , $xoopsModuleConfig['entries_per_rss'] , $errors , $xoopsModuleConfig ) ;
				
				foreach ($entries as $entry) {
					$items[] = array (
						'title'		  => $entry['headline'],
						'link'		  => $entry['link'],
						'guid'		  => $entry['fingerprint'],
						'pubdate'	  => $entry['pubtime'],
						'description' => $entry['description'],
						'category'    => $entry['pipe']['name4xml'],
					);
				}
			}
		}
	}
	eval('class '.ucfirst($mydirname).'_d3pipesBackend extends d3pipesBackendPreloadBase{ var $mydirname = "'.$mydirname.'" ; }');
}
