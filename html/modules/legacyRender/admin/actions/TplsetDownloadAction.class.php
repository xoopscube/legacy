<?php
/**
 * @package legacyRender
 * @version $Id: TplsetDownloadAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacyRender/admin/forms/TplfileEditForm.class.php";

class LegacyRender_TplsetDownloadAction extends LegacyRender_Action
{
	var $mPreparedFlag = false;
	
	var $mTplset = null;
	
	var $mDownloader = null;
	
	function &_createDownloader($method)
	{
		$ret = null;
		
		switch ($method) {
			case 'tar':
				if (@function_exists('gzencode')) {
					require_once XOOPS_ROOT_PATH . "/class/tardownloader.php";
					$ret =new XoopsTarDownloader();
				}
				break;
			case 'zip':
				if (@function_exists('gzcompress')) {
					require_once XOOPS_ROOT_PATH . "/class/zipdownloader.php";
					$ret =new XoopsZipDownloader();
				}
				break;
		}
		
		return $ret;
	}

	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		$method = xoops_getrequest('method') == 'tar' ? 'tar' : 'zip';
		$this->mDownloader =& $this->_createDownloader($method);
		
		if ($this->mDownloader == null) {
			return LEGACYRENDER_FRAME_VIEW_ERROR;
		}
		
		$id = xoops_getrequest('tplset_id');
		
		$handler =& xoops_getmodulehandler('tplset');
		$this->mTplset =& $handler->get($id);
		
		if ($this->mTplset == null) {
			return LEGACYRENDER_FRAME_VIEW_ERROR;
		}

		$xml = "<?xml version=\"1.0\"?>" . "\n" .
		       "<tplset>" . "\n" .
		       "  <name>" . $this->mTplset->getShow('tplset_name') . "</name>" . "\n" .
		       "  <dateCreated>" . $this->mTplset->getShow('tplset_created') . "</dateCreated>" . "\n" .
		       "  <credits>" . $this->mTplset->getShow('tplset_credits') . "</credits>" . "\n" .
			   "  <generator>" . XOOPS_VERSION. "</generator>" . "\n";

		$handler =& xoops_getmodulehandler('tplfile');
		$files =& $handler->getObjects(new Criteria('tpl_tplset', $this->mTplset->get('tplset_name')));
		
		$count = count($files);
		
		if ($count > 0) {
			$xml .= "  <templates>" . "\n";
			for ($i = 0; $i < $count; $i++) {
				$files[$i]->loadSource();
				if ($files[$i]->Source != null) {
					$type = null;
					if ($files[$i]->get('tpl_type') == 'block') {
						$path = $this->mTplset->getShow('tplset_name') . '/templates/' . $files[$i]->getShow('tpl_module') . "/blocks/" . $files[$i]->getShow('tpl_file');
						$type = "block";
					}
					elseif ($files[$i]->get('tpl_type') == 'module') {
						$path = $this->mTplset->getShow('tplset_name') . '/templates/' . $files[$i]->getShow('tpl_module') . "/" . $files[$i]->getShow('tpl_file');
						$type = "module";
					}
					$xml .= '    <template name="' . $files[$i]->getShow('tpl_file') . '">' . "\n" .
					        "      <module>" . $files[$i]->getShow('tpl_module') . "</module>" . "\n" .
					        "      <type>module</type>" . "\n" .
					        "      <lastModified>" . $files[$i]->getShow('tpl_lastmodified') . "</lastModified>" . "\n" .
					        "    </template>" . "\n";
					
					$this->mDownloader->addFileData($files[$i]->Source->get('tpl_source'), $path, $files[$i]->getShow('tpl_lastmodified'));
				}
			}
			
			$xml .= "  </templates>" . "\n";
		}
		
		$xml .= "</tplset>";
		
		$this->mDownloader->addFileData($xml, $this->mTplset->getShow('tplset_name') . '/tplset.xml', time());
		
		return LEGACYRENDER_FRAME_VIEW_SUCCESS;
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		print $this->mDownloader->download($this->mTplset->getShow('tplset_name'), true);
		exit(0);
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect("./index.php?action=TplsetList", 1, _AD_LEGACYRENDER_ERROR_DBUPDATE_FAILED);
	}
}

?>
