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

	if( class_exists( $classname ) ) {
		$d3com =& new $classname( '' , $mydirname , $mytrustdirname ) ;
		$d3com->setSmarty( $smarty ) ;
		switch( $params['mode'] ) {
			case 'count' :
				$d3com->displayCommentsCount( $params ) ;
				break ;
			case 'display_inline' :
			default :
				$d3com->displayCommentsInline( $params ) ;
				break ;
		}
	} else {
		echo "class parameter is invalid in <{d3comment}>" ;
	}
}

?>