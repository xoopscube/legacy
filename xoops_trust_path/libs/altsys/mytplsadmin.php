<?php
// ------------------------------------------------------------------------- //
//                         mytplsadmin.php (altsys)                          //
//               - XOOPS templates admin for each modules -                  //
//                      GIJOE <http://www.peak.ne.jp/>                       //
// ------------------------------------------------------------------------- //

require_once dirname(__FILE__).'/class/AltsysBreadcrumbs.class.php' ;
include_once dirname(__FILE__)."/include/gtickets.php" ;
include_once dirname(__FILE__).'/include/altsys_functions.php' ;
include_once dirname(__FILE__)."/include/tpls_functions.php" ;


// only groups have 'module_admin' of 'altsys' can do that.
$module_handler =& xoops_gethandler('module') ;
$module =& $module_handler->getByDirname('altsys') ;
if (! is_object($module)) {
    die('install altsys') ;
}
$moduleperm_handler =& xoops_gethandler('groupperm') ;
if (! is_object(@$xoopsUser) || ! $moduleperm_handler->checkRight('module_admin', $module->getVar('mid'), $xoopsUser->getGroups())) {
    die('only admin of altsys can access this area') ;
}


// initials
$db =& XoopsDatabaseFactory::getDatabaseConnection();
(method_exists('MyTextSanitizer', 'sGetInstance') and $myts =& MyTextSanitizer::sGetInstance()) || $myts =& MyTextSanitizer::getInstance() ;

// language file
altsys_include_language_file('mytplsadmin') ;

// check $xoopsModule
if (! is_object($xoopsModule)) {
    redirect_header(XOOPS_URL.'/user.php', 1, _NOPERM) ;
}

// set target_module if specified by $_GET['dirname']
$module_handler =& xoops_gethandler('module');
if (! empty($_GET['dirname'])) {
    $dirname = preg_replace('/[^0-9a-zA-Z_-]/', '', $_GET['dirname']) ;
    $target_module =& $module_handler->getByDirname($dirname) ;
}

if (! empty($target_module) && is_object($target_module)) {
    // specified by dirname (for tplsadmin as an independent module)
    $target_mid = $target_module->getVar('mid') ;
    $target_dirname = $target_module->getVar('dirname') ;
    $target_dirname4sql = addslashes($target_dirname) ;
    $target_mname = $target_module->getVar('name') . "&nbsp;" . sprintf("(%2.2f)", $target_module->getVar('version') / 100.0) ;
    //$query4redirect = '?dirname='.urlencode(strip_tags($_GET['dirname'])) ;
} elseif (@$_GET['dirname'] == '_custom') {
    // custom template
    $target_mid = 0 ;
    $target_dirname = '_custom' ;
    $target_dirname4sql = '_custom' ;
    $target_mname = _MYTPLSADMIN_CUSTOMTEMPLATE ;
    //$query4redirect = '' ;
} else {
    // not specified by dirname (for 3rd party modules as mytplsadmin)
    $target_mid = $xoopsModule->getVar('mid') ;
    $target_dirname = $xoopsModule->getVar('dirname') ;
    $target_dirname4sql = addslashes($target_dirname) ;
    $target_mname = $xoopsModule->getVar('name') ;
    //$query4redirect = '' ;
}


//**************//
// POST stages  //
//**************//

