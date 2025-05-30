<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * Keep Block option values when update
 * @package    Altsys
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

global $xoopsDB;
$query  = 'SELECT mid FROM ' . $xoopsDB->prefix( 'modules' ) . " WHERE dirname='" . $modversion['dirname'] . "' ";
$result = $xoopsDB->query( $query );
$record = $xoopsDB->fetchArray( $result );
if ( $record ) {
	$mid   = $record['mid'];
	$count = is_countable($modversion['blocks']) ? count( $modversion['blocks'] ) : 0;


	$sql     = 'SELECT * FROM ' . $xoopsDB->prefix( 'newblocks' ) . ' WHERE mid=' . $mid . " AND block_type <>'D' AND func_num > $count";
	$fresult = $xoopsDB->query( $sql );

	while ( false !== ( $fblock = $xoopsDB->fetchArray( $fresult ) ) ) {
		$local_msgs[] = 'Non Defined Block <b>' . $fblock['name'] . '</b> will be deleted';
		$sql          = 'DELETE FROM ' . $xoopsDB->prefix( 'newblocks' ) . " WHERE bid='" . $fblock['bid'] . "'";
		$iret         = $xoopsDB->query( $sql );
	}

	for ( $i = 1; $i <= $count; ++ $i ) {
		$sql     = 'SELECT name,options FROM ' . $xoopsDB->prefix( 'newblocks' ) . ' WHERE mid=' . $mid . ' AND func_num=' . $i . " AND show_func='" . addslashes( $modversion['blocks'][ $i ]['show_func'] ) . "' AND func_file='" . addslashes( $modversion['blocks'][ $i ]['file'] ) . "'";
		$fresult = $xoopsDB->query( $sql );
		$fblock  = $xoopsDB->fetchArray( $fresult );
		if ( isset( $fblock['options'] ) ) {
			$old_vals = explode( '|', $fblock['options'] );

			$def_vals = explode( '|', $modversion['blocks'][ $i ]['options'] );

			if ( count( $old_vals ) == count( $def_vals ) ) {
				$modversion['blocks'][ $i ]['options'] = $fblock['options'];

				$local_msgs[] = "Option's values of the block <b>" . $fblock['name'] . '</b> will be kept. (value = <b>' . $fblock['options'] . '</b>)';
			} elseif ( count( $old_vals ) < count( $def_vals ) ) {
				for ( $j = 0; $j < count( $old_vals ); $j ++ ) {
					$def_vals[ $j ] = $old_vals[ $j ];
				}
				$modversion['blocks'][ $i ]['options'] = implode( '|', $def_vals );

				$local_msgs[] = "Option's values of the block <b>" . $fblock['name'] . '</b> will be kept and new option(s) are added. (value = <b>' . $modversion['blocks'][ $i ]['options'] . '</b>)';
			} else {
				$local_msgs[] = "Option's values of the block <b>" . $fblock['name'] . '</b> will be reset to the default, because of some decrease of options. (value = <b>' . $modversion['blocks'][ $i ]['options'] . '</b>)';
			}
		}
	}
}

global $msgs, $myblocksadmin_parsed_updateblock;
if ( ! empty( $msgs ) && ! empty( $local_msgs ) && empty( $myblocksadmin_parsed_updateblock ) ) {
	$msgs = array_merge( $msgs, $local_msgs );

	$myblocksadmin_parsed_updateblock = true;
}
