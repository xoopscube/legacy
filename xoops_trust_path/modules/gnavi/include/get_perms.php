<?php
$global_perms = 0 ;
if( is_object( $xoopsDB ) ) {
	if( ! is_object( $xoopsUser ) ) {
		$whr_groupid = "GPERM_groupid=".XOOPS_GROUP_ANONYMOUS ;
	} else {
		$groups = $xoopsUser->getGroups() ;
		$whr_groupid = "GPERM_groupid IN (" ;
		foreach( $groups as $groupid ) {
			$whr_groupid .= "$groupid," ;
		}
		$whr_groupid = substr( $whr_groupid , 0 , -1 ) . ")" ;
	}
	$rs = $xoopsDB->query( "SELECT GPERM_itemid FROM ".$xoopsDB->prefix("group_permission")." LEFT JOIN ".$xoopsDB->prefix("modules")." m ON GPERM_modid=m.mid WHERE m.dirname='$mydirname' AND GPERM_name='gnavi_global' AND ($whr_groupid)" ) ;
	while( list( $itemid ) = $xoopsDB->fetchRow( $rs ) ) {
		$global_perms |= $itemid ;
	}
}
?>