// Create new template set (blank or clone)
if (! empty($_POST['clone_tplset_do']) && ! empty($_POST['clone_tplset_from']) && ! empty($_POST['clone_tplset_to'])) {
    // Ticket Check
    if (! $xoopsGTicket->check()) {
        redirect_header(XOOPS_URL.'/', 3, $xoopsGTicket->getErrors());
    }

    $tplset_from = $myts->stripSlashesGPC($_POST['clone_tplset_from']) ;
    $tplset_to = $myts->stripSlashesGPC($_POST['clone_tplset_to']) ;
    // check tplset_name "from" and "to"
    if (! preg_match('/^[0-9A-Za-z_-]{1,16}$/', $_POST['clone_tplset_from'])) {
        tplsadmin_die(_MYTPLSADMIN_ERR_INVALIDSETNAME, $target_dirname) ;
    }
    if (! preg_match('/^[0-9A-Za-z_-]{1,16}$/', $_POST['clone_tplset_to'])) {
        tplsadmin_die(_MYTPLSADMIN_ERR_INVALIDSETNAME, $target_dirname) ;
    }
    list($is_exist) = $db->fetchRow($db->query("SELECT COUNT(*) FROM ".$db->prefix("tplfile")." WHERE tpl_tplset='".addslashes($tplset_to)."'")) ;
    if ($is_exist) {
        tplsadmin_die(_MYTPLSADMIN_ERR_DUPLICATEDSETNAME, $target_dirname) ;
    }
    list($is_exist) = $db->fetchRow($db->query("SELECT COUNT(*) FROM ".$db->prefix("tplset")." WHERE tplset_name='".addslashes($tplset_to)."'")) ;
    if ($is_exist) {
        tplsadmin_die(_MYTPLSADMIN_ERR_DUPLICATEDSETNAME, $target_dirname) ;
    }
    // insert tplset table
    $db->query("INSERT INTO ".$db->prefix("tplset")." SET tplset_name='".addslashes($tplset_to)."', tplset_desc='Created by tplsadmin', tplset_created=UNIX_TIMESTAMP()") ;
    tplsadmin_copy_templates_db2db($tplset_from, $tplset_to, "tpl_module='$target_dirname4sql'") ;
    redirect_header('?mode=admin&lib=altsys&page=mytplsadmin&dirname='.$target_dirname, 1, _MYTPLSADMIN_DBUPDATED) ;
    exit ;
}

// DB to DB template copy (checked templates)
if (is_array(@$_POST['copy_do'])) {
    foreach ($_POST['copy_do'] as $tplset_from_tmp => $val) {
        if (! empty($val)) {
            // Ticket Check
    if (! $xoopsGTicket->check()) {
        redirect_header(XOOPS_URL.'/', 3, $xoopsGTicket->getErrors());
    }

            $tplset_from = $myts->stripSlashesGPC($tplset_from_tmp) ;
            if (empty($_POST['copy_to'][$tplset_from]) || $_POST['copy_to'][$tplset_from] == $tplset_from) {
                tplsadmin_die(_MYTPLSADMIN_ERR_INVALIDTPLSET, $target_dirname) ;
            }
            if (empty($_POST["{$tplset_from}_check"])) {
                tplsadmin_die(_MYTPLSADMIN_ERR_NOTPLFILE, $target_dirname) ;
            }
            $tplset_to = $myts->stripSlashesGPC($_POST['copy_to'][$tplset_from]) ;
            foreach ($_POST["{$tplset_from}_check"] as $tplfile_tmp => $val) {
                if (empty($val)) {
                    continue ;
                }
                $tplfile = $myts->stripSlashesGPC($tplfile_tmp) ;
                tplsadmin_copy_templates_db2db($tplset_from, $tplset_to, "tpl_file='".addslashes($tplfile)."'") ;
            }
            redirect_header('?mode=admin&lib=altsys&page=mytplsadmin&dirname='.$target_dirname, 1, _MYTPLSADMIN_DBUPDATED) ;
            exit ;
        }
    }
}

// File to DB template copy (checked templates)
if (! empty($_POST['copyf2db_do'])) {
    // Ticket Check
    if (! $xoopsGTicket->check()) {
        redirect_header(XOOPS_URL.'/', 3, $xoopsGTicket->getErrors());
    }

    if (empty($_POST['copyf2db_to'])) {
        tplsadmin_die(_MYTPLSADMIN_ERR_INVALIDTPLSET, $target_dirname) ;
    }
    if (empty($_POST['basecheck'])) {
        tplsadmin_die(_MYTPLSADMIN_ERR_NOTPLFILE, $target_dirname) ;
    }
    $tplset_to = $myts->stripSlashesGPC($_POST['copyf2db_to']) ;
    foreach ($_POST['basecheck'] as $tplfile_tmp => $val) {
        if (empty($val)) {
            continue ;
        }
        $tplfile = $myts->stripSlashesGPC($tplfile_tmp) ;
        tplsadmin_copy_templates_f2db($tplset_to, "tpl_file='".addslashes($tplfile)."'") ;
    }
    redirect_header('?mode=admin&lib=altsys&page=mytplsadmin&dirname='.$target_dirname, 1, _MYTPLSADMIN_DBUPDATED) ;
    exit ;
}

