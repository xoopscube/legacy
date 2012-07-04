<?php
/**
 * Smarty plugin
 */

/**
 * Include the {@link shared.make_timestamp.php} plugin
 */
/**
 * Smarty xugj_date modifier plugin
 *
 * Type:     modifier
 * Name:     xugj_date
 * Purpose:  format datestamps via date()
 * Input:
 *         - string: input date string or integer
 *         - format: format of date() for output
 *         - new1_string: message for the latest timestamp
 *         - new2_string: message for the second latest timestamp
 *         - is_uzone: is the string offsetted for user's timezone
 * @link http://www.xugj.org/
 * @author   xugj members
 * @param string or integer
 * @param string (optional)
 * @param string (optional)
 * @param string (optional)
 * @param bool (optional)
 * @return string|void
 */
function smarty_modifier_xugj_date( $string , $format = 'Y-n-j' , $new1_string = 'New!' , $new2_string = 'New' , $is_uzone = true )
{
	if( is_numeric( $string) ) {
		// specified by UNIX TIMESTAMP
		$time = intval( $string ) ;
	} else {
		// specified by format
		$time = strtotime( $string ) ;
	}

	if( $time <= 0 ) {
		$time = time() ;
	}

	$utime = $is_uzone ? $time : xoops_getUserTimestamp( $time ) ;
	$unow = xoops_getUserTimestamp( time() ) ;

	$new_marks = '' ;
	if( $new1_string ) {
		if( $utime > $unow - 1 * 86400 ) {
			$new_marks = '<span class="new1">' . $new1_string . '</span>' ;
		} else if( $new2_string ) {
			if( $utime > $unow - 7 * 86400 ) {
				$new_marks = '<span class="new2">' . $new2_string . '</span>' ;
			}
		}
	}

	return date( $format , $utime ) . $new_marks ;
}
?>
