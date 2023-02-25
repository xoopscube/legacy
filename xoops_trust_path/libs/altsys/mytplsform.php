<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * Templates admin form for each modules
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
include_once __DIR__ . '/include/tpls_functions.php';
include_once __DIR__ . '/include/Text_Diff.php';
include_once __DIR__ . '/include/Text_Diff_Renderer.php';
include_once __DIR__ . '/include/Text_Diff_Renderer_unified.php';


// only user groups with admin permissions
$module_handler     =& xoops_gethandler( 'module' );
$module             =& $module_handler->getByDirname( 'altsys' );
$moduleperm_handler =& xoops_gethandler( 'groupperm' );
if ( ! is_object( @$xoopsUser ) || ! $moduleperm_handler->checkRight( 'module_admin', $module->getVar( 'mid' ), $xoopsUser->getGroups() ) ) {
	die( 'only admin of altsys can access this area' );
}


// initials
$db =& XoopsDatabaseFactory::getDatabaseConnection();
( method_exists( 'MyTextSanitizer', 'sGetInstance' ) and $myts =& MyTextSanitizer::sGetInstance() ) || $myts =& MyTextSanitizer::getInstance();


// language file
altsys_include_language_file( 'mytplsform' );
altsys_include_language_file( 'mytplsadmin' );


// check $xoopsModule
if ( ! is_object( $xoopsModule ) ) {
	redirect_header( XOOPS_URL . '/user.php', 1, _NOPERM );
}


// tpl_file from $_GET
$tpl_tplset = $myts->stripSlashesGPC( @$_GET['tpl_tplset'] );
if ( ! $tpl_tplset ) {
	$tpl_tplset = $xoopsConfig['template_set'];
}
$tpl_tplset4sql = addslashes( $tpl_tplset );


if ( empty( $_GET['tpl_file'] ) || '_custom' == $_GET['tpl_file'] ) {
	$edit_mode = 'create';
	$tpl_file  = '_custom';
	$tpl       = [
		'tpl_id'           => 0,
		'tpl_refid'        => 0,
		'tpl_module'       => '_custom',
		'tpl_tplset'       => $tpl_tplset,
        'tpl_file'         => '_custom_' . substr(date('YmdHis'), 2, -2) . '.html',
		'tpl_desc'         => '',
		'tpl_lastmodified' => 0,
		'tpl_lastimported' => 0,
		'tpl_type'         => 'custom',
		'tpl_source'       => '',
	];


	// breadcrumbs
	$breadcrumbsObj = AltsysBreadcrumbs::getInstance();
	$breadcrumbsObj->appendPath( XOOPS_URL . '/modules/altsys/admin/index.php?mode=admin&amp;lib=altsys&amp;page=mytplsadmin', '_MI_ALTSYS_MENU_MYTPLSADMIN' );
	$breadcrumbsObj->appendPath( XOOPS_URL . '/modules/altsys/admin/index.php?mode=admin&amp;lib=altsys&amp;page=mytplsadmin&amp;dirname=_custom', _MYTPLSADMIN_CUSTOMTEMPLATE );
	$breadcrumbsObj->appendPath( '', '_MYTPLSADMIN_CREATENEWCUSTOMTEMPLATE' );
	$target_mname = _MYTPLSADMIN_CUSTOMTEMPLATE;

} else {

	// tpl_file from $_GET
	$edit_mode    = 'modify';
	$tpl_file     = $myts->stripSlashesGPC( @$_GET['tpl_file'] );
	$tpl_file     = str_replace( 'db:', '', $tpl_file );
	$tpl_file4sql = addslashes( $tpl_file );

	// get information from tplfile table
	$sql = 'SELECT * FROM ' . $db->prefix( 'tplfile' ) . ' f NATURAL LEFT JOIN ' . $db->prefix( 'tplsource' ) . " s WHERE f.tpl_file='$tpl_file4sql' ORDER BY f.tpl_tplset='$tpl_tplset4sql' DESC,f.tpl_tplset='default' DESC";
	$tpl = $db->fetchArray( $db->query( $sql ) );

	// get module info
	if ( '_custom' == $tpl['tpl_module'] ) {
		$target_module = null;
		$target_mname  = _MYTPLSADMIN_CUSTOMTEMPLATE;
	} else {
		$module_handler =& xoops_gethandler( 'module' );
		$target_module  =& $module_handler->getByDirname( $tpl['tpl_module'] );
		$target_mname   = is_object( $target_module ) ? $target_module->getVar( 'name' ) : '';
	}

	// breadcrumbs
	$breadcrumbsObj = AltsysBreadcrumbs::getInstance();
	if ( 'altsys' != $mydirname && is_object( $target_module ) ) {
		// mytplsform in each modules
		$mod_url = XOOPS_URL . '/modules/' . $target_module->getVar( 'dirname' );
		$modinfo = $target_module->getInfo();
		$breadcrumbsObj->appendPath( $mod_url . '/' . @$modinfo['adminindex'], $target_mname );
		$breadcrumbsObj->appendPath( $mod_url . '/admin/index.php?mode=admin&amp;lib=altsys&amp;page=mytplsadmin', _MD_A_MYTPLSFORM_TPLSADMIN );
	} else {
		// mytplsform in altsys
		$breadcrumbsObj->appendPath( XOOPS_URL . '/modules/altsys/admin/index.php?mode=admin&amp;lib=altsys&amp;page=mytplsadmin', '_MI_ALTSYS_MENU_MYTPLSADMIN' );
		$breadcrumbsObj->appendPath( XOOPS_URL . '/modules/altsys/admin/index.php?mode=admin&amp;lib=altsys&amp;page=mytplsadmin&amp;dirname=' . htmlspecialchars( $tpl['tpl_module'] ), $target_mname );
	}
	$breadcrumbsObj->appendPath( '', _MD_A_MYTPLSFORM_EDIT );
}


