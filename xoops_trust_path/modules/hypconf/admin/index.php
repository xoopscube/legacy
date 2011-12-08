<?php

if (! $data = @ file_get_contents(XOOPS_TRUST_PATH . HYP_COMMON_PRELOAD_CONF)) {
	$data = '';
}
$data = htmlspecialchars($data);
$content = <<<EOD
<div style="max-width:700px;">
 <pre style="width:100%;height:25em;overflow:auto;padding:1em;border:1px solid gray">$data</pre>
</div>
EOD;

$check_dir = array(
	XOOPS_ROOT_PATH .'/class/hyp_common/cache',
	XOOPS_TRUST_PATH.'/cache',
	XOOPS_TRUST_PATH.'/class/hyp_common/favicon/cache',
	XOOPS_TRUST_PATH.'/uploads/hyp_common',
	XOOPS_TRUST_PATH.'/uploads/hyp_common/kakasi',
);

$dir_res = array();

foreach($check_dir as $dir){
	$dir = rtrim($dir, '/');
	if (is_writable($dir)) {
		$dir .= ' (<span style="color:green;font-weight:bold;">OK</span>)';
	} else {
		$dir .= ' (<span style="color:red;font-weight:bold;">NG</span>)';
	}
	$dir_res[] = $dir;
}

$dir_res = '<ul><li>'.join('</li><li>', $dir_res).'</li></ul>';

// output
xoops_cp_header();

include dirname(__FILE__).'/mymenu.php' ;

echo '<h3>'.hypconf_constant($constpref . '_DESC').' - '.hypconf_constant($constpref . '_ADMENU_CONTENTSADMIN').'</h3>' ;
echo '<h4>trust'.HYP_COMMON_PRELOAD_CONF.'</h4>' ;
echo <<<EOD

$content

<h3>Writable check results</h3>
$dir_res

EOD;

xoops_cp_footer();

exit();