<?php

require_once dirname(dirname(__FILE__)).'/D3pipesBlockAbstract.class.php' ;

if( file_exists( XOOPS_TRUST_PATH.'/modules/d3diary/include/d3pipesd3com.inc.php' ) ) {
	require_once XOOPS_TRUST_PATH.'/modules/d3diary/include/d3pipesd3com.inc.php' ;
	class D3pipesBlockD3diaryd3com extends D3pipesBlockD3diaryd3comSubstance {}
} else {
	class D3pipesBlockD3diaryd3com extends D3pipesBlockAbstract {}
}

?>