<?php
/**
 *  Index.php
 *
 *  @author    {$author}
 *  @package   Hdinstaller
 *  @version   $Id: app.action.default.php 573 2008-06-08 01:43:28Z mumumu-org $
 */

/**
 *  Index form implementation
 *
 *  @author    {$author}
 *  @access    public
 *  @package   Hdinstaller
 */
require_once dirname(__FILE__).".php";
class Hdinstaller_Form_JsonDownloadpackage extends Hdinstaller_Form_Json
{
	function __construct(&$c)
	{
		parent::__construct($c);
		$this->setRequired();
	}
}

/**
 *  Index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Hdinstaller
 */
class Hdinstaller_Action_JsonDownloadpackage extends Hdinstaller_Action_Json
{
    /**
     *  preprocess Index action.
     *
     *  @access    public
     *  @return    string  Forward name (null if no errors.)
     */
    function prepare()
    {
		if ($this->af->validate() == 0){
			/// download file
			
			$url = sprintf('%s/repository.sphp', rtrim($this->af->get('repository_url'), '/'));
			$cache_file = $this->backend->ctl->repositoryURL2CacheFile($url);
			$repo_data = unserialize(file_get_contents($cache_file));
			
			list($package, $version) = explode('@', $this->af->get('target_package'));
			
			$urls = array();
			foreach ($repo_data as $package_name => $package_data){
				if ($package_name == $package){
					foreach ($package_data as $_pdata){
						if ($_pdata['version'] == $version){
							$urls = $_pdata['urls'];
							$filesize = $_pdata['size'];
						}
					}
				}
			}
			
			
			require_once 'HTTP/Request.php';
			$req = new HTTP_Request();
			$req->setMethod(HTTP_REQUEST_METHOD_HEAD);
			$command = 'no command';
			foreach ($urls as $_url_data){
				$_url = $_url_data['url'];
				$req->setURL($_url);
				$req->sendRequest();
				if ($req->getResponseCode() == "302"){
					$headers = $req->getResponseHeader();
					$req->setURL($headers['location']);
				}
				$req->sendRequest();
				if ($req->getResponseCode() == '200'){
					$data_file = $this->backend->ctl->package2dataFile($package, $version);
					if ($this->fetchTgzFile($data_file, $req->getUrl())){
						if (filesize($data_file) == $filesize || !$filesize){
							chmod($data_file, 0666);
							return null;
						}
					}
				}
			}
			$this->ae->add('wget failed',
						   _('file download failed.').'[debug]'.sprintf('SIZE:[datafile,%d => repos,%d]', filesize($data_file), $filesize));
		}
        return 'json_error_reload';
    }

    /**
     *  Index action implementation.
     *
     *  @access    public
     *  @return    string  Forward Name.
     */
    function perform()
    {
		$this->af->setApp('result', 1);
		$this->af->setApp('next_message', _('Extract dowloaded files'));
        return 'json';
    }
	
	
	
	
	/// Socketでtgzファイルを取る
	/**
	 * @brief 
	 * @param string $data_file Saveするファイルのパス
	 * @param string $url データのURL
	 * @retval bool
	 */
	function fetchTgzFile($data_file, $url)
	{
		/*					exec('which wget || which curl', $which, $status);
					$command = strpos($which[0], 'wget')!==false
					  ? sprintf('%s -O %s %s', $which[0], $data_file, $_url)
						: sprintf('%s -o %s %s', $which[0], $data_file, $_url);
					exec($command, $result, $status);
					return $status === 0;
		 */
		if ($fp = fopen($data_file, 'w')){
			$url = parse_url($url);
			if (isset($url['port']) && $url['port']){
				$port = $url['port'];
			}
			else {
				$port = $url['scheme']=='https' ? '443' : '80' ;
			}
			$path = (isset($url['path']) && $url['path']) ? $url['path'] : '/';
			$path .= (isset($url['query']) && $url['query']) ? $url['query'] : '';

			require_once 'Net/Socket.php';
			$sock = new Net_Socket();
			$connect = $sock->connect($url['host'], $port);
			if ($connect)
			{
				$sock->writeLine("GET $path  HTTP/1.1");
				$sock->writeLine("Host: ".$url['host']);
				$sock->writeLine("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; ja; rv:1.9.0.7) Gecko/2009021910 Firefox/3.0.7 (.NET CLR 3.5.30729)");
				$sock->writeLine("Keep-Alive: 1000");
				$sock->writeLine("Connection: keep-alive");
				$sock->writeLine("");

				$null_line = false;
				while (!$sock->eof()) {
					if ($null_line === false){
						$_sock_d = $sock->readLine();
						if ($_sock_d == ""){
							$null_line = true;
						}
					}
					if ($null_line === true){
						$_sock_d = $sock->read(1024);
						fputs($fp, $_sock_d);
					}
				}
				$sock->disconnect();
			}
			fclose($fp);
			return true;
		}
		return false;
	}
}