// DB template remove (checked templates)
if (is_array(@$_POST['del_do'])) {
    foreach ($_POST['del_do'] as $tplset_from_tmp => $val) {
        if (! empty($val)) {
            // Ticket Check
    if (! $xoopsGTicket->check()) {
        redirect_header(XOOPS_URL.'/', 3, $xoopsGTicket->getErrors());
    }

            $tplset_from = $myts->stripSlashesGPC($tplset_from_tmp) ;
            if ($tplset_from == 'default' && $target_dirname != '_custom') {
                tplsadmin_die(_MYTPLSADMIN_ERR_CANTREMOVEDEFAULT, $target_dirname) ;
            }
            if (empty($_POST["{$tplset_from}_check"])) {
                tplsadmin_die(_MYTPLSADMIN_ERR_NOTPLFILE, $target_dirname) ;
            }

            require_once XOOPS_ROOT_PATH.'/class/template.php' ;
            $tpl = new XoopsTpl();
            $tpl->force_compile = true;

            foreach ($_POST["{$tplset_from}_check"] as $tplfile_tmp => $val) {
                if (empty($val)) {
                    continue ;
                }
                $tplfile = $myts->stripSlashesGPC($tplfile_tmp) ;
                $result = $db->query("SELECT tpl_id FROM ".$db->prefix("tplfile")." WHERE tpl_tplset='".addslashes($tplset_from)."' AND tpl_file='".addslashes($tplfile)."'") ;
                while (list($tpl_id) = $db->fetchRow($result)) {
                    $tpl_id = intval($tpl_id) ;
                    $db->query("DELETE FROM ".$db->prefix("tplfile")." WHERE tpl_id=$tpl_id") ;
                    $db->query("DELETE FROM ".$db->prefix("tplsource")." WHERE tpl_id=$tpl_id") ;
                }
        // remove templates_c
        $tpl->clear_cache('db:'.$tplfile);
                $tpl->clear_compiled_tpl('db:'.$tplfile);
            }
            redirect_header('?mode=admin&lib=altsys&page=mytplsadmin&dirname='.$target_dirname, 1, _MYTPLSADMIN_DBUPDATED) ;
            exit ;
        }
    }
}



//************//
// GET stage  //
//************//

// javascript
$_MYTPLSADMIN_ERR_INVALIDTPLSET = htmlspecialchars(_MYTPLSADMIN_ERR_INVALIDTPLSET);
$_MYTPLSADMIN_ERR_NOTPLFILE = htmlspecialchars(_MYTPLSADMIN_ERR_NOTPLFILE);
$javascript = <<<EOD
<script type="text/javascript">
	function altsys_mytpladmin_check_copy_submit(msg, id, selcheck) {
		if (typeof jQuery != 'undefined') {
			var checked = jQuery('form[name="MainForm"] input[name^="'+id+'check"]:checked').val();
			if (typeof checked == 'undefined') {
				alert("$_MYTPLSADMIN_ERR_NOTPLFILE");
				return false;
			}
			if (selcheck) {
				if (id == 'base') {
					var select = 'copyf2db_to';
				} else {
					var select = 'copy_to['+id.substr(0,id.length-1)+']'
				}
				if (jQuery('form[name="MainForm"] select[name="'+select+'"]').val() == '') {
					alert("$_MYTPLSADMIN_ERR_INVALIDTPLSET");
					return false;
				}
			}
		}
		return confirm(msg);
	}
</script>
EOD;

// get tplsets
$tplset_handler =& xoops_gethandler('tplset') ;
$tplsets = array_keys($tplset_handler->getList()) ;
$sql = "SELECT distinct tpl_tplset FROM ".$db->prefix("tplfile")." ORDER BY tpl_tplset='default' DESC,tpl_tplset" ;
$srs = $db->query($sql);
while (list($tplset) = $db->fetchRow($srs)) {
    if (! in_array($tplset, $tplsets)) {
        $tplsets[] = $tplset ;
    }
}

$tplsets_th4disp = '' ;
$tplset_options = "<option value=''>----</option>\n" ;
foreach ($tplsets as $tplset) {
    $tplset4disp = htmlspecialchars($tplset, ENT_QUOTES) ;
    $active = $th_attr = '';
    if ($tplset == $xoopsConfig['template_set']) {
        $th_attr = "class='active dbtplset_active'";
        $active = '<sup>*</sup>';
    }
    $tplsets_th4disp .= "<th $th_attr><input type='checkbox' title='"._MYTPLSADMIN_TITLE_CHECKALL."' onclick=\"with(document.MainForm){for(i=0;i<length;i++){if(elements[i].type=='checkbox'&&elements[i].name.indexOf('{$tplset4disp}_check')>=0){elements[i].checked=this.checked;}}}\" />{$active}DB-{$tplset4disp}</th>" ;
    $tplset_options .= "<option value='$tplset4disp'>$tplset4disp</option>\n" ;
}

