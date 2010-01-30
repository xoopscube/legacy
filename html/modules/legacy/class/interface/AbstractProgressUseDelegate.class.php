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

/**
 * Interface of progress use delegate
**/
abstract class Legacy_AbstractProgressUseDelegate
{
    /**
     * getModuleUsingProgress
     *
     * @param string $dirname
     * @param string $target
     * @param int    $id
     *
     * @return  void
     */ 
    abstract public function getTargetUsingProgress(/*** array ***/ &$list);

	/**
	 * getOriginalUrl
	 *
	 * @param string &$url
	 * @param string $dirname
	 * @param string $target_name
	 * @param string $target_id
	 *
     * @return  void
	 */	
	abstract public function getOriginalUrl(/*** string ***/ &$url, /*** string ***/ $dirname, /*** string ***/ $target_name, /*** $id ***/ $target_id);

	/**
	 * updateStatus
	 *
	 * @param string &$result
	 * @param string $dirname
	 * @param string $target_name
	 * @param string $target_id
	 *
     * @return  void
	 */	
	abstract public function updateStatus(/*** string ***/ &$result, /*** string ***/ $dirname, /*** string ***/ $target_name, /*** $id ***/ $target_id, /*** int ***/ $status);

    /**
     * Create directory name list.
     * 
     * @param   void
     * 
     * @return  string{}[]
    **/
    protected function _createDirnameList()
    {
        $list = array();
        $cri = new Criteria('isactive',0,'>');
        $cri->addSort('weight','ASC');
        $cri->addSort('mid','ASC');
        foreach(xoops_gethandler('module')->getObjects($cri) as $module)
        {
            if($name = $module->getInfo('trust_dirname'))
            {
                $list[$name][] = $module->get('dirname');
            }
        }
        return $list;
    }

}

?>
