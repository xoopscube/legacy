<?php
/*
 * Created on 2012/01/22 by nao-pon http://hypweb.net/
 * $Id: index.php,v 1.1 2012/01/22 20:26:49 nao-pon Exp $
 */

$xelfinderOpenJs = 'openWithSelfMain("'.XOOPS_URL.'/modules/'.$mydirname.'/manager.php", "elfinder", 750, 500);';
$xelfinderAdminOpenJs = 'openWithSelfMain("'.XOOPS_URL.'/modules/'.$mydirname.'/manager.php?admin=1", "elfinder", 750, 500);';


include XOOPS_ROOT_PATH.'/header.php' ;

echo '<h4>xelFinder</h4>';

echo '<script>
	(function(){
		var windowonload;
		if (typeof window.onload == "function") {
			windowonload = window.onload();
		}
		window.onload=function(){
			if (windowonload) {
				windowonload();
			}
			'.$xelfinderAdminOpenJs.'
		};
	}());
</script>';

echo '<p><a href="#" onclick="'.htmlspecialchars($xelfinderOpenJs).'return false;">Open elFinder window</span></p>';
echo '<p><a href="#" onclick="'.htmlspecialchars($xelfinderAdminOpenJs).'return false;">Open elFinder window (Admin mode)</span></p>';

include XOOPS_ROOT_PATH.'/footer.php';

//include '../manager.php';
