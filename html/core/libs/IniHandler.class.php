<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_IniHandler.class.php,v 1.0
 * @copyright Copyright 2005-2010 XOOPS Cube Project  <http://xoopscube.sourceforge.net/>
 * @license http://xoopscube.sourceforge.net/license/bsd_licenses.txt Modified BSD license
 *
 */



/*
If the first character in a line is #, ; or //, the line is treated as comment.
*/

class XCube_IniHandler
{
	protected /*** string[] ***/	$_mConfig = array();
	protected /*** string ***/	$_mFilePath = null;
	protected /*** bool ***/	$_mSectionFlag = false;

	/**
	 * __constract
	 * 
	 * @param	string	$filePath
	 * @param	bool	$section
	 * 
	 * @return	void
	**/
	public function __construct(/*** string ***/ $filePath, /*** bool ***/ $section=false)
	{
		$this->_mSectionFlag = $section;
		$this->_mFilePath = $filePath;
		$this->_loadIni();
	}

	/**
	 * _loadIni
	 * 
	 * @param	void
	 * 
	 * @return	void
	**/
	protected function _loadIni()
	{
		if(file_exists($this->_mFilePath)){
			$key = null;
			$file = fopen($this->_mFilePath, 'r');
			for($lineNum=1; $line=fgets($file);$lineNum++){
				//remove CR
				$line = preg_replace('/\r/', '', $line);
			
				//case: comment line
				if(substr($line,1,1)==';'||substr($line,1,1)=='#'||substr($line,1,2)=='//'){
					continue;
				}
				//case: section line
				elseif(preg_match('/\[(.*)\]/', $line, $str)){
					if($this->_mSectionFlag===true){
						$key = $str[1];
						$this->_mConfig[$key] = array();
					}
				}
				//case: key/value line
				elseif(preg_match('/^(.*)=(.*)$/', $line, $str)){
					if(preg_match('/^"(.*)"$/', $str[2], $body)||preg_match('/^\'(.*)\'$/', $str[2], $body)){
						$str[2] = $body[1];
					}
				
					if($this->_mSectionFlag===true){
						$this->_mConfig[$key][$str[1]] = $str[2];
					}
					else{
						$this->_mConfig[$str[1]] = $str[2];
					}
				}
			}
		}
	}

	/**
	 * getConfig
	 * 
	 * @param	string	$key
	 * @param	string	$section
	 * 
	 * @return	string
	**/
	public function getConfig(/*** string ***/ $key, /*** string ***/ $section='')
	{
		if($this->_mSectionFlag===true){
			return $this->_mConfig[$section][$key];
		}
		else{
			return $this->_mConfig[$key];
		}
	}

	/**
	 * getSectionConfig
	 * 
	 * @param	string	$section
	 * 
	 * @return	string[]
	**/
	public function getSectionConfig(/*** string ***/ $section)
	{
		return ($this->_mSectionFlag===true) ? $this->_mConfig[$section] : null;
	}

	/**
	 * getAllConfig
	 * 
	 * @param	void
	 * 
	 * @return	string[]
	**/
	public function getAllConfig()
	{
		return $this->_mConfig;
	}

}
?>
