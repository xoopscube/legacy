<?php
/**
 *
 * @package Legacy
 * @version $Id: IPbanningFilter.class.php,v 1.5 2008/09/25 15:12:43 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

/***
 * This burns the access from the specific IP address, which is specified at
 * the preference.
 */
class Legacy_IPbanningFilter extends XCube_ActionFilter
{
	function preBlockFilter()
	{
		if ($this->mRoot->mContext->getXoopsConfig('enable_badips')) {
			if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']) {
				foreach ($this->mRoot->mContext->mXoopsConfig['bad_ips'] as $bi) {
					$bi = str_replace('.', '\.', $bi);
					if (!empty($bi) && preg_match("/".$bi."/", $_SERVER['REMOTE_ADDR'])) {
						die();
					}
				}
			}
		}
	}
}

?>
