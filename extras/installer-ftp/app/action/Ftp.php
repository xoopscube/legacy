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

class Hdinstaller_Form_Ftp extends Hdinstaller_ActionForm
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
				'required' => true,
				),
			'ftp_password' => array(
				'name' => _('FTP password'),
				'type' => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_PASSWORD,
				'required' => true,
				),
			));
		
		parent::__construct($c);
		
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
class Hdinstaller_Action_Ftp extends Hdinstaller_ActionClass
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
        return 'ftp';
    }
}

?>
