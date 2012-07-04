<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     d3comment
 * Version:  1.0
 * Date:     
 * Author:   GIJOE
 * Purpose:  
 * Input:    
 * 
 * Examples: {d3comment class=(class_name) mydirname=(dirname)}
 * -------------------------------------------------------------
 */
function smarty_function_d3comment($params, &$smarty)
{
	$forum_dirpath = XOOPS_TRUST_PATH.'/modules/d3forum' ;
	require_once $forum_dirpath.'/class/D3commentObj.class.php' ;

	$mydirname = @$params['mydirname'] ;
	$classname = @$params['class'] ;

	$mytrustdirname = '' ;
	if( $mydirname != '' ) {
		@include XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/mytrustdirname.php' ;
	}
	$params['mytrustdirname'] = $mytrustdirname ;

	$class_bases = array(
		XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/class' ,
		XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/class' ,
		XOOPS_TRUST_PATH.'/modules/d3forum/class' ,
	) ;

	foreach( $class_bases as $class_base ) {
		if( file_exists( $class_base.'/'.$classname.'.class.php' ) ) {
			require_once $class_base.'/'.$classname.'.class.php' ;
			break ;
		}
	}

	$m_params['forum_dirname'] = $forum_dirname ;

	$m_params['external_dirname'] = $mydirname  ; 
	$m_params['classname'] = $classname ;
	$m_params['external_trustdirname'] = $mytrustdirname ;

	if( class_exists( $classname ) ) {
		$obj =& D3commentObj::getInstance ( $m_params ) ;
		$obj->d3comObj->setSmarty( $smarty ) ;
		switch( $params['mode'] ) {
			case 'count' :
				$obj->d3comObj->displayCommentsCount( $params ) ;
				break ;
			case 'display_inline' :
			default :
				$obj->d3comObj->displayCommentsInline( $params ) ;
				break ;
		}
	} else {
		echo "class parameter is invalid in <{d3comment}>" ;
	}
}

?>