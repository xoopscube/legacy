<?php

function im_admin_update($menu_num, $id, $title, $link, $hide, $groups, $target) {
	global $xoopsDB;

	$xoopsDB =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();
	$title = $myts->makeTboxData4Save($title);
	$link = $myts->makeTboxData4Save($link);
	$groups = (is_array($groups)) ? implode(" ", $groups) : '';
	$db = $xoopsDB->prefix( "multimenu".$menu_num );
	if ( empty($id) ) {
		$newid = $xoopsDB->genId($db."_id_seq");
		$success = $xoopsDB->query("INSERT INTO ".$db." (id,title,hide,link,weight,groups,target) VALUES ($newid,'$title','$hide','$link','255','$groups','$target')");
		im_admin_clean($menu_num);
	} else	{
		$success = $xoopsDB->query("UPDATE ".$db." SET title='$title', hide='$hide', link='$link', groups='$groups', target='$target' WHERE id='$id'");
	}

	if ( !$success ) {
		redirect_header("index".$menu_num.".php",2,_AD_MULTIMENU_UPDATED);
	}else {
		redirect_header("index".$menu_num.".php",2,_AD_MULTIMENU_UPDATED);
	}
	exit();
}

function im_admin_edit ($menu_num, $id) {
	global $xoopsDB;

	xoops_cp_header();
	mm_admin_menu(intval($menu_num), _AD_MULTIMENU_ADMIN.$menu_num );
	echo "<fieldset style='padding: 5px;'><legend style='font-weight: bold; color: #900;'>". _AD_MULTIMENU_ADMIN . $menu_num . "</legend>"; 

	$xoopsDB =& Database::getInstance();
	$db = $xoopsDB->prefix( "multimenu".$menu_num );
	$result = $xoopsDB->query("SELECT title, hide, link, groups, target FROM ".$db." WHERE id=$id");
	list($title, $hide, $link, $groups, $target) = $xoopsDB->fetchrow($result);
	$groups = explode(" ", $groups);
	include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
	$form = new XoopsThemeForm(_AD_MULTIMENU_EDITIMENU, "editform", "index".$menu_num.".php");
	$myts =& MyTextSanitizer::getInstance();
	$title = $myts->makeTboxData4Edit($title);
	$link = $myts->makeTboxData4Edit($link);
	$formtitle = new XoopsFormTextArea(_AD_MULTIMENU_TITLE, "title", $title, 5, 80 );	// bluemoon 2011/08/15
	$formlink = new XoopsFormText(_AD_MULTIMENU_LINK, "link", 50, 255, $link);
	$formhide = new XoopsFormSelect(_AD_MULTIMENU_HIDE, "hide", $hide);
	$formhide->addOption("0", _NO);
	$formhide->addOption("1", _YES);
	$formtarget  = new XoopsFormSelect(_AD_MULTIMENU_TARGET, "target", $target);
	$formtarget->addOption("_self", _AD_MULTIMENU_TARG_SELF);
	$formtarget->addOption("_blank", _AD_MULTIMENU_TARG_BLANK);
	$formtarget->addOption("_parent", _AD_MULTIMENU_TARG_PARENT);
	$formtarget->addOption("_top", _AD_MULTIMENU_TARG_TOP);
	$formgroups = new XoopsFormSelectGroup(_AD_MULTIMENU_GROUPS, "groups", true, $groups, 5, true);
	$submit_button = new XoopsFormButton("", "submit", _AD_MULTIMENU_SUBMIT, "submit");

	$form->addElement($formtitle, true);
	$form->addElement($formlink, false);
	$form->addElement($formhide);
	$form->addElement($formtarget);
	$form->addElement($formgroups);
	$form->addElement(new XoopsFormHidden("id", $id));
	$form->addElement(new XoopsFormHidden("op", "update"));
	$form->addElement($submit_button);
	$form->display();
	echo "</fieldset><br />"; 
	xoops_cp_footer();
}

function im_admin_del ($menu_num, $id, $del=0) {
	global $xoopsDB;

	$xoopsDB =& Database::getInstance();
	$db = $xoopsDB->prefix( "multimenu".$menu_num );
	if ( $del == 1 ) {
		if ( $xoopsDB->query("DELETE FROM ".$db." WHERE id=$id") ) {
			im_admin_clean($menu_num);
			redirect_header("index".$menu_num.".php", 2, _AD_MULTIMENU_UPDATED);
		} else {
			redirect_header("index".$menu_num.".php", 2, _AD_MULTIMENU_NOTUPDATED);
		}
		exit();
	} else {
		xoops_cp_header();
		echo "<h4>"._AD_MULTIMENU_ADMIN.$menu_num."</h4>";
		xoops_confirm(array('op' => 'del', 'id' => $id, 'del' => 1), 'index'.$menu_num.'.php', _AD_MULTIMENU_SUREDELETE);
		xoops_cp_footer();
	}
}

