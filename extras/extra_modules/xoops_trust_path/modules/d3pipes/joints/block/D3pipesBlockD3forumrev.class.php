<?php

require_once dirname(dirname(__FILE__)).'/D3pipesBlockAbstract.class.php' ;

if( file_exists( XOOPS_TRUST_PATH.'/modules/d3forum/include/d3pipesd3forumrev.inc.php' ) ) {
	require_once XOOPS_TRUST_PATH.'/modules/d3forum/include/d3pipesd3forumrev.inc.php' ;
	class D3pipesBlockD3forumrev extends D3pipesBlockD3forumrevSubstance {}
} else {
	class D3pipesBlockD3forumrev extends D3pipesBlockAbstract {}
}

?>