// error in specifying tpl_file
if ( empty( $tpl ) ) {
	if ( 0 === strncmp( $tpl_file, 'file:', 5 ) ) {
		die( 'Not DB template' );
	}

	//die( 'Invalid tpl_file.' ); // TODO Cannot edit newblock template - redirect to block management !
   // redirect_header( $_SERVER['REQUEST_URI'], 1, $msg );
    redirect_header( XOOPS_URL . '/modules/legacy/admin/index.php?action=BlockList', 2, 'Edit Template with Block Management ');
}


// TRANSACTION
if ( ! empty( $_POST['do_modifycont'] ) || ! empty( $_POST['do_modify'] ) ) {
	// Ticket Check
	if ( ! $xoopsGTicket->check( true, 'altsys_tplsform' ) ) {
		redirect_header( XOOPS_URL . '/', 3, $xoopsGTicket->getErrors() );
	}

	$result = $db->query( 'SELECT tpl_id FROM ' . $db->prefix( 'tplfile' ) . " WHERE tpl_file='$tpl_file4sql' AND tpl_tplset='" . addslashes( $tpl['tpl_tplset'] ) . "'" );
	while ( list( $tpl_id ) = $db->fetchRow( $result ) ) {
		$sql = 'UPDATE ' . $db->prefix( 'tplsource' ) . " SET tpl_source='" . addslashes( $myts->stripSlashesGPC( $_POST['tpl_source'] ) ) . "' WHERE tpl_id=$tpl_id";
		if ( ! $db->query( $sql ) ) {
			die( 'SQL Error' );
		}
		$db->query( 'UPDATE ' . $db->prefix( 'tplfile' ) . " SET tpl_lastmodified=UNIX_TIMESTAMP() WHERE tpl_id=$tpl_id" );
		altsys_template_touch( $tpl_id );
	}

	// continue or end ?
	if ( ! empty( $_POST['do_modifycont'] ) ) {
		redirect_header( 'index.php?mode=admin&lib=altsys&page=mytplsform&tpl_file=' . $tpl_file . '&tpl_tplset=' . $tpl_tplset . '&dirname=' . $tpl['tpl_module'] . '#altsys_tplsform_top', 1, _MD_A_MYTPLSFORM_UPDATED );
	} else {
		redirect_header( 'index.php?mode=admin&lib=altsys&page=mytplsadmin&dirname=' . $tpl['tpl_module'], 1, _MD_A_MYTPLSFORM_UPDATED );
	}
	exit;
}