function im_admin_move ($menu_num, $id, $weight) {
	global $xoopsDB;

	$xoopsDB =& Database::getInstance();
	$db = $xoopsDB->prefix( "multimenu".$menu_num );
	$xoopsDB->queryF("UPDATE ".$db." SET weight=weight+1 WHERE weight>=$weight AND id<>$id");
	$xoopsDB->queryF("UPDATE ".$db." SET weight=$weight WHERE id=$id");
	im_admin_clean($menu_num);
}

function im_admin_new($menu_num) {
	global $xoopsDB;
	xoops_cp_header();
	mm_admin_menu(intval($menu_num), _AD_MULTIMENU_ADMIN.$menu_num );
	echo "<fieldset style='padding: 5px;'><legend style='font-weight: bold; color: #900;'>". _AD_MULTIMENU_ADMIN . $menu_num . "</legend>"; 

	$id = 0;
	$title = '';
	$link = '';
	$hide = '';
	$weight = 255;
	$target = "_self";
	$member_handler =& xoops_gethandler('member');
	$xoopsgroups =& $member_handler->getGroups();
	$count = count($xoopsgroups);
	$groups = array();
	for ($i = 0; $i < $count; $i++)  $groups[] = $xoopsgroups[$i]->getVar('groupid');
	include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
	$form = new XoopsThemeForm(_AD_MULTIMENU_NEWIMENU, "newform", "index".$menu_num.".php");
	$myts =& MyTextSanitizer::getInstance();
	$title = $myts->makeTboxData4Edit($title);
	$link = $myts->makeTboxData4Edit($link);
	$formtitle = new XoopsFormTextArea(_AD_MULTIMENU_TITLE, "title", $title, 5, 80 );	// bluemoon 2011/08/15
	$formlink = new XoopsFormText(_AD_MULTIMENU_LINK, "link", 50, 255, $link);
	$formhide = new XoopsFormSelect(_AD_MULTIMENU_HIDE, "hide", $hide);
	$formhide->addOption("0", _NO);
	$formhide->addOption("1", _YES);
	$formtarget  = new XoopsFormSelect(_AD_MULTIMENU_TARGET, "target", $target);
	$formtarget->addOption("_self", _AD_MULTIMENU_TARG_SELF);
	$formtarget->addOption("_blank", _AD_MULTIMENU_TARG_BLANK);
	$formtarget->addOption("_parent", _AD_MULTIMENU_TARG_PARENT);
	$formtarget->addOption("_top", _AD_MULTIMENU_TARG_TOP);
	$formgroups = new XoopsFormSelectGroup(_AD_MULTIMENU_GROUPS, "groups", true, $groups, 5, true);
	$submit_button = new XoopsFormButton("", "submit", _AD_MULTIMENU_SUBMIT, "submit");

	$form->addElement($formtitle, true);
	$form->addElement($formlink, false);
	$form->addElement($formhide);
	$form->addElement($formtarget);
	$form->addElement($formgroups);
	$form->addElement(new XoopsFormHidden("id", $id));
	$form->addElement(new XoopsFormHidden("op", "update"));
	$form->addElement($submit_button);
	$form->display();
	echo "</fieldset><br />"; 
	xoops_cp_footer();
}

function im_admin_list($menu_num) {
	global $xoopsDB;

	xoops_cp_header();
	mm_admin_menu(intval($menu_num), _AD_MULTIMENU_ADMIN.$menu_num );
	echo "<fieldset style='padding: 5px;'><legend style='font-weight: bold; color: #900;'>". _AD_MULTIMENU_ADMIN . $menu_num . "</legend>"; 

	$xoopsDB =& Database::getInstance();
	$db = $xoopsDB->prefix( "multimenu".$menu_num );
	echo "<form action='index".$menu_num.".php?op=new' method='post' name='form1'>
	<table width='100%' border='0' cellspacing='1' cellpadding='0' class='outer'><tr>
	<th align='center'>"._AD_MULTIMENU_TITLE."</th>
	<th align='center'>"._AD_MULTIMENU_HIDE."</th>
	<th align='center'>"._AD_MULTIMENU_LINK."</th>
	<th align='center'>"._AD_MULTIMENU_OPERATION."</th></tr>";
	$result=$xoopsDB->query("SELECT id, link, title, hide, weight FROM ".$db." ORDER BY weight ASC");
	$class = 'even';
	while ($row=$xoopsDB->fetcharray($result)) {
		$status = ( $row['hide'] == 0 ) ? _NO : _YES;
		if ($row['weight'] != 0) {
			$moveup = "<a href='index".$menu_num.".php?op=move&id=".$row['id']."&weight=".($row['weight']-1)."'>["._AD_MULTIMENU_UP."]</a>";
		} else {
			$moveup = "["._AD_MULTIMENU_UP."]";
		}
		if ($row['weight'] != ($xoopsDB->getRowsNum($result) - 1)) {
			$movedown = "<a href='index".$menu_num.".php?op=move&id=".$row['id']."&weight=".($row['weight']+2)."'>["._AD_MULTIMENU_DOWN."]</a>";
		} else {
			$movedown = "["._AD_MULTIMENU_DOWN."]";
		}
		echo "<tr>
			<td class='$class'>".$row['title']."</td>
			<td class='$class' align='center'>$status</td>
			<td class='$class'>".$row['link']."</td>
			<td class='$class' align='center'><small><a href='index".$menu_num.".php?op=del&id=".$row['id']."'>["._DELETE."]</a>
			<a href='index".$menu_num.".php?op=edit&id=".$row['id']."'>["._EDIT."]</a>".$moveup.$movedown."</small></td></tr>";
		$class = ($class == 'odd') ? 'even' : 'odd';
	}
	echo "<tr><td class='foot' colspan='4' align='right'>
	<input type='submit' name='submit' value='"._AD_MULTIMENU_NEW."'>
	</td></tr></table></form>";
	echo "</fieldset><br />"; 
	xoops_cp_footer();
}

