<?php
/**
 * Altsys library (UI-Components) for D3 modules
 *
 * @package    Altsys
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

/**
 * @param      $DB
 * @param      $gperm_modid
 * @param null $gperm_name
 * @param null $gperm_itemid
 *
 * @return bool
 */
function myDeleteByModule( $DB, $gperm_modid, $gperm_name = null, $gperm_itemid = null ) {
	$criteria = new CriteriaCompo( new Criteria( 'gperm_modid', (int) $gperm_modid ) );

	if ( isset( $gperm_name ) ) {
		$criteria->add( new Criteria( 'gperm_name', $gperm_name ) );

		if ( isset( $gperm_itemid ) ) {
			$criteria->add( new Criteria( 'gperm_itemid', (int) $gperm_itemid ) );
		}
	}

	$sql = 'DELETE FROM ' . $DB->prefix( 'group_permission' ) . ' ' . $criteria->renderWhere();

	if ( ! $result = $DB->query( $sql ) ) {
		return false;
	}

	return true;
}


// include '../../../include/cp_header.php'; GIJ
$modid = isset( $_POST['modid'] ) ? (int) $_POST['modid'] : 1;

if ( 1 == $modid ) {
	// check by the permission of eather 'altsys' or 'system'
	$module_handler = xoops_gethandler( 'module' );

	$module =& $module_handler->getByDirname( 'altsys' );

	if ( ! is_object( $module ) ) {
		$module = $module_handler->getByDirname( 'system' );
		if ( ! is_object( $module ) ) {

			die( 'there is no AltSys UI Components nor base system.' );
		}
	}

	$moduleperm_handler = xoops_gethandler( 'groupperm' );
	if ( ! is_object( @$GLOBALS['xoopsUser'] ) || ! $moduleperm_handler->checkRight( 'module_admin', $module->getVar( 'mid' ), $GLOBALS['xoopsUser']->getGroups() ) ) {
		die( 'only admin of UI Components can access this area' );
	}
} else {
	// check the permission of 'module_admin' of the module
	if ( $modid <= 0 || ! is_object( $GLOBALS['xoopsUser'] ) || ! $GLOBALS['xoopsUser']->isAdmin( $modid ) ) {
		die( _NOPERM );
	}
	$module_handler = xoops_gethandler( 'module' );

	$module = $module_handler->get( $modid );
	if ( ! is_object( $module ) || ! $module->getVar( 'isactive' ) ) {
		die( _MODULENOEXIST );
	}
}

$member_handler = xoops_gethandler( 'member' );
$group_list     = $member_handler->getGroupList();
if ( ! empty( $_POST['perms'] ) && is_array( $_POST['perms'] ) ) {

	if ( ! isset( $msg ) || ! is_array( $msg ) ) {
		$msg = isset( $msg ) ? [ $msg ] : [];
	}
	$gperm_handler = xoops_gethandler( 'groupperm' );

	foreach ( $_POST['perms'] as $perm_name => $perm_data ) {

		foreach ( $perm_data['itemname'] as $item_id => $item_name ) {
			// checking code
			// echo "<pre>" ;
			// var_dump( $_POST['perms'] ) ;
			// exit ;
			if ( false != myDeleteByModule( $gperm_handler->db, $modid, $perm_name, $item_id ) ) {
				if ( empty( $perm_data['groups'] ) ) {
					continue;
				}
				foreach ( $perm_data['groups'] as $group_id => $item_ids ) {
					//				foreach ($item_ids as $item_id => $selected) {
					$selected = $item_ids[ $item_id ] ?? 0;
					if ( 1 == $selected ) {
						// make sure that all parent ids are selected as well
						if ( '' != $perm_data['parents'][ $item_id ] ) {
							$parent_ids = explode( ':', $perm_data['parents'][ $item_id ] );
							foreach ( $parent_ids as $pid ) {
								if ( 0 != $pid && ! in_array( $pid, array_keys( $item_ids ) ) ) {
									// one of the parent items were not selected, so skip this item
									$msg[] = sprintf( _MD_A_MYBLOCKSADMIN_PERMADDNG, '<b>' . $perm_name . '</b>', '<b>' . $perm_data['itemname'][ $item_id ] . '</b>', '<b>' . $group_list[ $group_id ] . '</b>' ) . ' (' . _MD_A_MYBLOCKSADMIN_PERMADDNGP . ')';
									continue 2;
								}
							}
						}
						$gperm = $gperm_handler->create();
						$gperm->setVar( 'gperm_groupid', $group_id );
						$gperm->setVar( 'gperm_name', $perm_name );
						$gperm->setVar( 'gperm_modid', $modid );
						$gperm->setVar( 'gperm_itemid', $item_id );

						if ( ! $gperm_handler->insert( $gperm ) ) {
							$msg[] = sprintf( _MD_A_MYBLOCKSADMIN_PERMADDNG, '<b>' . $perm_name . '</b>', '<b>' . $perm_data['itemname'][ $item_id ] . '</b>', '<b>' . $group_list[ $group_id ] . '</b>' );
						} else {
							$msg[] = sprintf( _MD_A_MYBLOCKSADMIN_PERMADDOK, '<b>' . $perm_name . '</b>', '<b>' . $perm_data['itemname'][ $item_id ] . '</b>', '<b>' . $group_list[ $group_id ] . '</b>' );
						}
						unset( $gperm );
					}
				}
			} else {
				$msg[] = sprintf( _MD_A_MYBLOCKSADMIN_PERMRESETNG, $module->getVar( 'name' ) );
			}
		}
	}
}