if ( ! empty( $_POST['do_create'] ) ) {
	// Ticket Check
	if ( ! $xoopsGTicket->check( true, 'altsys_tplsform' ) ) {
		redirect_header( XOOPS_URL . '/', 3, $xoopsGTicket->getErrors() );
	}

	$sql = 'INSERT INTO '
	       . $db->prefix( 'tplfile' ) . " SET tpl_file='" . addslashes( $myts->stripSlashesGPC( $_POST['tpl_file'] ) ) . "',tpl_refid=0,tpl_module='" . addslashes( $tpl['tpl_module'] ) . "',tpl_tplset='" . addslashes( $tpl['tpl_tplset'] ) . "',tpl_lastmodified=UNIX_TIMESTAMP(),tpl_type='" . addslashes( $tpl['tpl_type'] ) . "'";
	if ( ! $db->query( $sql ) ) {
		die( 'SQL Error' . __LINE__ );
	}
	$tpl_id = (int) $db->getInsertId();
	$sql    = 'INSERT INTO ' . $db->prefix( 'tplsource' ) . " SET tpl_id=$tpl_id,tpl_source='" . addslashes( $myts->stripSlashesGPC( $_POST['tpl_source'] ) ) . "'";
	if ( ! $db->query( $sql ) ) {
		die( 'SQL Error' . __LINE__ );
	}
	altsys_template_touch( $tpl_id );

	// continue or end ?
	redirect_header( 'index.php?mode=admin&lib=altsys&page=mytplsadmin&dirname=' . $tpl['tpl_module'], 1, _MD_A_MYTPLSFORM_CREATED );
	exit;
}


// FORM RENDER

xoops_cp_header();
$mymenu_fake_uri = 'index.php?mode=admin&lib=altsys&page=mytplsadmin&dirname=' . $mydirname;

// Menu
altsys_include_mymenu();

echo '<h2 style="text-align:' . _GLOBAL_LEFT . ';">' . _MD_A_MYTPLSFORM_EDIT . '</h2>';

// Template Set, Name, Type
echo '<table class="outer">
    <tr><td>' . _MYTPLSADMIN_TH_SET . '</td><td>' . htmlspecialchars( $tpl['tpl_tplset'], ENT_QUOTES ) . '</td></tr>
    <tr><td>' . _MYTPLSADMIN_TH_NAME .'</td><td>' . htmlspecialchars( $tpl['tpl_file'], ENT_QUOTES ) . '</td></tr>
    <tr><td>' . _MYTPLSADMIN_TH_TYPE . '</td><td>' . htmlspecialchars( $tpl['tpl_type'], ENT_QUOTES ) . '</td></tr>
    </table>';

// Diff from file to selected DB template
$basefilepath        = tplsadmin_get_basefilepath( $tpl['tpl_module'], $tpl['tpl_type'], $tpl['tpl_file'] );
$diff_from_file4disp = '';
if ( file_exists( $basefilepath ) ) {
	$original_error_level = error_reporting();
	error_reporting( $original_error_level & ~E_NOTICE & ~E_WARNING );
	$diff     = new Text_Diff( file( $basefilepath ), explode( "\n", $tpl['tpl_source'] ) );
	$renderer = new Text_Diff_Renderer_unified();
	$diff_str = htmlspecialchars( $renderer->render( $diff ), ENT_QUOTES );
	foreach ( explode( "\n", $diff_str ) as $line ) {
		if ( 0x2d == ord( $line ) ) {
			$diff_from_file4disp .= "<span style='color:var(--color-red);'>" . $line . "</span>\n";
		} elseif ( 0x2b == ord( $line ) ) {
			$diff_from_file4disp .= "<span style='color:var(--color-green);'>" . $line . "</span>\n";
		} else {
			$diff_from_file4disp .= $line . "\n";
		}
	}
	error_reporting( $original_error_level );
}

