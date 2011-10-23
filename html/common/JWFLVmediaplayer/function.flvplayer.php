<?php
/**
 *
 * @package Legacy
 * @version $Id: function.xoops_pagenavi.php,v 1.2 2007/06/24 07:26:21 nobunobu Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://www.gnu.org/licenses/gpl.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     xoops_pagenavi2
 * Version:  1.0
 * Date:     Nov 13, 2005
 * Author:   minahito, hiro
 * Purpose:  the place holder for xoops pagenavi.
 *           like 2.0 style
 * Input:    pagenavi =
 *           offset =
 * 
 * Examples: {xoops_pagenavi2 pagenavi=$pagenavi}
 * -------------------------------------------------------------
 */
function smarty_function_flvplayer($params, &$smarty)
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
			$ret .= @sprintf("<font class=\"navi_pagneutral\"><a href='%s'>&laquo;</a></font>", $navi->renderURLForPage($navi->getPrivStart()));
		}

		//
		// counting
		//
		$counter=1;
		$currentPage = $navi->getCurrentPage();
		while($counter<=$totalPages) {
			if($counter==$currentPage) {
				$ret.=@sprintf("<strong><font class=\"navi_pagact\">%d</font></strong>",$counter);
			}
			elseif(($counter>$currentPage-$offset && $counter<$currentPage+$offset) || $counter==1 || $counter==$totalPages) {
				if($counter==$totalPages && $currentPage<$totalPages-$offset) {
					$ret.="... ";
				}
				$ret .= @sprintf("<font class=\"navi_pagneutral\"><a href='%s'>%d</a></font>",$navi->renderURLForPage(($counter-1)*$perPage),$counter);
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
			$ret.=@sprintf("<font class=\"navi_pagneutral\"><a href='%s'>&raquo;</a></font>",$navi->renderURLForPage($navi->getNextStart()));
		}
	}

	print $ret;
}

?>
