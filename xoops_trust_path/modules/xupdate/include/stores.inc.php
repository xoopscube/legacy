<?php
// for test
		$stores = array();
		$items = array();

//-------------------------------------

		// sf.jp
		$_store = array();
		$_store['sid'] = '1';
		$_store['name'] = 'sourceforge.jp pack2012 '._SYS_RECOMMENDED_MODULES.'modules';
		$_store['addon_url'] = 'http://sourceforge.jp/projects/xoopscube22x/svn/view/files/%s.zip?view=co&root=xoopscube22x';
		//$stores[$_store['sid']]=$_store;

//-------------------------------------

		// Xoops X (Ten) - GitHub
		$_store = array();
		$_store['sid'] = '2';
		$_store['name'] = 'Xoops X (Ten) - GitHub';
		$_store['addon_url'] = 'http://www.naaon.com/uploads/xupdate/modules_xoopsx.txt';
		$stores[$_store['sid']]=$_store;

//-------------------------------------

		// sf.jp
		$_store = array();
		$_store['sid'] = '3';
		$_store['name'] = 'sourceforge.jp pack2012 '._SYS_OPTION_MODULES.'X2Module modules';
		$_store['addon_url'] = 'http://sourceforge.jp/projects/xoopscube22x/svn/view/files/%s.zip?view=co&root=xoopscube22x';
		//$stores[$_store['sid']]=$_store;

//-------------------------------------

		// naao - GitHub
		$_store = array();
		$_store['sid'] = '4';
		$_store['name'] = 'naao - GitHub';
		$_store['addon_url'] = 'http://www.naaon.com/uploads/xupdate/modules_naao.txt';
		$stores[$_store['sid']]=$_store;

//-------------------------------------

		// xodomifara.lolipop.jp
		$_store = array();
		$_store['sid'] = '5';
		$_store['name'] = 'xodomifara.lolipop.jp test';
		$_store['addon_url'] = 'http://xodomifara.lolipop.jp/karidown/%s.zip';
		//$stores[$_store['sid']]=$_store;

//-------------------------------------

		// nao-pon - GitHub
		$_store = array();
		$_store['sid'] = '6';
		$_store['name'] = 'nao-pon - GitHub';
		$_store['addon_url'] = 'https://github.com/nao-pon/%s/zipball/master';
		//$stores[$_store['sid']]=$_store;

?>