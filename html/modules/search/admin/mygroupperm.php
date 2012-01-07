<?php

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

function myDeleteByModule($DB, $gperm_modid, $gperm_name = null, $gperm_itemid = null)
{
	$criteria = new CriteriaCompo(new Criteria('gperm_modid', intval($gperm_modid)));
	if (isset($gperm_name)) {
		$criteria->add(new Criteria('gperm_name', $gperm_name));
		if (isset($gperm_itemid)) {
			$criteria->add(new Criteria('gperm_itemid', intval($gperm_itemid)));
		}
	}
	$sql = "DELETE FROM ".$DB->prefix('group_permission').' '.$criteria->renderWhere();
	if (!$result = $DB->query($sql)) {
		return false;
	}
	return true;
}



// include '../../../include/cp_header.php'; GIJ
$modid = isset($_POST['modid']) ? intval($_POST['modid']) : 1;
// we dont want system module permissions to be changed here ( 1 -> 0 GIJ)
if ($modid <= 0 || !is_object($xoopsUser) || !$xoopsUser->isAdmin($modid)) {
	redirect_header(XOOPS_URL.'/user.php', 1, _NOPERM);
	exit();
}
$module_handler =& xoops_gethandler('module');
$module =& $module_handler->get($modid);
if (!is_object($module) || !$module->getVar('isactive')) {
	redirect_header(XOOPS_URL.'/admin.php', 1, _MODULENOEXIST);
	exit();
}
$member_handler =& xoops_gethandler('member');
$group_list =& $member_handler->getGroupList();
if (is_array($_POST['perms']) && !empty($_POST['perms'])) {
	$gperm_handler = xoops_gethandler('groupperm');
	foreach ($_POST['perms'] as $perm_name => $perm_data) {
		foreach( $perm_data['itemname' ] as $item_id => $item_name ) {
			// checking code
			// echo "<pre>" ;
			// var_dump( $_POST['perms'] ) ;
			// exit ;
			if (false != myDeleteByModule($gperm_handler->db,$modid,$perm_name,$item_id)) {
				if( empty( $perm_data['groups'] ) ) continue ;
				foreach ($perm_data['groups'] as $group_id => $item_ids) {
	//				foreach ($item_ids as $item_id => $selected) {
					$selected = isset( $item_ids[ $item_id ] ) ? $item_ids[ $item_id ] : 0 ;
					if ($selected == 1) {
						// make sure that all parent ids are selected as well
						if ($perm_data['parents'][$item_id] != '') {
							$parent_ids = explode(':', $perm_data['parents'][$item_id]);
							foreach ($parent_ids as $pid) {
								if ($pid != 0 && !in_array($pid, array_keys($item_ids))) {
									// one of the parent items were not selected, so skip this item
									$msg[] = sprintf(_MD_AM_PERMADDNG, '<b>'.$perm_name.'</b>', '<b>'.$perm_data['itemname'][$item_id].'</b>', '<b>'.$group_list[$group_id].'</b>').' ('._MD_AM_PERMADDNGP.')';
									continue 2;
								}
							}
						}
						$gperm =& $gperm_handler->create();
						$gperm->setVar('gperm_groupid', $group_id);
						$gperm->setVar('gperm_name', $perm_name);
						$gperm->setVar('gperm_modid', $modid);
						$gperm->setVar('gperm_itemid', $item_id);
						if (!$gperm_handler->insert($gperm)) {
							$msg[] = sprintf(_MD_AM_PERMADDNG, '<b>'.$perm_name.'</b>', '<b>'.$perm_data['itemname'][$item_id].'</b>', '<b>'.$group_list[$group_id].'</b>');
						} else {
							$msg[] = sprintf(_MD_AM_PERMADDOK, '<b>'.$perm_name.'</b>', '<b>'.$perm_data['itemname'][$item_id].'</b>', '<b>'.$group_list[$group_id].'</b>');
						}
						unset($gperm);
					}
				}
			} else {
				$msg[] = sprintf(_MD_AM_PERMRESETNG, $module->getVar('name'));
			}
		}
	}
}
/*
$backlink = XOOPS_URL.'/admin.php';
if ($module->getVar('hasadmin')) {
	$adminindex = $module->getInfo('adminindex');
	if ($adminindex) {
		$backlink = XOOPS_URL.'/modules/'.$module->getVar('dirname').'/'.$adminindex;
	}
}

$msg[] = '<br /><br /><a href="'.$backlink.'">'._BACK.'</a>';
xoops_cp_header();
xoops_result($msg);
xoops_cp_footer();  GIJ */
?>