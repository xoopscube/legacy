<?php
/*
 * Created on 2008/02/11 by nao-pon http://hypweb.net/
 * $Id: favicon.php,v 1.2 2010/06/04 06:51:51 nao-pon Exp $
 */

$xoopsOption['nocommon'] = TRUE;
define('_LEGACY_PREVENT_LOAD_CORE_', TRUE);

define('PROTECTOR_SKIP_DOS_CHECK', TRUE);
define('BIGUMBRELLA_DISABLED', TRUE);

include '../../mainfile.php' ;

if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH in mainfile.php' ) ;
require XOOPS_TRUST_PATH.'/class/hyp_common/favicon/favicon.php';
?>