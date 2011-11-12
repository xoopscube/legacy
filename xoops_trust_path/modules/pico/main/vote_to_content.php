<?php

// common prepend
require dirname(dirname(__FILE__)).'/include/common_prepend.inc.php' ;
// global $breadcrumbsObj, $picoRequest, $permissions, $currenCategoryObj
// global $xoopsModuleConfig(overridden)

// controller
require_once dirname(dirname(__FILE__)).'/class/PicoControllerVoteContent.class.php' ;
$controller = new PicoControllerVoteContent( $currentCategoryObj ) ;
$controller->execute( $picoRequest ) ;
$controller->render() ;

?>