// Diff from DB-default to selected DB template
$diff_from_default4disp = '';
if ( 'default' != $tpl['tpl_tplset'] ) {
	$original_error_level = error_reporting();
	error_reporting( $original_error_level & ~E_NOTICE & ~E_WARNING );
	[ $default_source ] = $db->fetchRow( $db->query( 'SELECT tpl_source FROM ' . $db->prefix( 'tplfile' ) . ' NATURAL LEFT JOIN ' . $db->prefix( 'tplsource' ) . " WHERE tpl_tplset='default' AND tpl_file='" . addslashes( $tpl['tpl_file'] ) . "' AND tpl_module='" . addslashes( $tpl['tpl_module'] ) . "'" ) );
	$diff     = new Text_Diff( explode( "\n", $default_source ), explode( "\n", $tpl['tpl_source'] ) );
	$renderer = new Text_Diff_Renderer_unified();
	$diff_str = htmlspecialchars( $renderer->render( $diff ), ENT_QUOTES );
	foreach ( explode( "\n", $diff_str ) as $line ) {
		if ( 0x2d == ord( $line ) ) {
			$diff_from_default4disp .= "<span style='color:var(--color-red);'>" . $line . "</span>\n";
		} elseif ( 0x2b == ord( $line ) ) {
			$diff_from_default4disp .= "<span style='color:var(--color-blue);'>" . $line . "</span>\n";
		} else {
			$diff_from_default4disp .= $line . "\n";
		}
	}
	error_reporting( $original_error_level );
}


echo '<div class="ui-card-full">';

// Diff Switch View
echo '<form name="diff_form" id="diff_form" action="" method="get">';
if ( $diff_from_file4disp ) {
    echo '<input class="switch" 
    type="checkbox" 
    name="display_diff2file" 
    id="display_diff2file" 
    onclick="slideToggle(\'#diff2file\', this)" 
    value="0">&nbsp;<label for="display_diff2default">Diff from file</label>';
	echo "<pre id=\"diff2file\" style=\"display: none; max-height: 340px;overflow-y: auto\"><code class=\"language-diff diff-highlight\">$diff_from_file4disp</code></pre>";
}
if ( $diff_from_default4disp ) {
    echo '<input class="switch" 
    type="checkbox" 
    name="display_diff2default" 
    id="display_diff2default" 
    onclick="slideToggle(\'#diff2default\', this)" 
    value="0">&nbsp;<label for="display_diff2default">Diff from default</label>';
	echo "<pre id=\"diff2default\" style=\"display: none; max-height: 340px;overflow-y: auto\"><code class=\"language-diff diff-highlight\">$diff_from_default4disp</code></pre>";
}
echo "</form>";


// Edit Template
echo "<a id='altsys_tplsform_top'></a>
    <form name='MainForm' id='altsys_tplsform' action='?mode=admin&amp;lib=altsys&amp;page=mytplsform&amp;tpl_file="
    . htmlspecialchars( $tpl_file, ENT_QUOTES ) . "&amp;tpl_tplset="
    . htmlspecialchars( $tpl['tpl_tplset'], ENT_QUOTES ) . "&amp;dirname=" . $target_mname . "' method='post'>"
    . $xoopsGTicket->getTicketHtml( __LINE__, 1800, 'altsys_tplsform' ) . "	
	<br>
	<textarea name='tpl_source' class='html' style='width:100%; height:35vh'>" . htmlspecialchars( $tpl['tpl_source'], ENT_QUOTES ) . "</textarea>
	<br>";

// Create New Template
if ( 'create' == $edit_mode ) {
	// create form
	echo "<label for='tpl_file'>" . _MD_A_MYTPLSFORM_LABEL_TPLFILE . "</label>
	<input type='text' name='tpl_file' id='tpl_file' value='" . htmlspecialchars( $tpl['tpl_file'], ENT_QUOTES ) . "' size='64'><br>
	<input class='button submit' type='submit' name='do_create' id='do_create' value='" . _MD_A_MYTPLSFORM_BTN_CREATE . "'>";
} else {
	// modify form
	echo "<br>
    <div class='adminnavi'>
	<input class='button update' type='submit' name='do_modifycont' id='do_modifycont' value='" . _MD_A_MYTPLSFORM_BTN_MODIFYCONT . "'>
    <input class='button reset' type='reset' name='reset' value='" . _MD_A_MYTPLSFORM_BTN_RESET . "'>
    <input class='button submit' type='submit' name='do_modify' id='do_modify' value='" . _MD_A_MYTPLSFORM_BTN_MODIFYEND . "'>
    </div>";
}
echo "</form></div>";

xoops_cp_footer();
