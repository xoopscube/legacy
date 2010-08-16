<?php
/**
 * @file
 * @package legacy
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit();
}

abstract class Legacy_AbstractImageObject extends XoopsSimpleObject
{
	const IMAGE_TAG = '<img src="%s" width="%d" height="%d" alt="%s" />';
	const SWF_TAG = '<object data="%s" type="application/x-shockwave-flash" width="%d" height="%d"><param name="movie" value="%s" /><param name=loop value=false>
</object>';

	protected $mDirArray = array();

	/**
	 * __construct
	 * 
	 * @param	void
	 * 
	 * @return	void
	**/
	public function __construct()
	{
		$this->initVar('image_id', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('title', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('uid', XOBJ_DTYPE_INT, '', false);
		$this->initVar('dirname', XOBJ_DTYPE_STRING, '', false);
		$this->initVar('dataname', XOBJ_DTYPE_STRING, '', false);
		$this->initVar('data_id', XOBJ_DTYPE_INT, '', false);
		$this->initVar('num', XOBJ_DTYPE_INT, 1, false);
		//body of file name
		$this->initVar('file_name', XOBJ_DTYPE_STRING, '', false, 60);
		//extension type of file name : Lenum_FileType
		$this->initVar('file_type', XOBJ_DTYPE_INT, '', false);
		$this->initVar('image_width', XOBJ_DTYPE_INT, '', false);
		$this->initVar('image_height', XOBJ_DTYPE_INT, '', false);
		$this->initVar('posttime', XOBJ_DTYPE_INT, time(), false);
	}

	/**
	 * getRandomFileName
	 * 
	 * @param	string	$prefix
	 * @param	bool	$salt = null
	 * @return string
	 */
	public function getRandomFileName($prefix,$salt=null)
	{
		if (! isset($salt)) {
			$root=&XCube_Root::getSingleton();
			$salt = $root->getSiteConfig('Cube', 'Salt');
		}
		srand(microtime() *1000000);
		$body = md5($salt . rand());
		return $prefix . $body;
	}

	/**
	 * Return file size.
	 * @return int
	 */
	public function getImageInfo($type, $tsize=0)
	{
		if(! file_exists($this->getFilePath($tsize))){
			return null;
		}
		$info = getimagesize($this->getFilePath($tsize));
	
		switch($type){
		case 'width':
		case '0':
			return $info[0];
			break;
		case 'height':
		case '1':
			return $info[1];
			break;
		case 'file_type':
		case '2':
			return $info[2];
			break;
		case 'attr':
		case '3':
			return $info[3];
			break;
		}
	}

	/**
	 * makeImageTag
	 * 
	 * @param int		$tsize
	 * @param string	$htmlId
	 * @param string	$htmlClass
	 *
	 * @return string
	 */
	abstract public function makeImageTag(/*** int ***/ $tsize=1, /*** string ***/ $htmlId=null, /*** string ***/ $htmlClass=null);

	/**
	 * getFilePath
	 * 
	 * @param int	$tsize
	 *
	 * @return string
	 */
	abstract public function getFilePath($tsize=0);

	/**
	 * getFileUrl
	 * 
	 * @param int	$tsize
	 *
	 * @return string
	 */
	abstract public function getFileUrl($tsize=0);
}

?>
