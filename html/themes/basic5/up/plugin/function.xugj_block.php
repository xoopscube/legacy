<?php

function smarty_function_xugj_block( $params , &$smarty )
{
	$assign_name = @$params['item'] . @$params['assign'] ;

	$block_file = XOOPS_ROOT_PATH . '/' . @$params['file'] ;

	if( file_exists( $block_file ) ) {
		include_once $block_file ;
		if( function_exists( @$params['func'] ) ) {
			$options = empty( $params['opt'] ) ? explode( '|' , @$params['options'] ) : explode( ',' , @$params['opt'] ) ;
			if( empty( $assign_name ) ) {
				$block = call_user_func( $params['func'] , $options ) ;
				if( empty( $block['content'] ) ) {
					var_dump( array_keys( $block ) ) ;
					echo 'missing "item" in <{xugj_block}>' ;
				} else {
					echo $block['content'] ;
				}
				$block['content'] ;
			} else {
				$options['disable_renderer'] = true ;
				$block = call_user_func( $params['func'] , $options ) ;
				$smarty->assign( $assign_name , $block ) ;
			}
		} else {
			echo 'invalid "func" in <{xugj_block}>' ;
			return ;
		}

	} else {
		echo 'invalid "file" in <{xugj_block}>' ;
		return ;
	}
}

?>