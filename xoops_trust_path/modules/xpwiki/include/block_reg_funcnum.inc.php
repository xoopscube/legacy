<?php
/*
 * Created on 2008/02/08 by nao-pon http://hypweb.net/
 * $Id: block_reg_funcnum.inc.php,v 1.2 2008/02/08 03:12:06 nao-pon Exp $
 */

$db =& Database::getInstance() ;
$query = "SELECT mid FROM ".$db->prefix('modules')." WHERE dirname='".$modversion['dirname']."' ";
$result = $db->query($query);
$record= $db->fetcharray($result);
if ($record) {
	$mid = $record['mid'];

	$sql = "SELECT * FROM ".$db->prefix('newblocks')." WHERE mid=".$mid.' AND block_type <>\'D\' ORDER BY `bid` ASC';
	$fresult = $db->query($sql);
	$done = array();
	while ($fblock = $db->fetchArray($fresult)) {
		$i = 1;
		$fblock['func_num'] = intval($fblock['func_num']);
		while(isset($modversion['blocks'][$i])) {
			if ($modversion['blocks'][$i]['name'] === $fblock['name']) {
				if (isset($done[$fblock['name']])) {
					if (! $fblock['visible']) {
						// not useing -> delete
						$sql = 'DELETE FROM `'.$db->prefix('newblocks').'` WHERE `bid` = '.$fblock['bid'].' LIMIT 1';
						if ($db->query($sql)) {
							//$local_msgs[] = 'Deleted ( '.$fblock['bid'].' ) from "newblocks"';
						} else {
							//$local_msgs[] = 'mysql Error: '.$sql;
						}
						$sql = 'DELETE FROM `'.$db->prefix('block_module_link').'` WHERE `bid` = '.$fblock['bid'].' LIMIT 1';
						if ($db->query($sql)) {
							//$local_msgs[] = 'Deleted ( '.$fblock['bid'].' ) from "block_module_link"';
						} else {
							//$local_msgs[] = 'mysql Error: '.$sql;
						}
						$sql = 'DELETE FROM `'.$db->prefix('group_permission').'` WHERE `gperm_itemid` = '.$fblock['bid'].' AND `gperm_name` = \'block_read\' LIMIT 1';
						if ($db->query($sql)) {
							//$local_msgs[] = 'Deleted ( '.$fblock['bid'].' ) from "group_permission"';
						} else {
							//$local_msgs[] = 'mysql Error: '.$sql;
						}
					}
					if ($i !== $fblock['func_num']) {
						// using -> change to type "D"
						$sql = 'UPDATE `'.$db->prefix('newblocks').'` SET `func_num` = \'255\', `block_type` = \'D\' WHERE `bid` = '.$fblock['bid'].' LIMIT 1';
						if ($db->query($sql)) {
							//$local_msgs[] = 'Changed block type to "D" ( '.$fblock['bid'].' )';
						} else {
							//$local_msgs[] = 'mysql Error: '.$sql;
						}
					}
				} else {
					$done[$fblock['name']] = TRUE;
					if ($i !== $fblock['func_num']) {
						$sql = 'UPDATE `'.$db->prefix('newblocks').'` SET `func_num` = '.$i.' WHERE `bid` = '.$fblock['bid'].' LIMIT 1';
						if ($db->query($sql)) {
							//$local_msgs[] = 'Changed block "func_num" to "'.$i.'" ( '.$fblock['bid'].' )';
						} else {
							//$local_msgs[] = 'mysql Error: '.$sql;
						}
					}
				}
				break;
			}
			$i++;
		}
	}
}

//global $msgs;
//if( isset( $msgs ) && ! empty( $local_msgs ) ) {
//	$msgs = array_merge( $msgs , $local_msgs ) ;
//}
