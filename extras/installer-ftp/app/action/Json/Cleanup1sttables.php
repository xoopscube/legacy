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
class Hdinstaller_Form_JsonCleanup1sttables extends Hdinstaller_Form_Json
{
	function __construct(&$c)
	{
		parent::__construct($c);
		
		$this->setDef(null, array(
			'dbhost' => array(
				'name' => _('DB Host'),
				'type' => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_TEXT,
				'required' => true,
				),
			'dbname' => array(
				'name' => _('DB name'),
				'type' => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_TEXT,
				'required' => true,
				),
			'dbuname' => array(
				'name' => _('DB Username'),
				'type' => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_PASSWORD,
				'required' => true,
				),
			'dbpass' => array(
				'name' => _('DB Password'),
				'type' => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_TEXT,
				'required' => true,
				),
			'prefix' => array(
				'name' => _('Database table prefix'),
				'type' => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_RADIO,
				'required' => true,
				),
			));
		
	}
}

/**
 *  Index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Hdinstaller
 */
class Hdinstaller_Action_JsonCleanup1sttables extends Hdinstaller_Action_Json
{
    /**
     *  preprocess Index action.
     *
     *  @access    public
     *  @return    string  Forward name (null if no errors.)
     */
    function prepare()
    {
		if ($this->af->validate() > 0){
			return 'json_error_repeatxoops1ststep';
		}
    }

    /**
     *  Index action implementation.
     *
     *  @access    public
     *  @return    string  Forward Name.
     */
    function perform()
    {
		/// clean up all table
		$dbhost  = $this->af->get('dbhost');
		$dbname  = $this->af->get('dbname');
		$dbuname = $this->af->get('dbuname');
		$dbpass  = $this->af->get('dbpass');
		$prefix  = $this->af->get('prefix');

		/// remove all tables prefixed by $prefix
		if (mysql_connect($dbhost, $dbuname, $dbpass)){
			if (mysql_select_db($dbname)){
				if ($ret = mysql_query("SHOW TABLES LIKE '${prefix}%'")){
					while ($re = mysql_fetch_assoc($ret)){
						$sql = sprintf('DROP TABLE %s', current($re));
						mysql_query($sql);
					}
				}
			}
		}
		
        return 'json_cleanup1sttables';
    }
}
