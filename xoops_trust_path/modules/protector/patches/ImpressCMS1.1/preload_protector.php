<?php
/**
* Initiating Protector module
*
* This file is responsible for initiating the Protector module so no hacks on mainfile are required
*
* @copyright	The ImpressCMS Project http://www.impresscms.org/
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @package		libraries
* @since		1.1
* @author		marcan <marcan@impresscms.org>
* @version		$Id: protector.php 1742 2008-04-20 14:46:20Z malanciault $
*/

/**
 * Define these constants to specify weight. Only for demonstration purposes for now
 */
/*define(ICMSPRELOADPROTECTOR_STARTCOREBOOT, 2);
define(ICMSPRELOADPROTECTOR_FINISHCOREBOOT, 10);
*/
class IcmsPreloadProtector extends IcmsPreloadItem
{
	function eventStartCoreBoot() {
		$filename = ICMS_TRUST_PATH.'/modules/protector/include/precheck.inc.php';
		if (file_exists($filename)) {
			include $filename;
		}
	}
	
	function eventFinishCoreBoot() {
		$filename = ICMS_TRUST_PATH.'/modules/protector/include/postcheck.inc.php';
		if (file_exists($filename)) { 
			include $filename;
		}
	}
}
?>