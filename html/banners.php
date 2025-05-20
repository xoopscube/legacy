<?php
/**
 * @package Legacy
 * @version 2.5.0
 * @author     Nuno Luciano (aka gigamaster)
 * @copyright (c) 2005-2025 The XOOPSCube Project
 * @license GPL 2.0
 */
/*------------------------------------------------------------------------*
 |  This file was entirely rewritten by the XOOPS Cube Project Legacy     |
 *------------------------------------------------------------------------*/

require_once './mainfile.php';

require_once XOOPS_ROOT_PATH . '/header.php';

// Define and call the delegate point for banner access
XCube_DelegateUtils::call('Legacypage.Banners.Access');

// If no delegate handled the call AND exited, this part will be reached.
// This indicates that bannerstats isn't active.
if (!headers_sent()) { // Check if a delegate already started output or redirected
    echo "<div style='padding:1.5rem;border:5px solid #face7427;margin:1.5rem;text-align:center;'>";
    echo "<h3>The banner management system has been updated.</h3>";
    echo "<p>If you are looking for banner statistics, please ensure the 'bannerstats' module is active.</p>";
    echo "<p>Access it directly via <a href='" . XOOPS_URL . "/modules/bannerstats/'>Banner Stats</a></p>";
    echo "</div>";
}
require_once XOOPS_ROOT_PATH . '/footer.php';
?>
