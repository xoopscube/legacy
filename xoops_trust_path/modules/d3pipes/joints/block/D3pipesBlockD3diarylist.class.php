<?php

require_once dirname(dirname(__FILE__)).'/D3pipesBlockAbstract.class.php' ;

if( file_exists( XOOPS_TRUST_PATH.'/modules/d3diary/include/d3pipes.inc.php' ) ) {
	require_once XOOPS_TRUST_PATH.'/modules/d3diary/include/d3pipes.inc.php' ;
	class D3pipesBlockD3diarylist extends D3pipesBlockD3diarylistSubstance {}
} else {
	class D3pipesBlockD3diarylist extends D3pipesBlockAbstract {}
}

?>