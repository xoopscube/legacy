<?php
//
// Created on 2006/10/18 by nao-pon http://hypweb.net/
// $Id: plugin_conv.php,v 1.2 2009/02/01 07:53:57 nao-pon Exp $
//

if (empty($_POST['plugin']) && empty($_FILES)) {
	xoops_cp_header() ;
	
	$mymenu_fake_uri = 'admin/index.php?page=plugin_conv' ;
	include dirname(__FILE__).'/mymenu.php' ;
	
	include dirname(dirname(__FILE__))."/util/plugin_conv/index.php";
	
	xoops_cp_footer() ;

} else {	
	include dirname(dirname(__FILE__))."/util/plugin_conv/index.php";
}
