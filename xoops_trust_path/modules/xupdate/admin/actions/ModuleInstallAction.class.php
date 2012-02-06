<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit;
}

require_once XUPDATE_TRUST_PATH . '/class/AbstractAction.class.php';
require_once XUPDATE_TRUST_PATH . '/class/Root.class.php';

/**
 * Xupdate_Admin_StoreAction
*
 * @property mixed downloadUrlFormat
 */
class Xupdate_Admin_ModuleInstallAction extends Xupdate_AbstractAction
{

	protected $Xupdate  ;	// Xupdate instance
	protected $Ftp  ;	// FTP instance
	protected $Func ;	// Functions instance
	protected $mod_config ;
	protected $content ;

	protected $downloadDirPath;
	protected $exploredDirPath;
	protected $downloadUrlFormat;
	protected $targetKeyName;
	protected $targetType;

	/**
	 * getDefaultView
	 *
	 * @param	void
	 *
	 * @return	Enum
	**/


	public function __construct()
	{
		parent::__construct();

		$this->mRoot =& XCube_Root::getSingleton();
        $this->mModule =& $this->mRoot->mContext->mModule;
        $this->mAsset =& $this->mModule->mAssetManager;

		// Xupdate_ftp class object
		require_once XUPDATE_TRUST_PATH .'/class/Root.class.php';

		$this->Xupdate = new Xupdate_Root ;// Xupdate instance
		$this->Ftp =& $this->Xupdate->Ftp ;		// FTP instance
		$this->Func =& $this->Xupdate->func ;		// Functions instance
		$this->mod_config = $this->mRoot->mContext->mModuleConfig ;	// mod_config
		//	adump($this->mod_config);
		//	adump($this->Ftp);
        //$this->targetKeyName = $this->mRoot->mContent->mRequest->getRequest('target_key');
        //$this->targetType = $this->mRoot->mContent->mRequest->getRequest('target_key');
        $this->targetKeyName = $this->Xupdate->get('target_key');
        $this->targetType = $this->Xupdate->get('target_type');
        $this->downloadDirPath = $this->Xupdate->params['temp_path'];
        $this->downloadUrlFormat = $this->mod_config['Mod_download_Url_format'];

	}

    public function execute(&$controller, &$xoopsUser)
    {
    }

    public function getDefaultView()
    {
        return XUPDATE_FRAME_VIEW_SUCCESS;
    }

    /**
     * executeViewSuccess
     *
     * @param	XCube_RenderTarget	&$render
     *
     * @return	void
     **/

