<?php
/**
 *
 * @package Legacy
 * @version $Id: function.xoops_pagenavi.php,v 1.3 2008/09/25 15:12:37 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     xoops_pagenavi
 * Version:  1.0
 * Date:     Nov 13, 2005
 * Author:   minahito
 * Purpose:  the place holder for xoops pagenavi.
 * Input:    pagenavi =
 *           offset =
 * 
 * Examples: {xoops_pagenavi pagenavi=$pagenavi}
 * -------------------------------------------------------------
 */
function smarty_function_xoops_pagenavi($params, &$smarty)
{
	$ret = "";

	if (isset($params['pagenavi']) && is_object($params['pagenavi'])) {
		
		$navi =& $params['pagenavi'];
		
		$perPage = $navi->getPerpage();

		$total = $navi->getTotalItems();
		$totalPages = $navi->getTotalPages();
		
		if ($totalPages == 0) {
			return;
		}
		
		$url = $navi->renderURLForPage();
		$current = $navi->getStart();
		
		$offset = isset($params['offset']) ? intval($params['offset']) : 4;

		//
		// check prev
		//
		if($navi->hasPrivPage()) {
			$ret .= @sprintf("<a href='%s'>&laquo;</a> ", $navi->renderURLForPage($navi->getPrivStart()));
		}

		//
		// counting
		//
		$counter=1;
		$currentPage = $navi->getCurrentPage();
		while($counter<=$totalPages) {
			if($counter==$currentPage) {
				$ret.=@sprintf("<strong>(%d)</strong> ",$counter);
			}
			elseif(($counter>$currentPage-$offset && $counter<$currentPage+$offset) || $counter==1 || $counter==$totalPages) {
				if($counter==$totalPages && $currentPage<$totalPages-$offset) {
					$ret.="... ";
				}
				$ret .= @sprintf("<a href='%s'>%d</a> ",$navi->renderURLForPage(($counter-1)*$perPage),$counter);
				if($counter==1 && $currentPage>1 + $offset) {
					$ret.="... ";
				}
			}
			$counter++;
		}
	
		//
		// check next
		//
		$next=$current + $perPage;
		if($navi->hasNextPage()) {
			$ret.=@sprintf("<a href='%s'>&raquo;</a>",$navi->renderURLForPage($navi->getNextStart()));
		}
	}

	print $ret;
}

?>
