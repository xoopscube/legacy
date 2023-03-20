<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * Compile templates admin
 * @package    Altsys
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

require_once __DIR__ . '/class/AltsysBreadcrumbs.class.php';
include_once __DIR__ . '/include/gtickets.php';
include_once __DIR__ . '/include/altsys_functions.php';

// this page can be called only from altsys
// if( $xoopsModule->getVar('dirname') != 'altsys' ) die( 'this page can be called only from altsys' ) ;

// language file
altsys_include_language_file( 'compilehookadmin' );

//
// DEFINITIONS
//

$compile_hooks = [

	'enclosebycomment' => [
		'pre'         => '<!-- begin altsys_tplsadmin %s -->',
		'post'        => '<!-- end altsys_tplsadmin %s -->',
		'success_msg' => _TPLSADMIN_FMT_MSG_ENCLOSEBYCOMMENT,
		'dt'          => _TPLSADMIN_DT_ENCLOSEBYCOMMENT,
		'dd'          => _TPLSADMIN_DD_ENCLOSEBYCOMMENT,
		'conf_msg'    => _TPLSADMIN_CNF_ENCLOSEBYCOMMENT,
		'skip_theme'  => true,
	],

	'enclosebybordereddiv' => [
		'pre'         => '<div class="altsys_tplsadmin_frame" style="border:1px solid black;overflow-wrap: break-word;">',
		'post'        => '<br><a href="' . XOOPS_URL . '/modules/altsys/admin/index.php?mode=admin&amp;lib=altsys&amp;page=mytplsform&amp;tpl_file=%1$s" style="color:red;">Edit:<br>%1$s</a></div>',
		'success_msg' => _TPLSADMIN_FMT_MSG_ENCLOSEBYBORDEREDDIV,
		'dt'          => _TPLSADMIN_DT_ENCLOSEBYBORDEREDDIV,
		'dd'          => _TPLSADMIN_DD_ENCLOSEBYBORDEREDDIV,
		'conf_msg'    => _TPLSADMIN_CNF_ENCLOSEBYBORDEREDDIV,
		'skip_theme'  => true,
	],

	'hooksavevars' => [
		'pre'         => '<?php include_once "' . XOOPS_TRUST_PATH . '/libs/altsys/include/compilehook.inc.php" ; tplsadmin_save_tplsvars(\'%s\',$this) ; ?>',
		'post'        => '',
		'success_msg' => _TPLSADMIN_FMT_MSG_HOOKSAVEVARS,
		'dt'          => _TPLSADMIN_DT_HOOKSAVEVARS,
		'dd'          => _TPLSADMIN_DD_HOOKSAVEVARS,
		'conf_msg'    => _TPLSADMIN_CNF_HOOKSAVEVARS,
		'skip_theme'  => false,
	],

	'removehooks' => [
		'pre'         => '',
		'post'        => '',
		'success_msg' => _TPLSADMIN_FMT_MSG_REMOVEHOOKS,
		'dt'          => _TPLSADMIN_DT_REMOVEHOOKS,
		'dd'          => _TPLSADMIN_DD_REMOVEHOOKS,
		'conf_msg'    => _TPLSADMIN_CNF_REMOVEHOOKS,
		'skip_theme'  => false,
	],

];


//
// EXECUTE STAGE
//

// clearing files in templates_c/
if ( ! empty( $_POST['clearcache'] ) || ! empty( $_POST['cleartplsvars'] ) ) {
	// Ticket Check

	if ( ! $xoopsGTicket->check() ) {
		redirect_header( XOOPS_URL . '/', 3, $xoopsGTicket->getErrors() );
	}

	if ( $handler = opendir( XOOPS_COMPILE_PATH . '/' ) ) {
		while ( false !== ( $file = readdir( $handler ) ) ) {
			if ( ! empty( $_POST['clearcache'] ) ) {
				// check template cache '*.php'

				if ( '.php' !== mb_substr( $file, - 4 ) ) {
					continue;
				}
			} else {
				// check tplsvars cache 'tplsvars_*'

				if ( 'tplsvars_' !== mb_substr( $file, 0, 9 ) ) {
					continue;
				}
			}

			$file_path = XOOPS_COMPILE_PATH . '/' . $file;

			@unlink( $file_path );
		}

		redirect_header( '?mode=admin&lib=altsys&page=compilehookadmin', 1, _TPLSADMIN_MSG_CLEARCACHE );

		exit;
	}

	exit( 'XOOPS_COMPILE_PATH cannot be opened' );
}

