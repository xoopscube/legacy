<?php
class xpwiki_plugin_includesubmenu extends xpwiki_plugin {
	function plugin_includesubmenu_init () {



	}
	// $Id: includesubmenu.inc.php,v 1.4 2009/01/04 11:44:55 nao-pon Exp $
	
	function plugin_includesubmenu_convert()
	{
	//  global $script,$vars;
	
	  $ShowPageName = FALSE;
	
	  if (func_num_args()) {
	    $aryargs = func_get_args();
	    if ($aryargs[0] == 'showpagename') {
	      $ShowPageName = TRUE;
	    }
	  }
	
	  $SubMenuPageName = '';
	
	  if ($this->root->render_mode === 'block') {
	  	$tmppage = $GLOBALS['Xpwiki_'.$this->root->mydirname]['page'];
	  } else {
	  	$tmppage = $this->func->strip_bracket($this->root->vars['page']);
	  }
	  //下階層のSubMenuページ名
	  $SubMenuPageName1 = $tmppage . '/SubMenu';
	
	  //同階層のSubMenuページ名
	  $LastSlash= strrpos($tmppage,'/');
	  if ($LastSlash === FALSE) {
	    $SubMenuPageName2 = 'SubMenu';
	  } else {
	    $SubMenuPageName2 = substr($tmppage,0,$LastSlash) . '/SubMenu';
	  }
	  //echo "$SubMenuPageName1 <br>";
	  //echo "$SubMenuPageName2 <br>";
	  //下階層にSubMenuがあるかチェック
	  //あれば、それを使用
	  if ($this->func->is_page($SubMenuPageName1)) {
	    //下階層にSubMenu有り
	    $SubMenuPageName = $SubMenuPageName1;
	  }
	  else if ($this->func->is_page($SubMenuPageName2)) {
	    //同階層にSubMenu有り
	    $SubMenuPageName = $SubMenuPageName2;
	  }
	  else {
	    //SubMenu無し
	    return "";
	  }
	
	  $body = $this->func->convert_html($this->func->get_source($SubMenuPageName), $tmppage);
	
	  if ($ShowPageName) {
	    $r_page = rawurlencode($SubMenuPageName);
	    $s_page = htmlspecialchars($SubMenuPageName);
	    $link = "<a href=\"{$this->root->script}?cmd=edit&amp;page=$r_page\">$s_page</a>";
	    $body = "<h1>$link</h1>\n$body";
	  }
	  return $body;
	}
}
?>