<?php

if( ! class_exists( 'GNaviTextSanitizer' ) ) {

include_once( XOOPS_ROOT_PATH . '/class/module.textsanitizer.php' ) ;

class GNaviTextSanitizer extends MyTextSanitizer {

	var $nbsp = 0 ;

	/*
	* Constructor of this class
    *
	* Gets allowed html tags from admin config settings
	* <br> should not be allowed since nl2br will be used
	* when storing data.
    *
    * @access	private
    *
    * @todo Sofar, this does nuttin' ;-)
	*/
	function GNaviTextSanitizer()
	{
		parent::MyTextSanitizer() ;
	}

	/**
	 * Access the only instance of this class
     *
     * @return	object
     *
     * @static
     * @staticvar   object
	 */
	function &getInstance()
	{
		static $instance;
		if (!isset($instance)) {
			$instance = new GNaviTextSanitizer();
		}
		return $instance;
	}

	/**
	 * Filters textarea form data in DB for display
	 *
	 * @param   string  $text
	 * @param   bool    $html   allow html?
	 * @param   bool    $smiley allow smileys?
	 * @param   bool    $xcode  allow xoopscode?
	 * @param   bool    $image  allow inline images?
	 * @param   bool    $br     convert linebreaks?
	 * @return  string
	 **/
	function displayTarea($text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1 , $nbsp = 0 )
	{
		$this->nbsp = $nbsp ;
		$text = parent::displayTarea( $text , $html , $smiley , $xcode , $image , $br ) ;
		return $this->postCodeDecode( $text , $image ) ;
/*		if ($html != 1) {
			// html not allowed
			$text =& $this->htmlSpecialChars($text);
		}
		$text =& $this->makeClickable($text);
		if ($smiley != 0) {
			// process smiley
			$text =& $this->smiley($text);
		}
		if ($xcode != 0) {
			// decode xcode
			if ($image != 0) {
				// image allowed
				$text =& $this->xoopsCodeDecode($text);
            		} else {
                		// image not allowed
                		$text =& $this->xoopsCodeDecode($text, 0);
			}
		}
		if ($br != 0) {
			$text =& $this->nl2Br($text);
		}
		return $text; */
	}


	/**
	 * Replace some appendix codes with their equivalent HTML formatting
	 *
	 * @param   string  $text
	 * @return  string
	 **/
	function postCodeDecode( $text , $image )
	{
		$removal_tags = array( '[summary]' , '[/summary]' , '[pagebreak]' ) ;
		$text = str_replace( $removal_tags , '' , $text ) ;

		$patterns = array();
		$replacements = array();

		$patterns[] = "/\[siteimg align=(['\"]?)(left|center|right)\\1]([^\"\(\)\?\&'<>]*)\[\/siteimg\]/sU";
		$patterns[] = "/\[siteimg]([^\"\(\)\?\&'<>]*)\[\/siteimg\]/sU";
		if( $image ) {
			$replacements[] = '<img src="'.XOOPS_URL.'/\\3" align="\\2" alt="" />';
	
			$replacements[] = '<img src="'.XOOPS_URL.'/\\1" alt="" />';
		} else {
			$replacements[] = '<a href"'.XOOPS_URL.'/\\3" target="_blank">'.XOOPS_URL.'/\\3</a>';
			$replacements[] = '<a href"'.XOOPS_URL.'/\\1" target="_blank">'.XOOPS_URL.'/\\1</a>';
		}

		return preg_replace($patterns, $replacements, $text);
	}


	/**
	 * get inside of tags [summary] and [/summary]
	 *
	 * @param   string  $text
	 * @return  string
	 **/
	function extractSummary( $text )
	{
		$patterns[] = "/^(.*)\[summary\](.*)\[\/summary\](.*)$/sU";
		$replacements[] = '$2';

		return preg_replace($patterns, $replacements, $text);
	}


	/**
	 * Convert linebreaks to <br /> tags
     *
     * @param	string  $text
     *
     * @return	string
	 */
	function &nl2Br( $text )
	{
		$text = preg_replace("/(\015\012)|(\015)|(\012)/","<br />",$text);
		if( $this->nbsp ) {
			$patterns = array( '  ' , '\"' ) ;
			$replaces = array( ' &nbsp;' , '"' ) ;
			$text = substr(preg_replace('/\>.*\</esU',"str_replace(\$patterns,\$replaces,'\\0')",">$text<"),1,-1);
		}
		return $text ;
	}


	/*
	* if magic_quotes_gpc is on, stirip back slashes
    *
    * @param	string  $text
    *
    * @return	string
	*/
	function stripSlashesGPC($text)
	{
		if (get_magic_quotes_gpc()) {
			$text = stripslashes($text);
		}

		if( function_exists( 'gnavi_callback_after_stripslashes_local' ) ) {
			$text = gnavi_callback_after_stripslashes_local( $text ) ;
		}

		return $text;
	}



// The End of Class
}

}

?>