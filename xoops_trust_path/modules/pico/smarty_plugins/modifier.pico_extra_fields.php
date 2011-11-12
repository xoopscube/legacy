<?php

/**
 * Smarty plugin
 */


/**
 * Smarty pico_extra_fields modifier plugin
 *
 * Type:     modifier
 * Name:     extra_fields
 * Usage:    {"(field_name)"|pico_extra_fields:$content}
 * @link 
 * @author   
 * @param string
 * @return string
 */

require_once XOOPS_TRUST_PATH.'/modules/pico/include/common_functions.php' ;

function smarty_modifier_pico_extra_fields( $key = '' , $content_row )
{
	$extra_fields = pico_common_unserialize( @$content_row['extra_fields'] ) ;
	return empty( $key ) ? $extra_fields : @$extra_fields[ $key ] ;
}

?>