// append hooking commands
foreach ( $compile_hooks as $command => $compile_hook ) {
	if ( ! empty( $_POST[ $command ] ) ) {
		// Ticket Check

		if ( ! $xoopsGTicket->check() ) {
			redirect_header( XOOPS_URL . '/', 3, $xoopsGTicket->getErrors() );
		}

		if ( $handler = opendir( XOOPS_COMPILE_PATH . '/' ) ) {
			$file_count = 0;

			while ( false !== ( $file = readdir( $handler ) ) ) {

				// skip /. /.. and hidden files
				if ( '.' == $file[0] ) {
					continue;
				}

				// skip if the extension is not .html.php
				if ( '.html.php' != mb_substr( $file, - 9 ) ) {
					continue;
				}

				// skip theme.html when comment-mode or div-mode
				if ( $compile_hook['skip_theme'] && '%theme.html.php' == mb_substr( $file, - 15 ) ) {
					$skip_mode = true;
				} else {
					$skip_mode = false;
				}

				$file_path = XOOPS_COMPILE_PATH . '/' . $file;

				$file_bodies = file( $file_path );

				// remove lines inserted by compilehookadmin
				if ( mb_strstr( $file_bodies[0], 'altsys' ) ) {
					array_shift( $file_bodies );
				}

				if ( mb_strstr( $file_bodies[ count( $file_bodies ) - 1 ], 'altsys' ) ) {
					array_pop( $file_bodies );

					$file_bodies[ count( $file_bodies ) - 1 ] = rtrim( $file_bodies[ count( $file_bodies ) - 1 ] );
				}

				// get the name of the source template from Smarty's comment
				if ( preg_match( '/compiled from (\S+)/', $file_bodies[1], $regs ) ) {
					$tpl_name = $regs[1];
				} else {
					$tpl_name = '__FILE__';
				}

				$fw = fopen( $file_path, 'wb' );

				// insert "pre" command before the compiled cache
				if ( $compile_hook['pre'] && ! $skip_mode ) {
					fwrite( $fw, sprintf( $compile_hook['pre'], htmlspecialchars( $tpl_name, ENT_QUOTES ) ) . "\r\n" );
				}

				// rest of template cache
				foreach ( $file_bodies as $line ) {
					fwrite( $fw, $line );
				}

				// insert "post" command after the compiled cache
				if ( $compile_hook['post'] && ! $skip_mode ) {
					fwrite( $fw, "\r\n" . sprintf( $compile_hook['post'], htmlspecialchars( $tpl_name, ENT_QUOTES ) ) );
				}

				fclose( $fw );

				$file_count ++;
			}

			if ( $file_count > 0 ) {
				redirect_header( '?mode=admin&lib=altsys&page=compilehookadmin', 3, sprintf( $compile_hook['success_msg'], $file_count ) );

				exit;
			}

			redirect_header( '?mode=admin&lib=altsys&page=compilehookadmin', 3, _TPLSADMIN_MSG_CREATECOMPILECACHEFIRST );

			exit;
		}

		die( 'XOOPS_COMPILE_PATH cannot be opened' );
	}
}


// count template vars & compiled caches
$compiledcache_num = 0;
$tplsvars_num      = 0;
if ( $handler = opendir( XOOPS_COMPILE_PATH . '/' ) ) {
	while ( false !== ( $file = readdir( $handler ) ) ) {
		if ( 0 === strncmp( $file, 'tplsvars_', 9 ) ) {
			$tplsvars_num ++;
		} elseif ( '.php' === mb_substr( $file, - 4 ) ) {
			$compiledcache_num ++;
		}
	}
}


// get tplsets
$sql            = 'SELECT tplset_name,COUNT(DISTINCT tpl_file) FROM ' . $xoopsDB->prefix( 'tplset' ) . ' LEFT JOIN ' . $xoopsDB->prefix( 'tplfile' ) . " ON tplset_name=tpl_tplset GROUP BY tpl_tplset ORDER BY tpl_tplset='default' DESC,tpl_tplset";
$srs            = $xoopsDB->query( $sql );
$tplset_options = "<option value=''>----</option>\n";
while ( list( $tplset, $tpl_count ) = $xoopsDB->fetchRow( $srs ) ) {
	$tplset4disp = htmlspecialchars( $tplset, ENT_QUOTES );

	$tplset_options .= "<option value='$tplset4disp'>$tplset4disp ($tpl_count)</option>\n";
}


