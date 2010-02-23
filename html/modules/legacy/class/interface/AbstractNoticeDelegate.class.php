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
 * Interface of notice delegate
**/
abstract class Legacy_AbstractNoticeDelegate
{
    /**
     * getNoticeItems   Legacy_Notice.GetNoticeItems
     *
     * @param mix[] &$item
     *  $item['dirname']*
     *  $item['data_type']*
     *  $item['id']
     *  $item['uid']
     *  $item['title']*
     *  $item['pubdate']*
     *  $item['description']
     *  $item['category']
     *  $item['author']
     *  $item['link']
     * @param int $uid
     *
     * @return  void
     */ 
    abstract public function getNoticeItems(/*** mix[] ***/ &$item, /*** int ***/ $uid);

}

?>
