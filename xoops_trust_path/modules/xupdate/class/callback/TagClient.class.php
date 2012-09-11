<?php
/**
 * @package xupdate
 * @version $Id: TagClient.class.php,v 1.1 2007/05/15 02:35:07 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

/**
 * tag client delegate
**/
class Xupdate_TagClientDelegate implements Legacy_iTagClientDelegate
{
    /**
     * getClientList
     *
     * @param mixed[]   &$list
     *  @list[]['dirname']
     *  @list[]['dataname']
     * @param string    $tDirname   Legacy_Tag module's dirname
     *
     * @return  void
     */ 
    public static function getClientList(/*** mixed[] ***/ &$list, /*** string ***/ $tDirname)
    {
        //don't call this method multiple times when site owner duplicate.
        static $isCalled = false;
        if($isCalled === true){
            return;
        }
    
        //get dirname list of Xupdate
        $dirnames = Legacy_Utils::getDirnameListByTrustDirname(basename(dirname(dirname(dirname(__FILE__)))));
    
        foreach($dirnames as $dir){
            //setup client module info
            if(Xupdate_Utils::getModuleConfig($dir, 'tag_dirname')==$tDirname){
                $list[] = array('dirname'=>$dir, 'dataname'=>'ModuleStore');
                $list[] = array('dirname'=>$dir, 'dataname'=>'ThemeStore');
                $list[] = array('dirname'=>$dir, 'dataname'=>'PreloadStore');

            }
        }
    
        $isCalled = true;
    }

    /**
     * getClientData
     *
     * @param mixed     &$list
     * @param string    $dirname
     * @param string    $dataname
     * @param int[]     $idList
     *
     * @return  void
     */ 
    public static function getClientData(/*** mixed ***/ &$list, /*** string ***/ $dirname, /*** string ***/ $dataname, /*** int[] ***/ $idList)
    {
        //default
        $limit = 30;
        $start =0;
    
        $handler = Legacy_Utils::getModuleHandler($dataname, $dirname);
        if(! $handler){
            return;
        }
        $contents = strtolower(str_replace('Store', '', $dataname));
        
        //setup client module info
        $cri = new CriteriaCompo();
        $cri->add(new Criteria('contents', $contents));
        $cri->add(new Criteria($handler->mPrimary, $idList, 'IN'));
        $objs = $handler->getObjects($cri, $limit, $start);
        if(count($objs)>0){
	        $list['dirname'][] = $dirname;
	        $list['dataname'][] = $dataname;
	        $list['data'][] = $objs;
	        $handler = xoops_gethandler('module');
	        $module = $handler->getByDirname($dirname);
	        $list['title'][] = $module->name() . ' - ' . ucfirst($contents);
	        $list['template_name'][] = 'db:'.$dirname .'_modulestore_inc.html';
	    }
    }
}

?>