function im_admin_clean($menu_num) {
	global $xoopsDB;
	$i=0;
	$db = $xoopsDB->prefix( "multimenu".$menu_num );
	$result = $xoopsDB->query("SELECT id FROM ".$db." ORDER BY weight ASC");
	while (list($id) = $xoopsDB->fetchrow($result)) {
		$xoopsDB->queryF("UPDATE ".$db." SET weight='$i' WHERE id=$id");
		$i++;
	}
}


function mm_admin_menu($currentoption=0, $breadcrumb="")
{
    global $xoopsModule, $xoopsConfig;
    $tblColors=Array();
    $tblColors[0] = $tblColors[1] = $tblColors[2] = $tblColors[3] = $tblColors[4] = $tblColors[5] = $tblColors[6] = $tblColors[7] = $tblColors[8] = '#DDE';
    $tblColors[$currentoption] = 'white';

	$language = $xoopsConfig['language'] ;
	if( ! file_exists( XOOPS_ROOT_PATH . '/modules/system/language/'.$language.'/admin/blocksadmin.php') ) $language = 'english' ;
	//include_once( XOOPS_ROOT_PATH . '/modules/system/language/'.$language.'/admin.php' ) ;
	//include_once( XOOPS_ROOT_PATH . '/modules/system/language/'.$language.'/admin/blocksadmin.php' ) ;

	echo "<table width=100% class='outer'><tr><td align=right>
          <font size=2><b>".$xoopsModule->name()." : ".$breadcrumb."</b></font>
          </td></tr></table><br />";
	echo "<div id=\"navcontainer\">
		<ul style=\"padding: 3px 0; margin-left: 0; font: bold 12px Verdana, sans-serif; \">";
	echo "<li style=\"list-style: none; margin: 0; display: inline; \"><a href=\"index01.php\" style=\"padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: ".$tblColors[1]."; text-decoration: none; \">"._AD_MULTIMENU_ADMIN_01."</a></li>";
	echo "<li style=\"list-style: none; margin: 0; display: inline; \"><a href=\"index02.php\" style=\"padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: ".$tblColors[2]."; text-decoration: none; \">"._AD_MULTIMENU_ADMIN_02."</a></li>";
	echo "<li style=\"list-style: none; margin: 0; display: inline; \"><a href=\"index03.php\" style=\"padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: ".$tblColors[3]."; text-decoration: none; \">"._AD_MULTIMENU_ADMIN_03."</a></li>";
	echo "<li style=\"list-style: none; margin: 0; display: inline; \"><a href=\"index04.php\" style=\"padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: ".$tblColors[4]."; text-decoration: none; \">"._AD_MULTIMENU_ADMIN_04."</a></li>";
	echo "</ul></div>";
	echo "<div id=\"navcontainer\">
		<ul style=\"padding: 3px 0; margin-left: 0; font: bold 12px Verdana, sans-serif; \">";
	echo "<li style=\"list-style: none; margin: 0; display: inline; \"><a href=\"index05.php\" style=\"padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: ".$tblColors[5]."; text-decoration: none; \">"._AD_MULTIMENU_ADMIN_05."</a></li>";
	echo "<li style=\"list-style: none; margin: 0; display: inline; \"><a href=\"index06.php\" style=\"padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: ".$tblColors[6]."; text-decoration: none; \">"._AD_MULTIMENU_ADMIN_06."</a></li>";
	echo "<li style=\"list-style: none; margin: 0; display: inline; \"><a href=\"index07.php\" style=\"padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: ".$tblColors[7]."; text-decoration: none; \">"._AD_MULTIMENU_ADMIN_07."</a></li>";
	echo "<li style=\"list-style: none; margin: 0; display: inline; \"><a href=\"index08.php\" style=\"padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: ".$tblColors[8]."; text-decoration: none; \">"._AD_MULTIMENU_ADMIN_08."</a></li>";
	echo "<li style=\"list-style: none; margin: 0; display: inline; \"><a href=\"myblocksadmin.php\" style=\"padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: ".$tblColors[0]."; text-decoration: none; \">"._AM_BADMIN."</a></li>";
	echo "</ul></div>";
	echo "<br />";
}

?>