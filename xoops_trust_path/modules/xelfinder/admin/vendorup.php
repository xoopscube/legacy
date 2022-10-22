<?php
/**
 * X-elFinder module for XCL
 * @package    XelFinder
 * @version    XCL 2.3.1
 * @author     Other authors Nuno Luciano (aka Gigamaster) 2020 XCL/PHP7
 * @author     Naoki Sawada (aka Nao-pon) <https://github.com/nao-pon>
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

if (! empty ( $_POST ['doupdate'] )) {
	global $xoopsConfig;
	while( ob_get_level() && @ ob_end_clean() );
	header('X-Accel-Buffering: no');
?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="<?php echo _CHARSET ?>">
        <style>body{background:rgb(28, 32, 38);font-family:monospace;color:rgb(167, 175, 190);padding:1em;}</style>
	</head>
	<body>
<?php

	while ( @ob_end_flush() );
	flush ();
	$pluginsDir = dirname(__FILE__, 2) . '/plugins';

	$cwd = getcwd ();
	chdir ( $pluginsDir );
	
	$locale = '';
	switch($xoopsConfig['language']) {
		case 'ja_utf8' :
			$locale = 'ja_JP.utf8';
			break;
		case 'japanese' :
			$locale = 'ja_JP.eucjp';
			break;
		case 'english' :
			$locale = 'en_US.iso88591';
			break;
	}
	if ($locale) {
		setlocale(LC_ALL, $locale);
		putenv('LC_ALL='.$locale);
	}
	putenv ( 'COMPOSER_HOME=' . $pluginsDir . '/.composer' );
	
	$phpcli = !empty($_POST['phpcli'])? trim($_POST['phpcli']) : 'php';
	$php54 = !empty($_POST['php54']);
	$cmds = array();
	$cmds[] = $phpcli.' -d curl.cainfo=cacert.pem -d openssl.cafile=cacert.pem composer.phar self-update --no-ansi --no-interaction 2>&1';
	if ($php54) {
	    $cmds[] = $phpcli.' -d curl.cainfo=cacert.pem -d openssl.cafile=cacert.pem composer.phar remove --no-update kunalvarma05/dropbox-php-sdk';
	} else {
	    $cmds[] = $phpcli.' -d curl.cainfo=cacert.pem -d openssl.cafile=cacert.pem composer.phar require kunalvarma05/dropbox-php-sdk ^0.2';
	}
	$cmds[] = $phpcli.' -d curl.cainfo=cacert.pem -d openssl.cafile=cacert.pem composer.phar update  --no-ansi --no-interaction --prefer-dist --no-dev 2>&1';
	//$cmds = array(
	//	$phpcli.' composer.phar info --no-ansi --no-interaction 2>&1',
	//);
	foreach($cmds as $cmd) {
		$res = '';
		$handle = popen($cmd, 'r');
		while ($res !== false && $handle && !feof($handle)) {
			if ($res = fgets($handle, 80)) {
				echo $res . '<br>';
				flush ();
			}
		}
		pclose($handle);
	}
	
	chdir ( $cwd );

    echo '<p>'.xelfinderAdminLang ( 'COMPOSER_UPDATE_STARTED' ).'</p>';
	echo '<p>'.xelfinderAdminLang ( 'COMPOSER_DONE_UPDATE' ).'</p>';
	echo '</body></html>';
	
	exit ();
}

// RENDER
xoops_cp_header ();
include dirname ( __FILE__ ) . '/mymenu.php';

// Check if vendor file exists and print a message
$filename = dirname(__FILE__, 2) . '/plugins/vendor/autoload.php';
if ( ! file_exists( $filename ) ) {
    $googledrivefail = sprintf( '<div class="error"><p>'.xelfinderAdminLang ( 'COMPOSER_UPDATE_ERROR' ).'</p></div><div class="message-warning"><p>'.xelfinderAdminLang ( 'COMPOSER_UPDATE_FAIL' ).'</p></div>', $filename );
    } else {
    $googledrivepass = sprintf('<div class="success">'.xelfinderAdminLang ( 'COMPOSER_UPDATE_SUCCESS' ).'</div>', $filename);
}

// Render
echo '<h2>' . xelfinderAdminLang ( 'COMPOSER_UPDATE' ) . '</h2>';

// COMPOSER UPDATE CHECK MESSAGE
if ( $googledrivefail ) {
    echo $googledrivefail;
    } else {
    echo $googledrivepass;
}


$php54up = false;

if ($php54up = version_compare(PHP_VERSION, '5.4.0', '>=')) {
	if (preg_match('/^(\d\.\d)/', PHP_VERSION, $m)) {
		$curver = $m[1];
	} else {
		$curver = '5.4';
	}
	$curverDig = str_replace('.', '', $curver);

    // COMPOSER
    echo '<hr>'
    .'<h2>'. xelfinderAdminLang( 'COMPOSER_RUN_UPDATE' ) .'</h2>'
    .'<div class="tips">'. xelfinderAdminLang( 'COMPOSER_UPDATE_HELP' ) .'<br>'. xelfinderAdminLang('COMPOSER_UPDATE_TIME') .'</div>'
?>

<div>
	<form action="./index.php?page=vendorup" method="post" id="xelfinder_vendorup_f" target="composer_update">
		<table>
            <tr>
			<td>
				<p>PHP CLI Command<br><label><input value="php" type="radio" name="cli" checked="checked">Default is "php"</label></p>
				<p><input type="text" name="phpcli" value="php" /></p>
			</td>
			<td>
				<dl>
					<dt>Customized example</dt>
					<dd><label><input value="/usr/local/php<?php echo $curver; ?>/bin/php" type="radio" name="cli">lolipop - "/usr/local/php<?php echo $curver; ?>/bin/php"</label></dd>
					<dd><label><input value="/usr/local/bin/php<?php echo $curverDig; ?>cli" type="radio" name="cli">XREA/CoreServer/ValueServer - "/usr/local/bin/php<?php echo $curverDig; ?>cli"</label></dd>
					<dd><label><input value="/opt/php-<?php echo PHP_VERSION; ?>/bin/php" type="radio" name="cli">XSERVER - "/opt/php-<?php echo PHP_VERSION; ?>/bin/php"</label></dd>
				</dl>
			</td>
		</tr></table>
		<p>
		    <input type="submit" name="doupdate" id="xelfinder_vendorup_s" value="<?php echo xelfinderAdminLang('ADMENU_VENDORUPDATE'); ?>">
		    <input type="hidden" name="php54" value="<?php echo $curver === '5.4' ? '1' : '0'; ?>">
		</p>
	</form>
</div>

<iframe id="ifm-xelfinder-vendorup" name="composer_update" title="Composer" style="display: block;border:none;width:80%;height:300px;margin:2em auto;overflow-y:scroll;"></iframe>

<script>
(function($){
	var autoHeight = function() {
		var innH = jQuery("#ifm-xelfinder-vendorup").contents().find('body').outerHeight(true);
		var boxH = jQuery("#ifm-xelfinder-vendorup").height();
		if (boxH < innH) {
			jQuery("#ifm-xelfinder-vendorup").height(innH + 50);
		}
		setTimeout(autoHeight, 500);
	};
	autoHeight();
	$('#xelfinder_vendorup_f').on('submit', function(e) {
		setTimeout(function() {
			$('#xelfinder_vendorup_s').replaceWith($('<p>').html("<?php echo xelfinderAdminLang('COMPOSER_UPDATE_STARTED'); ?>"));
		}, 100);
	})
	.find('input[type=radio]').on('change', function(e) {
		$('#xelfinder_vendorup_f').find('input[type=text]').val($(this).val());
	});
})(jQuery);
</script>

<?php
} else {
    echo '<div class="error"><p>Update Vendor requires PHP >= 5.4<br> Your PHP version is '. PHP_VERSION .'</p></div>';
}
xoops_cp_footer ();