// get tpl_file owned by the module
$sql = "SELECT tpl_file,tpl_desc,tpl_type,COUNT(tpl_id) FROM ".$db->prefix("tplfile")." WHERE tpl_module='$target_dirname4sql' GROUP BY tpl_file ORDER BY tpl_type, tpl_file" ;
$frs = $db->query($sql);

xoops_cp_header() ;

// css display
require_once XOOPS_TRUST_PATH.'/libs/altsys/class/D3Tpl.class.php' ;
$tpl = new D3Tpl() ;
echo '<style scoped="scoped">';
$tpl->display('db:altsys_inc_mytplsadmin.css') ;
echo '</style>';

// javascript
echo $javascript;

// mymenu
altsys_include_mymenu() ;

// breadcrumbs
$breadcrumbsObj =& AltsysBreadcrumbs::getInstance() ;
if ($breadcrumbsObj->hasPaths()) {
    $breadcrumbsObj->appendPath(XOOPS_URL.'/modules/altsys/admin/index.php?mode=admin&amp;lib=altsys&amp;page=mytplsadmin', _MI_ALTSYS_MENU_MYTPLSADMIN) ;
    $breadcrumbsObj->appendPath('', $target_mname) ;
}

echo "<h3 style='text-align:"._GLOBAL_LEFT.";'>"._MYTPLSADMIN_H3_MODULE." : $target_mname</h3>\n" ;

// link to create a new custom template
if ($target_dirname == '_custom') {
    echo "<a href='index.php?mode=admin&lib=altsys&page=mytplsform&tpl_tplset=default'>"._MYTPLSADMIN_CREATENEWCUSTOMTEMPLATE."</a>\n" ;
}

// beggining of table & form
echo "
	<form name='MainForm' action='?mode=admin&amp;lib=altsys&amp;page=mytplsadmin&amp;dirname=".htmlspecialchars($target_dirname, ENT_QUOTES)."' method='post'>
	".$xoopsGTicket->getTicketHtml(__LINE__)."
	<table class='outer altsys_mytplsadmin'>
		<tr>
			<th>"._MYTPLSADMIN_TH_NAME."</th>
			<th>"._MYTPLSADMIN_TH_TYPE."</th>
			<th><input type='checkbox' title="._MYTPLSADMIN_TITLE_CHECKALL." onclick=\"with(document.MainForm){for(i=0;i<length;i++){if(elements[i].type=='checkbox'&&elements[i].name.indexOf('basecheck')>=0){elements[i].checked=this.checked;}}}\" />"._MYTPLSADMIN_TH_FILE."</th>
			$tplsets_th4disp
		</tr>\n" ;

// STYLE for distinguishing fingerprints
$fingerprint_classes = array( '' , ' fingerprint1' , ' fingerprint2' , ' fingerprint3' , ' fingerprint4' , ' fingerprint5' , ' fingerprint6' , ' fingerprint7' ) ;

