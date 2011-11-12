<?php
/**
 * OpenID Auth Module
 * @version $Rev$
 * @author "OpenID Auth Module" Development Committee
 *         Original author Nat Sakimura
 * @copyright 2008 by Nat Sakimura (=nat)
 * @license GPL
 * @link $URL$
 */
require '../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/openid/class/controller/identifier.php';
$controller = new Openid_Controller_Identifier();

if ($controller->execute() == OPENID_VIEW_DEFAULT) {
    $xoopsOption['template_main'] = 'openid_consumer.html';
    require XOOPS_ROOT_PATH . '/header.php';
    $controller->viewDefault();
    require XOOPS_ROOT_PATH . '/footer.php';
}
?>