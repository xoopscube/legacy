<?php

$op = '';

if (isset($_POST) && isset($_POST['op'])) {
	$op = $_POST['op'];
}

if ($op === 'save') {
	var_dump($_POST);

} else {

	if (! $data = @ file_get_contents(XOOPS_TRUST_PATH . HYP_COMMON_PRELOAD_CONF)) {
		$data = '';
	}
	$data = htmlspecialchars($data);
	$content = <<<EOD
<div style="max-width:700px;">
 <pre style="width:100%;height:25em;overflow:auto;padding:1em;border:1px solid gray">$data</pre>
</div>
EOD;

	xoops_cp_header();

	include dirname(__FILE__).'/mymenu.php' ;

	echo '<h3>'.hypconf_constant($constpref . '_DESC').' - '.hypconf_constant($constpref . '_ADMENU_CONTENTSADMIN').'</h3>' ;
	echo '<h4>trust'.HYP_COMMON_PRELOAD_CONF.'</h4>' ;
	echo $content;

	xoops_cp_footer();
}
exit();