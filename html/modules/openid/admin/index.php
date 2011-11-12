<?php
/**
 * Router for admin panel
 * @version $Rev$
 * @link $URL$
 */
require '../../../include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/modules/openid/class/admin/controller.php';
Openid_Admin_Controller::route();
?>