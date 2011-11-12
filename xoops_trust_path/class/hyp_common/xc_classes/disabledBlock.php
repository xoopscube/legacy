<?php
/*
 * Created on 2008/09/16 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: disabledBlock.php,v 1.1 2008/09/16 04:12:25 nao-pon Exp $
 */

class HypXCLDisabledBlock extends Legacy_AbstractBlockProcedure {
	function prepare()
	{
		return false;
	}
}
?>