	public function executeViewSuccess(&$render)
	{

		if( $this->Xupdate->params['is_writable']['result'] === true ) {
			$this->_downloadFile();
			if($this->_unzipFile()==true) {
				// ToDo port , timeout
				if($this->Ftp->app_login("127.0.0.1")==true) {
					$this->uploadFiles();
				}
			}
			$this->Ftp->app_logout();
			$this->content.= 'cleaning up... <br />';
			$this->_cleanup($this->exploredDirPath);
			$this->content.= 'completed <br /><br />';
			$this->content.= $this->_get_nextlink($this->targetKeyName);
		}

		$render->setAttribute('xupdate_writable', $this->Xupdate->params['is_writable']);
       		$render->setAttribute('xupdate_content', $this->content);
		$render->setAttribute('xupdate_message', $this->Ftp->getMes());

		$render->setTemplateName('admin_module_install.html');
		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());
    }

	/**
	 * @public
	 */
	protected function &_getHandler()
	{
	//	$handler =& $this->mAsset->load('handler', "Module");
	//	return $handler;
	}


    private function _downloadFile()
        {
            chdir($this->downloadDirPath);
            mkdir($this->targetKeyName);
            $this->exploredDirPath = $this->downloadDirPath.'/'.$this->targetKeyName;
                $this->Ftp->appendMes('downladed in: '.$this->downloadDirPath.'<br />');
                $this->content.= 'downladed in: '.$this->downloadDirPath.'<br />';
            chdir($this->exploredDirPath);

            // TODO ファイルNotFound対策
            $url = sprintf($this->downloadUrlFormat, $this->targetKeyName);
            $downloadedFilePath = $this->_getDownloadFilePath();

            $ch = curl_init($url);
            if($ch === false){
                throw new Exception(t('curl_init fail'), 1);
                $this->Ftp->appendMes('curl_init fail<br />');
            } else {
                $this->Ftp->appendMes('curl_init OK<br />');
            }

            $fp = fopen($downloadedFilePath, "w");

            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);

            $result = curl_exec($ch);
            if($result === false){
                throw new Exception("curl exec fail", 1);
                $this->Ftp->appendMes('curl exec fail<br />');
            } else {
                $this->Ftp->appendMes('curl exec OK<br />');
            }
            fclose($fp);
        }

    private function _unzipFile()
    {
        // local file name
        $downloadPath = $this->_getDownloadFilePath();

        chdir($this->exploredDirPath);

        $zip = new ZipArchive;
        if ($zip->open($downloadPath) === TRUE) {
            $zip->extractTo('./');
            $zip->close();
            $this->Ftp->appendMes('explored in: '.$this->exploredDirPath.'<br />');
                $this->content.= 'explored in: '.$this->exploredDirPath.'<br />';
       } else {
            throw new Exception("unzip fail", 1);
            $this->Ftp->appendMes('unzip fail<br />');
       }

        return true;
    }

    private function _upload () {

        $this->Ftp->app_login("127.0.0.1") ;
		//$this->uploadFiles();
		//$this->Ftp->app_logout();

	}

    private function uploadFiles()
    {
         //$this->Ftp->connect();

        $this->Ftp->appendMes( 'start uploading..<br />');
        $this->content.=  'uploading..<br />';

        // copy html
        $uploadPath = XOOPS_ROOT_PATH . '/' ;
        $unzipPath =  $this->exploredDirPath .'/html';
        $this->Ftp->uploadNakami($unzipPath, $uploadPath);

        // copy xoops_trust_path
        $uploadPath = XOOPS_TRUST_PATH . '/' ;
        $unzipPath =  $this->exploredDirPath . '/xoops_trust_path';
        $this->Ftp->uploadNakami($unzipPath, $uploadPath);


    }

    private function _getDownloadFilePath()
    {
        //$downloadPath = sprintf( $this->downloadUrlFormat, 'd3diary') ;
        $downloadPath = $this->downloadDirPath .'/'. $this->targetKeyName . '.tgz';
        //$downloadPath = TP_ADDON_MANAGER_TMP_PATH .'/'. $this->targetKeyName . '.tgz';
        return $downloadPath;
    }

    private function _cleanup($dir)
    {
        if ($handle = opendir("$dir")) {
            while (false !== ($item = readdir($handle))) {
                if ($item != "." && $item != "..") {
                    if (is_dir("$dir/$item")) {
                        $this->_cleanup("$dir/$item");
                        $this->Ftp->appendMes('removing directory: '.$dir.'/'.$item.'<br />');
                    } else {
                        unlink("$dir/$item");
                   }
                }
            }
            closedir($handle);
            rmdir($dir);
        }
    }

	private function _get_nextlink($targetKeyName)
	{
		$ret ='';
		$hModule = Xupdate_Utils::getXoopsHandler('module');
		$module =& $hModule->getByDirname($targetKeyName) ;
		if (is_object($module)){
			if ($module->getVar('isactive') ){
				$ret ='<a href="'.XOOPS_URL.'/modules/legacy/admin/index.php?action=ModuleUpdate&dirname='.$targetKeyName.'">'._MI_XUPDATE_LANG_UPDATE.'</a>';
			}else{
				$ret =_AD_LEGACY_LANG_BLOCK_INACTIVETOTAL;
			}
		}else{
			$ret ='<a href="'.XOOPS_URL.'/modules/legacy/admin/index.php?action=ModuleInstall&dirname='.$targetKeyName.'">'._MI_XUPDATE_LANG_UPDATE.'</a>';
		}
		return $ret;
	}



}

?>