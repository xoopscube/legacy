<?php

$constpref = '_MI_' . strtoupper( $mydirname ) ;

$adminmenu[0]['title'] = constant( $constpref.'_ADMIN_CONF' ) ;
$adminmenu[0]['link']  = "?cmd=conf" ;

$adminmenu[1]['title'] = constant( $constpref.'_ADMIN_TOOLS' ) ;
$adminmenu[1]['link']  = "?:AdminTools" ;

$adminmenu[2]['title'] = constant( $constpref.'_PLUGIN_CONVERTER' ) ;
$adminmenu[2]['link']  = "admin/index.php?page=plugin_conv" ;

$adminmenu[3]['title'] = constant( $constpref.'_SKIN_CONVERTER' ) ;
$adminmenu[3]['link']  = "admin/index.php?page=skin_conv" ;

?>