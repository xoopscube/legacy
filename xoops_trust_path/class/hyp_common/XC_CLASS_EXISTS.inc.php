<?php
/*
 * Created on 2009/02/27 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: XC_CLASS_EXISTS.inc.php,v 1.1 2009/03/01 23:42:25 nao-pon Exp $
 */

function XC_CLASS_EXISTS($className)
{
	if (version_compare(PHP_VERSION, "5.0", ">=")) {
		return class_exists($className, false);
	}
	else {
		return class_exists($className);
	}
}
