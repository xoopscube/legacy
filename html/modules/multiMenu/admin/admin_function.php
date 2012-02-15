<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

class multimenu{
  private $mnum;
  private $db;
  private $root;

  public function __construct($menu_num = '01'){
    $this->root = XCube_Root::getSingleton();
    $this->mnum = $menu_num;
    $this->db = XoopsDatabaseFactory::getDatabaseConnection();
  }
  public function mm_admin_menu($currentoption=0, $breadcrumb=""){
    $tblColors=Array();
    $tblColors[0] = $tblColors[1] = $tblColors[2] = $tblColors[3] = $tblColors[4] = $tblColors[5] = $tblColors[6] = $tblColors[7] = $tblColors[8]  = $tblColors[99] = '#DDE';
    $tblColors[$currentoption] = 'white';
    echo '<table width=100% class="outer"><tr><td align="right">
          <font size="2"><b>'.$this->root->mContext->mModule->mXoopsModule->getShow('name').' : '.$breadcrumb.'</b></font>
          </td></tr></table><br />';
/*
    echo '<div id="navcontainer">
    <ul style="padding: 3px 0; margin-left: 0; font: bold 12px Verdana, sans-serif; ">';
    echo '<li style="list-style: none; margin: 0; display: inline;"><a href="index.php?mnum=1" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[1].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_01.'</a></li>';
    echo '<li style="list-style: none; margin: 0; display: inline;"><a href="index.php?mnum=2" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[2].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_02.'</a></li>';
    echo '<li style="list-style: none; margin: 0; display: inline;"><a href="index.php?mnum=3" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[3].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_03.'</a></li>';
    echo '<li style="list-style: none; margin: 0; display: inline;"><a href="index.php?mnum=4" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[4].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_04.'</a></li>';
    echo '<li style="list-style: none; margin: 0; display: inline; ">
    <a href="index.php?mnum=99" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[99].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_99.'</a></li>';
    echo '</ul>';

    echo '
    <ul style="padding: 3px 0; margin-left: 0; font: bold 12px Verdana, sans-serif; ">';
    echo '<li style="list-style: none; margin: 0; display: inline; "><a href="index.php?mnum=5" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[5].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_05.'</a></li>';
    echo '<li style="list-style: none; margin: 0; display: inline; "><a href="index.php?mnum=6" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[6].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_06.'</a></li>';
    echo '<li style="list-style: none; margin: 0; display: inline; "><a href="index.php?mnum=7" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[7].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_07.'</a></li>';
    echo '<li style="list-style: none; margin: 0; display: inline; "><a href="index.php?mnum=8" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[8].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_08.'</a></li>';
    echo '<li style="list-style: none; margin: 0; display: inline; ">
    <a href="myblocksadmin.php" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[0].'; text-decoration: none; ">'._AM_BADMIN.'</a></li>';
    echo '<li style="list-style: none; margin: 0; display: inline; ">
    <a href="'.XOOPS_MODULE_URL.'/legacy/admin/index.php?action=PreferenceEdit&confmod_id='.$this->root->mContext->mModule->mXoopsModule->get('mid').'" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: #DDE; text-decoration: none; ">'._PREFERENCES.'</a></li>';
    echo '<li style="list-style: none; margin: 0; display: inline; ">
    <a href="'.XOOPS_MODULE_URL.'/legacy/admin/index.php?action=Help&dirname=multiMenu" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: #DDE; text-decoration: none; ">'._HELP.'</a></li>';
    echo '</ul></div>';
*/
    echo '<div id="navcontainer">';
    echo '<div style="float:left; height:2.0em;"><a href="index.php?mnum=1" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[1].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_01.'</a></div>';
    echo '<div style="float:left; height:2.0em;"><a href="index.php?mnum=2" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[2].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_02.'</a></div>';
    echo '<div style="float:left; height:2.0em;"><a href="index.php?mnum=3" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[3].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_03.'</a></div>';
    echo '<div style="float:left; height:2.0em;"><a href="index.php?mnum=4" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[4].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_04.'</a></div>';
    echo '<div style="clear: both ; height:0px; visibility:hidden;"></div>';

    echo '<div style="float:left; height:2.0em;"><a href="index.php?mnum=5" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[5].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_05.'</a></div>';
    echo '<div style="float:left; height:2.0em;"><a href="index.php?mnum=6" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[6].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_06.'</a></div>';
    echo '<div style="float:left; height:2.0em;"><a href="index.php?mnum=7" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[7].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_07.'</a></div>';
    echo '<div style="float:left; height:2.0em;"><a href="index.php?mnum=8" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[8].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_08.'</a></div>';

    echo '<div style="float:left; height:2.0em;"><a href="index.php?mnum=99" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[99].'; text-decoration: none; ">'._AD_MULTIMENU_ADMIN_99.'</a></div>';
    echo '<div style="float:left; height:2.0em;"><a href="myblocksadmin.php" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '.$tblColors[0].'; text-decoration: none; ">'._AM_BADMIN.'</a></div>';
    echo '<div style="float:left; height:2.0em;"><a href="'.XOOPS_MODULE_URL.'/legacy/admin/index.php?action=PreferenceEdit&confmod_id='.$this->root->mContext->mModule->mXoopsModule->get('mid').'" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: #DDE; text-decoration: none; ">'._PREFERENCES.'</a></div>';
    echo '<div style="float:left; height:2.0em;"><a href="'.XOOPS_MODULE_URL.'/legacy/admin/index.php?action=Help&dirname=multiMenu" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: #DDE; text-decoration: none; ">'._HELP.'</a></div>';
    echo '<div style="clear: both ; height:0px; visibility:hidden;"></div>';
    echo '</div>';

    echo '<br />';
  }
  private function im_admin_clean(){
    global $xoopsDB;
    $i=0;
    $db = $xoopsDB->prefix( "multimenu".$this->menu_num );
    $result = $xoopsDB->query("SELECT id FROM ".$db." ORDER BY weight ASC");
    while (list($id) = $xoopsDB->fetchrow($result)) {
      $xoopsDB->queryF("UPDATE ".$db." SET weight='$i' WHERE id=$id");
      $i++;
    }
  }
  public function im_admin_list(){
    xoops_cp_header();
    $this->mm_admin_menu(intval($this->mnum), _AD_MULTIMENU_ADMIN.$this->mnum );

    echo '<fieldset style="padding: 5px;"><legend style="font-weight: bold; color: #900;">'. _AD_MULTIMENU_ADMIN . $this->mnum . '</legend>';
    echo '<form action="index.php?mnum='.$this->mnum.'&op=new" method="post" name="form1">
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="outer"><tr>
    <th align="center">'._AD_MULTIMENU_TITLE.'</th>
    <th align="center">'._AD_MULTIMENU_HIDE.'</th>
    <th align="center">'._AD_MULTIMENU_LINK.'</th>
    <th align="center">'._AD_MULTIMENU_OPERATION.'</th></tr>';

    $modhand = xoops_getmodulehandler('menu');
    $modhand->setTable($this->mnum);
    $mCriteria = new CriteriaCompo();
    $mCriteria->addSort('weight');
    $modhand->im_admin_clean();
    $objcts = $modhand->getObjects($mCriteria);
    $class = 'even';
    foreach ( $objcts as $obj ) {
      if ($obj->get('weight') != 0) {
        $moveup = "<a href='index.php?mnum=".$this->mnum."&op=move&id=".$obj->get('id')."&weight=".($obj->get('weight')-1).$gticket_param."'>["._AD_MULTIMENU_UP."]</a>";
      } else {
        $moveup = "["._AD_MULTIMENU_UP."]";
      }
      if ($obj->get('weight') != (count($objcts)  - 1)) {
        $movedown = "<a href='index.php?mnum=".$this->mnum."&op=move&id=".$obj->get('id')."&weight=".($obj->get('weight')+2).$gticket_param."'>["._AD_MULTIMENU_DOWN."]</a>";
      } else {
        $movedown = "["._AD_MULTIMENU_DOWN."]";
      }
//fix by domifara Notice [PHP]: Undefined variable: status
      $status = $obj->get('hide')? _YES :_NO ;
      echo "<tr>
        <td class='$class'>".$obj->get('title')."</td>
        <td class='$class' align='center'>$status</td>
        <td class='$class'>".$obj->get('link')."</td>
        <td class='$class' align='center'><small><a href='index.php?mnum=".$this->mnum."&op=del&id=".$obj->get('id')."'>["._DELETE."]</a>
        <a href='index.php?mnum=".$this->mnum."&op=edit&id=".$obj->get('id')."'>["._EDIT."]</a>".$moveup.$movedown."</small></td></tr>";
      $class = ($class == 'odd') ? 'even' : 'odd';
    }
    echo "<tr><td class='foot' colspan='4' align='right'>";
//	echo $GLOBALS['xoopsSecurity']->getTokenHTML();
	echo $GLOBALS['xoopsGTicket']->getTicketHtml( __LINE__ );
    echo "<input type='submit' name='submit' value='"._AD_MULTIMENU_NEW."'>
    </td></tr></table></form>";
    echo "</fieldset><br />";
    xoops_cp_footer();
  }
  public function im_admin_new() {
	if ( ! $GLOBALS['xoopsGTicket']->check() ) {
		redirect_header('index.php',3,$GLOBALS['xoopsGTicket']->getErrors());
	}
  	global $xoopsDB;
    xoops_cp_header();
    $this->mm_admin_menu(intval($this->mnum), _AD_MULTIMENU_ADMIN.$this->mnum );
    echo "<fieldset style='padding: 5px;'><legend style='font-weight: bold; color: #900;'>". _AD_MULTIMENU_ADMIN . $this->mnum . "</legend>";

    $id = 0;
    $title = '';
    $link = '';
    $hide = '';
    $weight = 255;
    $target = "_self";
    $member_handler = xoops_gethandler('member');
    $xoopsgroups = $member_handler->getGroups();
    $count = count($xoopsgroups);
    $groups = array();
    for ($i = 0; $i < $count; $i++)  $groups[] = $xoopsgroups[$i]->getVar('groupid');
    include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
    $form = new XoopsThemeForm(_AD_MULTIMENU_NEWIMENU, "newform", "index.php?mnum=".$this->mnum);

    $formtitle = new XoopsFormText(_AD_MULTIMENU_TITLE, "title", 50, 150, "");
    $formlink = new XoopsFormText(_AD_MULTIMENU_LINK, "link", 50, 255, "");
    $formhide = new XoopsFormSelect(_AD_MULTIMENU_HIDE, "hide", "");
    $formhide->addOption("0", _NO);
    $formhide->addOption("1", _YES);
    $formtarget  = new XoopsFormSelect(_AD_MULTIMENU_TARGET, "target", "_self");
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
    $form->addElement(new XoopsFormHidden("id", 0));
    $form->addElement(new XoopsFormHidden("op", "update"));
    $form->addElement($submit_button);

//for gticket by domifara
    $GLOBALS['xoopsGTicket']->addTicketXoopsFormElement( $form , __LINE__  ) ;

    $form->display();
    echo "</fieldset><br />";
    xoops_cp_footer();
  }
/*
  private function im_admin_update_flow(&$obj){
  	$block_id = isset($_POST['block_id']) ? intval($_POST['block_id']) : 0;
  	$parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : 0;
  	$title = isset($_POST['title']) ? $_POST['title'] : 'NoTitle';
  	$link = isset($_POST['link']) ? $_POST['link'] : 'http://www.google.co.jp/';
  	$hide = empty($_POST['hide']) ? 0 : 1;
  	$groups = isset($_POST['groups']) ? $_POST['groups'] : '';
  	$groups = (is_array($groups)) ? implode(" ", $groups) : '';
  	$target = isset($_POST['target']) ? $_POST['target'] : '_self';

  	$obj->set('block_id', $block_id);
  	$obj->set('parent_id', $parent_id);
  	$obj->set('title', $title);
  	$obj->set('hide', $hide);
  	$obj->set('link', $link);
  	$obj->set('weight', 255);
  	$obj->set('target', $target);
  	$obj->set('groups', $groups);
  }
*/
  private function im_admin_update_menu(&$obj){
  	$title = isset($_POST['title']) ? $this->root->mContext->mRequest->getRequest('title') : 'NoTitle';
  	$link = isset($_POST['link']) ? $this->root->mContext->mRequest->getRequest('link') : 'http://www.google.co.jp/';
  	$hide = empty($_POST['hide']) ? 0 : 1;
  	$groups = isset($_POST['groups']) ? $this->root->mContext->mRequest->getRequest('groups') : '';
  	$groups = (is_array($groups)) ? implode(" ", array_map( 'intval' , $groups ) ) : '';
  	$target = isset($_POST['target']) ? $this->root->mContext->mRequest->getRequest('target') : '_self';
  	//$obj->set('id', $id);
  	$obj->set('title', $title);
  	$obj->set('hide', $hide);
  	$obj->set('link', $link);
  	$obj->set('weight', 255);
  	$obj->set('target', $target);
  	$obj->set('groups', $groups);
  }
  public function im_admin_update(){
	if ( ! $GLOBALS['xoopsGTicket']->check() ) {
		redirect_header('index.php',3,$GLOBALS['xoopsGTicket']->getErrors());
	}
  	$modhand = xoops_getmodulehandler('menu');
    $modhand->setTable($this->mnum);
  	$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if ( $id == 0 ) {
		$obj = $modhand->create();
    } else {
    	$obj = $modhand->get($id);
    }
    $this->im_admin_update_menu($obj);
    $success = $modhand->insert($obj);
    if ( !$success ) {
      redirect_header("index.php?mnum=".$this->mnum,2,_AD_MULTIMENU_UPDATED);
    }else {
      $modhand->im_admin_clean();
      redirect_header("index.php?mnum=".$this->mnum,2,_AD_MULTIMENU_UPDATED);
    }
    exit();
  }
  public function im_admin_edit () {
    xoops_cp_header();
    $this->mm_admin_menu(intval($this->mnum), _AD_MULTIMENU_ADMIN.$this->mnum );
    echo "<fieldset style='padding: 5px;'><legend style='font-weight: bold; color: #900;'>". _AD_MULTIMENU_ADMIN . $this->mnum . "</legend>";

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $modhand = xoops_getmodulehandler('menu');
    $modhand->setTable($this->mnum);
    $obj = $modhand->get($id);

    $groups = explode(" ", $obj->get('groups'));
    include XOOPS_ROOT_PATH."/class/xoopsformloader.php";
    $form = new XoopsThemeForm(_AD_MULTIMENU_EDITIMENU, "editform", "index.php?mnum=".$this->mnum);
    $formtitle = new XoopsFormText(_AD_MULTIMENU_TITLE, "title", 50, 150, $obj->get('title'));
    $formlink = new XoopsFormText(_AD_MULTIMENU_LINK, "link", 50, 255, $obj->get('link'));
    /*
     * for future reqest 
     if ($this->mnum=="99"){
    	$block_id  = new XoopsFormText(_AD_MULTIMENU_BLOCKID , "block_id" , 5, 5, $obj->get('block_id'));
    	$parent_id = new XoopsFormText(_AD_MULTIMENU_PARENTID, "parent_id", 5, 5, $obj->get('parent_id'));
    }
     */
    $formhide = new XoopsFormSelect(_AD_MULTIMENU_HIDE, "hide", $obj->get('hide'));
    $formhide->addOption("0", _NO);
    $formhide->addOption("1", _YES);
    $formtarget  = new XoopsFormSelect(_AD_MULTIMENU_TARGET, "target", $obj->get('target'));
    $formtarget->addOption("_self", _AD_MULTIMENU_TARG_SELF);
    $formtarget->addOption("_blank", _AD_MULTIMENU_TARG_BLANK);
    $formtarget->addOption("_parent", _AD_MULTIMENU_TARG_PARENT);
    $formtarget->addOption("_top", _AD_MULTIMENU_TARG_TOP);
    $formgroups = new XoopsFormSelectGroup(_AD_MULTIMENU_GROUPS, "groups", true, $groups, 5, true);
    $submit_button = new XoopsFormButton("", "submit", _AD_MULTIMENU_SUBMIT, "submit");

    $form->addElement($formtitle, true);
    $form->addElement($formlink, false);
    $form->addElement($block_id, false);
    $form->addElement($parent_id, false);
    $form->addElement($formhide);
    $form->addElement($formtarget);
    $form->addElement($formgroups);
    $form->addElement(new XoopsFormHidden("id", $id));
    $form->addElement(new XoopsFormHidden("op", "update"));
    $form->addElement($submit_button);

//for gticket by domifara
    $GLOBALS['xoopsGTicket']->addTicketXoopsFormElement( $form , __LINE__  ) ;

    $form->display();
    echo "</fieldset><br />";
    xoops_cp_footer();
  }
  public function im_admin_del () {
    $del = isset($_POST['del']) ? 1 : 0;
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ( $del == 1 ) {
		if ( ! $GLOBALS['xoopsGTicket']->check() ) {
			redirect_header('index.php',3,$GLOBALS['xoopsGTicket']->getErrors());
		}
      $id = isset($_POST['id']) ? intval($_POST['id']) : $id;
      $modhand = xoops_getmodulehandler('menu');
      $modhand->setTable($this->mnum);
      $obj = $modhand->get($id);

      if ( $modhand->delete($obj) ) {
        $modhand->im_admin_clean($this->mnum);
        redirect_header("index.php?mnum=".$this->mnum, 2, _AD_MULTIMENU_UPDATED);
      } else {
        redirect_header("index.php?mnum=".$this->mnum, 2, _AD_MULTIMENU_NOTUPDATED);
      }
      exit();
    } else {
      xoops_cp_header();
      echo "<h4>"._AD_MULTIMENU_ADMIN.$this->mnum."</h4>";
      xoops_confirm(array('op' => 'del', 'id' => $id, 'del' => 1) + $GLOBALS['xoopsGTicket']->getTicketArray( __LINE__ ), 'index.php?op=del&mnum='.$this->mnum, _AD_MULTIMENU_SUREDELETE);
      xoops_cp_footer();
    }
  }
  public function im_admin_move () {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $weight = isset($_GET['weight']) ? intval($_GET['weight']) : 0;
    $db = $this->db->prefix( "multimenu".$this->mnum );
    $this->db->queryF("UPDATE `".$db."` SET `weight` = `weight` + 1 WHERE `weight` >= ".$weight." AND `id` <> ".$id);
    $this->db->queryF("UPDATE `".$db."` SET `weight` = ".$weight." WHERE `id` = ".$id);
    $modhand = xoops_getmodulehandler('menu');
    $modhand->im_admin_clean($this->mnum);
  }
}
?>