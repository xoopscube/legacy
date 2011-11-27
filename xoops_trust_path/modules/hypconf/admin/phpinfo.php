<?php
/*
 * Created on 2011/11/27 by nao-pon http://hypweb.net/
 * $Id: phpinfo.php,v 1.1 2011/11/27 06:39:57 nao-pon Exp $
 */

ob_start();
phpinfo();
$config['contents'] = ob_get_contents();
ob_end_clean();
if (preg_match('#<style.+?/style>#is', $config['contents'], $match)) {
	$style = $match[0];
} else {
	$style = '';
}
$style =<<< EOD
<style type="text/css">
div.phpinfo {background-color: #ffffff; color: #000000; width: 80%; margin-left:auto; margin-right: auto;}
div.phpinfo, div.phpinfo td, div.phpinfo th, div.phpinfo h1, div.phpinfo h2 {font-family: sans-serif;}
div.phpinfo pre {margin: 0px; font-family: monospace;}
div.phpinfo a {color: inherit; font: inherit;}
div.phpinfo a:link {color: #000099; text-decoration: none; background-color: #ffffff;}
div.phpinfo a:hover {text-decoration: underline;}
div.phpinfo table {border-collapse: collapse;}
div.phpinfo .center {text-align: center;}
div.phpinfo .center table { margin-left: auto; margin-right: auto; text-align: left;}
div.phpinfo .center th { text-align: center !important; }
div.phpinfo td, div.phpinfo th { border: 1px solid #000000; vertical-align: baseline;}
div.phpinfo td {padding: 5px;}
div.phpinfo .p {text-align: left;}
div.phpinfo .e {background-color: #ccccff; font-weight: bold; color: #000000;}
div.phpinfo .h {background-color: #9999cc; font-weight: bold; color: #000000;}
div.phpinfo .v {background-color: #cccccc; color: #000000;}
div.phpinfo .vr {background-color: #cccccc; text-align: right; color: #000000;}
div.phpinfo img {float: right; border: 0px;}
div.phpinfo hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}
</style>
EOD;
list(,$config['contents']) = explode('<body>', $config['contents'], 2);
list($config['contents']) = explode('</body>', $config['contents'], 2);
$config['contents'] = $style . '<div class="phpinfo">' . $config['contents'] . '</div>';
