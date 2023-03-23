<?php
/**
 * D3Forum module for XCL
 *
 * @package    D3Forum
 * @version    XCL 2.3.3
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 * @brief      Keep Block option values when update (by nobunobu) for XOOPS 2.x
 */

$db = XoopsDatabaseFactory::getDatabaseConnection();

$query = 'SELECT mid FROM ' . $db->prefix( 'modules' ) . " WHERE dirname='" . $modversion['dirname'] . "' ";

$result = $db->query( $query );

$record = $db->fetcharray( $result );

if ( $record ) {

	$mid = $record['mid'];

	$count = count( $modversion['blocks'] );

	$sql = 'SELECT * FROM ' . $db->prefix( 'newblocks' ) . ' WHERE mid=' . $mid . " AND block_type <>'D' AND func_num > $count";

	$fresult = $db->query( $sql );

	while ( $fblock = $db->fetchArray( $fresult ) ) {
		$local_msgs[] = 'Non Defined Block <b>' . $fblock['name'] . '</b> will be deleted';
		$sql          = 'DELETE FROM ' . $db->prefix( 'newblocks' ) . " WHERE bid='" . $fblock['bid'] . "'";
		$iret         = $db->query( $sql );
	}

	for ( $i = 1; $i <= $count; $i ++ ) {

		$sql = 'SELECT name,options FROM ' . $db->prefix( 'newblocks' ) . ' WHERE mid=' . $mid . ' AND func_num=' . $i . ' AND show_func=' . $db->quoteString( $modversion['blocks'][ $i ]['show_func'] ) . ' AND func_file=' . $db->quoteString( $modversion['blocks'][ $i ]['file'] );

		$fresult = $db->query( $sql );

		$fblock = $db->fetchArray( $fresult );

		if ( isset( $fblock['options'] ) ) {

			$old_vals = explode( '|', $fblock['options'] );

			$def_vals = explode( '|', $modversion['blocks'][ $i ]['options'] );

			if ( count( $old_vals ) === count( $def_vals ) ) {

				$modversion['blocks'][ $i ]['options'] = $fblock['options'];

				$local_msgs[] = "Option's values of the block <b>" . $fblock['name'] . '</b> will be kept. (value = <b>' . $fblock['options'] . '</b>)';

			} else if ( count( $old_vals ) < count( $def_vals ) ) {

				for ( $j = 0, $jMax = count( $old_vals ); $j < $jMax; $j ++ ) {
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
	$msgs                             = array_merge( $msgs, $local_msgs );
	$myblocksadmin_parsed_updateblock = true;
}