// FORM RENDER

xoops_cp_header();

// MyMenu
altsys_include_mymenu();

// Breadcrumbs
$breadcrumbsObj = AltsysBreadcrumbs::getInstance();
$breadcrumbsObj->appendPath( XOOPS_URL . '/modules/altsys/admin/index.php?mode=admin&amp;lib=altsys&amp;page=compilehookadmin', _MI_ALTSYS_MENU_COMPILEHOOKADMIN );

// Heading Title
echo "
<h2>" . _MI_ALTSYS_MENU_COMPILEHOOKADMIN . "</h2>

	<style>
		dl	{ margin: 10px; }
		dt	{ margin-bottom:5px; }
		dd	{ margin-left:20px; }
	</style>
	
	<form action='?mode=admin&amp;lib=altsys&amp;page=compilehookadmin' method='post'>
\n";

foreach ( $compile_hooks as $command => $compile_hook ) {
	echo "
		<h3>{$compile_hook['dt']}</h3>
		<div class='ui-card-full'>
		<p>{$compile_hook['dd']}</p>
		<p><input class='button' type='submit' name='$command' id='$command' value='" . _GO . "' onclick='return confirm(\"{$compile_hook['conf_msg']}\");'></p>
		</div>
	\n";
}

    echo "<div class='ui-card-full'>
		<p>" . _TPLSADMIN_NUMCAP_COMPILEDCACHES . ": <strong>$compiledcache_num</strong></p>
		<p><input type='submit' class='button delete' name='clearcache' value='" . _DELETE . "' onclick='return confirm(\"" . _TPLSADMIN_CNF_DELETEOK . "\");'></p>
		<p>" . _TPLSADMIN_NUMCAP_TPLSVARS . ": <strong>$tplsvars_num</strong></p>
		<p><input type='submit' class='button delete' name='cleartplsvars' value='" . _DELETE . "' onclick='return confirm(\"" . _TPLSADMIN_CNF_DELETEOK . "\");'></p>
		" . $xoopsGTicket->getTicketHtml( __LINE__ ) . "
		</div>
	</form>


	<form action='?mode=admin&amp;lib=altsys&amp;page=get_tplsvarsinfo' method='post' style='margin: 40px;' target='_blank'>
		<h3>" . _TPLSADMIN_DT_GETTPLSVARSINFO_DW . "</h3>
		<div class='ui-card-full'>
		<p>" . _TPLSADMIN_DD_GETTPLSVARSINFO_DW . "</p>
		<p><input class='button download' type='submit' name='as_dw_extension_zip' value='zip'> <input class='button download' type='submit' name='as_dw_extension_tgz' value='tar.gz'></p>
		</div>
	</form>

	<form action='?mode=admin&amp;lib=altsys&amp;page=get_templates' method='post' style='margin: 40px;' target='_blank'>
		<h3>" . _TPLSADMIN_DT_GETTEMPLATES . "</h3>
		<div class='ui-card-full'>
		<p>" . _TPLSADMIN_DD_GETTEMPLATES . "</p>
		<p><select name='tplset'>$tplset_options</select> <input class='button download' type='submit' name='download_zip' value='zip'> <input class='button download' type='submit' name='download_tgz' value='tar.gz'></p>
		</div>
	</form>

	<form action='?mode=admin&amp;lib=altsys&amp;page=put_templates' method='post' enctype='multipart/form-data' style='margin: 40px;'>
		<h3>" . _TPLSADMIN_DT_PUTTEMPLATES . "</h3>
		<div class='ui-card-full'>
		<p>" . _TPLSADMIN_DD_PUTTEMPLATES . "</p>
		<p><select name='tplset'>$tplset_options</select> <input type='file' accept='.tar' name='tplset_archive' size='60'> <input class='button upload' type='submit' value='" . _SUBMIT . "'></p>
		</div>
    </form>";

xoops_cp_footer();
