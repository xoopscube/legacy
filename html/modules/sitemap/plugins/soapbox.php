<?php
// $Id: soapbox.php,v 1.1 2005/04/07 09:23:42 gij Exp $
// NOTE		::	edit by domifara for soapbox1.5 RC3

function b_sitemap_soapbox(){
	$sitemap = null;
 global $xoopsUser ;
	$ret = [];
    $db = &XoopsDatabaseFactory::getDatabaseConnection();
	$myts =& MyTextSanitizer::getInstance();

	//get soapbox module object for readable columnIDs check
	$module_handler = &xoops_gethandler('module');
	$soapbox = $module_handler->getByDirname('soapbox');
	if (!is_object($soapbox)) {
		return $sitemap ;
	}
	$module_mid = $soapbox->getVar('mid');

	//groups for readable columnIDs check
	$groups = is_object( $xoopsUser ) ? $xoopsUser -> getGroups() : XOOPS_GROUP_ANONYMOUS;

	//get readable columnIDs
	$gperm_handler = & xoops_gethandler( 'groupperm' );
	$gperm_name = 'Column Permissions';
	$i = 0;
	$url = 'column.php?columnID=';

	$sql = 'SELECT columnID, name FROM '.$db->prefix('sbcolumns') . ' ORDER BY weight' ;
	if ( !$result = $db->query($sql)){
		return $sitemap;
	}
	while([$columnID, $name] = $db->fetchRow($result)){
		 $columnID = (int)$columnID;
		if ( $gperm_handler -> checkRight( $gperm_name, $columnID, $groups, $module_mid ) ){
			$ret["parent"][] = ["id" => $columnID, "title" => $myts->makeTboxData4Show($name), "url" => $url.$columnID];
		}

	}
	return $ret;
}