// template ROWS
while (list($tpl_file, $tpl_desc, $type, $count) = $db->fetchRow($frs)) {
    $evenodd = @$evenodd == 'even' ? 'odd' : 'even' ;
    $fingerprints = array();

    // information about the template
    echo "
		<tr>
			<td class='$evenodd'>
				<dl>
					<dt>".htmlspecialchars($tpl_file, ENT_QUOTES)."</dt>
					<dd>".htmlspecialchars($tpl_desc, ENT_QUOTES)."</dd>
				</dl>
			</td>
			<td class='$evenodd'>".$type."<br />(".$count.")</td>\n" ;

    // the base file template column
    $basefilepath = tplsadmin_get_basefilepath($target_dirname, $type, $tpl_file) ;

    if (file_exists($basefilepath)) {
        $fingerprint = tplsadmin_get_fingerprint(file($basefilepath)) ;
        $fingerprints[ $fingerprint ] = '' ;
        echo "<td class='$evenodd'>".formatTimestamp(filemtime($basefilepath), 'm').'<br />'.substr($fingerprint, 0, 16)."<br /><input type='checkbox' name='basecheck[$tpl_file]' value='1' /></td>\n" ;
        $fingerprint_class_count = 0 ;
    } else {
        echo "<td class='$evenodd'><br /></td>" ;
        $fingerprint_class_count = -1 ;
    }

    // db template columns
    foreach ($tplsets as $tplset) {
        $tplset4disp = htmlspecialchars($tplset, ENT_QUOTES) ;

        // query for templates in db
        $drs = $db->query("SELECT * FROM ".$db->prefix("tplfile")." f NATURAL LEFT JOIN ".$db->prefix("tplsource")." s WHERE tpl_file='".addslashes($tpl_file)."' AND tpl_tplset='".addslashes($tplset)."'") ;
        $numrows = $db->getRowsNum($drs) ;
        $tpl = $db->fetchArray($drs) ;
        if (empty($tpl['tpl_id'])) {
            echo "<td class='$evenodd'>($numrows)</td>\n" ;
        } else {
            $fingerprint = tplsadmin_get_fingerprint(explode("\n", $tpl['tpl_source'])) ;
            if (isset($fingerprints[ $fingerprint ])) {
                $class = $fingerprints[ $fingerprint ] ;
            } else {
                //$fingerprint_class_count ++ ;
                $class = $fingerprint_classes[++$fingerprint_class_count] ;
                $fingerprints[ $fingerprint ] = $class ;
            }
            echo "<td class='{$evenodd}{$class}'>".formatTimestamp($tpl['tpl_lastmodified'], 'm').'<br />'.substr($fingerprint, 0, 16)."<br /><input type='checkbox' name='{$tplset4disp}_check[{$tpl_file}]' value='1' /> &nbsp; <a href='?mode=admin&amp;lib=altsys&amp;page=mytplsform&amp;tpl_file=".htmlspecialchars($tpl['tpl_file'], ENT_QUOTES)."&amp;tpl_tplset=".htmlspecialchars($tpl['tpl_tplset'], ENT_QUOTES)."&amp;dirname=".htmlspecialchars($target_dirname, ENT_QUOTES)."'>"._EDIT."</a> ($numrows)</td>\n" ;
        }
    }

    echo "</tr>\n" ;
}

// command submit ROW
echo "
	<tr>
		<td class='head'>
			"._MYTPLSADMIN_CREATE_NEW_TPLSET.": <br />
			"._MYTPLSADMIN_CAPTION_BASE.":
			<select name='clone_tplset_from'>
				$tplset_options
				<option value='_blank_'>"._MYTPLSADMIN_OPT_BLANKSET."</option>
			</select>
			<br />
			"._MYTPLSADMIN_CAPTION_SETNAME.": <input type='text' name='clone_tplset_to' size='8' maxlength='16' /> <input type='submit' name='clone_tplset_do' value='"._MYTPLSADMIN_BTN_NEWTPLSET."' />
		</td>
		<td class='head'></td>
		<td class='head'>
			"._MYTPLSADMIN_CAPTION_COPYTO.":
			<select name='copyf2db_to'>
				$tplset_options
			</select>
			<br />
			<input name='copyf2db_do' type='submit' value='"._MYTPLSADMIN_BTN_COPY."' onclick='return altsys_mytpladmin_check_copy_submit(\""._MYTPLSADMIN_CNF_COPY_SELECTED_TEMPLATES."\", \"base\", true);' />
		</td>\n" ;

    foreach ($tplsets as $tplset) {
        $tplset4disp = htmlspecialchars($tplset, ENT_QUOTES) ;
        echo "\t\t<td class='head'>
			" . ($tplset == 'default' && $target_dirname != '_custom' ? "" : "<input name='del_do[{$tplset4disp}]' type='submit' value='"._DELETE."' onclick='return altsys_mytpladmin_check_copy_submit(\""._MYTPLSADMIN_CNF_DELETE_SELECTED_TEMPLATES."\", \"{$tplset4disp}_\", false);' /><br /><br />") . "
			"._MYTPLSADMIN_CAPTION_COPYTO.":
			<select name='copy_to[{$tplset4disp}]'>
				".str_replace('<option value=\''.$tplset4disp.'\'>'.$tplset4disp.'</option>', '', $tplset_options)."
			</select>
			<input name='copy_do[{$tplset4disp}]' type='submit' value='"._MYTPLSADMIN_BTN_COPY."' onclick='return altsys_mytpladmin_check_copy_submit(\""._MYTPLSADMIN_CNF_COPY_SELECTED_TEMPLATES."\", \"{$tplset4disp}_\", true);' />
		</td>\n" ;
    }

echo "	</tr>\n" ;


echo "</table></form>" ;
// end of table & form

xoops_cp_footer() ;
