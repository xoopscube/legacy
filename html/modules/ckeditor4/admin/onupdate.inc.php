<?php
/**
 * CKEditor4 module for XCL
 * @package    CKEditor4
 * @version    2.3.1
 * @author     Other authors Nuno Luciano (aka gigamaster), 2020, XCL PHP7
 * @author     Naoki Sawada (aka nao-pon) <https://xoops.hypweb.net/>
 * @copyright  2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

function xoops_module_update_ckeditor4()
{
	$module_handler = xoops_gethandler('module');
	$Module = $module_handler->getByDirname('ckeditor4');
	$config_handler = xoops_gethandler('config');
	$mid = $Module->mid();
	$ModuleConfig = $config_handler->getConfigsByCat(0, $mid);

	if (substr($ModuleConfig['toolbar_user'], -4) === '""]]') {
		//fix typo '""]]' to '"]]' for version <= 0.37
		$criteria = new CriteriaCompo(new Criteria('conf_modid', $mid));
		$criteria->add(new Criteria('conf_catid', 0));
		$criteria->add(new Criteria('conf_name', 'toolbar_user'));

		if ($configs = $config_handler->_cHandler->getObjects($criteria)) {
			$val = str_replace('""]]', '"]]', $ModuleConfig['toolbar_user']);
			$configs[0]->setVar('conf_value', $val, true);
			$config_handler->insertConfig($configs[0]);
		}
	}

	if (preg_match('/^head\s*$/m', $ModuleConfig['contentsCss'])) {
		//fix typo 'head' to '<head>' for version <= 0.38
		$criteria = new CriteriaCompo(new Criteria('conf_modid', $mid));
		$criteria->add(new Criteria('conf_catid', 0));
		$criteria->add(new Criteria('conf_name', 'contentsCss'));

		if ($configs = $config_handler->_cHandler->getObjects($criteria)) {
			$val = preg_replace('/^head(\s*)$/m', '<head>$1', $ModuleConfig['contentsCss']);
			$configs[0]->setVar('conf_value', $val, true);
			$config_handler->insertConfig($configs[0]);
		}
	}
	return true;
}
