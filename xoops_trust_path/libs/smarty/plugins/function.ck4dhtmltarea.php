<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:	 function
 * Name:	 ck4dhtmltarea
 * Version:  1.0
 * Date:	 Sep 18, 2012
 * Author:	 nao-pon
 * Purpose:  cycle through given values
 * Input:	 name = form 'name'.
 *			 value = preset value. Set HTML-escaped value with htmlspecialchars().
 *			 id = form 'id'. If it's empty, ID is defined automatically by prefix & name.
 *			 cols = amount of cols. (default 50)
 *			 rows = amount of rows. (default 5)
 *			 editor = textarea editor type (default bbcode)
 *			 toolbar = toolbar (JSON or String)
 * 
 * Examples: {ck4dhtmltarea name=message cols=40 rows=6 value=$message}
 * -------------------------------------------------------------
 */

require_once XOOPS_ROOT_PATH . '/modules/ckeditor4/class/Ckeditor4Utiles.class.php';

function smarty_function_ck4dhtmltarea($params, &$smarty)
{
	if (isset($params['name'])) {
		
		$js = Ckeditor4_Utils::getJS($params);
		
		if ($js) {

			if (version_compare(LEGACY_BASE_VERSION, '2.2', '>=')) {
				// Add script into HEAD
				$root =& XCube_Root::getSingleton();
				$jQuery = $root->mContext->getAttribute('headerScript');
				$jQuery->addScript($js);
				$jQuery->addLibrary('/modules/ckeditor4/ckeditor/ckeditor.js');
				$addScript = '';
			} else {
				$xoopsURL = XOOPS_URL;
				$addScript = <<<EOD
<script type="text/javascript">
if (typeof jQuery != 'undefined') {
	jQuery(function($){
		$js
	});
}
</script>
<script type="text/javascript" src="{$xoopsURL}/modules/ckeditor4/ckeditor/ckeditor.js"></script>
EOD;
				//$smarty->assign( 'xoops_module_header', $smarty->get_template_vars( 'xoops_module_header' ) . $addScript );
			}
		}
		
		//
		// Build the object for output.
		//
		print '<textarea name="'.$params['name'].'" class="'.$params['class'].'" style="'.$params['style'].'" cols="'.$params['cols'].'" rows="'.$params['rows'].'" id="'.$params['id'].'">'.$params['value'].'</textarea>'.$addScript;
	}
}

?>
