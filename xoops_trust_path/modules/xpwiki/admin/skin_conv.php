<?php
//
// Created on 2006/10/18 by nao-pon http://hypweb.net/
// $Id: skin_conv.php,v 1.1 2006/10/18 08:06:29 nao-pon Exp $
//

xoops_cp_header() ;

$mymenu_fake_uri = 'admin/index.php?page=skin_conv' ;
include dirname(__FILE__).'/mymenu.php' ;

include dirname(dirname(__FILE__))."/util/skin_conv/index.php";

xoops_cp_footer() ;
?>