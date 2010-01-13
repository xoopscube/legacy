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

class Hdinstaller_Form_Json extends Hdinstaller_ActionForm
{
	/// 
	/**
	 * @brief 
	 * @param 
	 * @retval
	 */
	function __construct(&$c)
	{

		$this->setDef(null, array(
			'ftp_username' => array(
				'name' => _('FTP user name'),
				'type' => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_TEXT,
				),
			'ftp_password' => array(
				'name' => _('FTP password'),
				'type' => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_PASSWORD,
				),
			'repository_url' => array(
				'name' => _('Repository URL'),
				'type' => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_TEXT,
				),
			'target_package' => array(
				'name' => _('Target Package'),
				'type' => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_RADIO,
				),
			'xoops_root_path' => array(
				'name' => _('Path to XOOPS Top page'),
				'type' => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_TEXT,
				),
			'xoops_trust_path' => array(
				'name' => _('Path to XOOPS program files.'),
				'type' => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_TEXT,
				),
			));
		
		parent::__construct($c);
		
	}
	
	
		/// 
	/**
	 * @brief 
	 * @param 
	 * @retval
	 */
	function setRequired($names=null)
	{
		if (!is_null($names)){
			foreach ($names as $name){
				if (isset($this->form[$name])){
					$this->form[$name]['required'] = true;
				}
			}
		}
		else {
			$def = $this->getDef();
			foreach (array_keys($def) as $name){
				$this->form[$name]['required'] = true;
			}
		}
	}

    /**
     *  Form input value convert filter : sample
     *
     *  @access protected
     *  @param  mixed   $value  Form Input Value
     *  @return mixed           Converted result.
     */
    /*
    function _filter_sample($value)
    {
        //  convert to upper case.
        return strtoupper($value);
    }
    */
}

/**
 *  Index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Hdinstaller
 */
class Hdinstaller_Action_Json extends Hdinstaller_ActionClass
{
    /**
     *  preprocess Index action.
     *
     *  @access    public
     *  @return    string  Forward name (null if no errors.)
     */
    function prepare()
    {
        /**
        if ($this->af->validate() > 0) {
            return 'error';
        }
        $sample = $this->af->get('sample');
        */
        return null;
    }

    /**
     *  Index action implementation.
     *
     *  @access    public
     *  @return    string  Forward Name.
     */
    function perform()
    {
        return 'json';
    }
	
	
	/// chroot‚³‚ê‚Ä‚Ä‚à‘åä•v‚È‚æ‚¤‚ÉAFTP‚ÌROOT‚ðÌ‚é
    /**
     *  Index action implementation.
     *
	 *  @param     resource $conn_id ftp connection resource
     *  @return    string  ftp root path
     */
	function seekFTPRoot($conn_id)
	{
		static $ftp_root ;
		
		if (!is_null($ftp_root)){
			return $ftp_root ;
		}
		
		$path = explode(DIRECTORY_SEPARATOR, BASE);
		
		$current_path = '';
		for ($i=count($path)-1; $i>=0 ;$i--){
			$current_path = DIRECTORY_SEPARATOR.$path[$i].$current_path;
			if (@ftp_chdir($conn_id, $current_path)){
				$ftp_root = substr(BASE, 0, strrpos(BASE, $current_path));
				return $ftp_root;
			}
		}
		
		return false;
	}
}
